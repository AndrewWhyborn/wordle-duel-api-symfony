<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Http\V1\ACL\Adaptor;

use App\Application\SecretFeature\Process\CreateSecretProcess;
use App\Domain\SecretFeature\Model\SecretInterface;
use App\Domain\WordleFeature\WordleRules;
use App\Infrastructure\Common\Http\V1\Constraint\JsonApiConstraintFactory;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\Common\JsonEncoding\JsonDecoder;
use App\Infrastructure\DictionaryFeature\Constraint\WordExists;
use App\Infrastructure\SecretFeature\Http\V1\Enum\SecretResourceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Адаптер HTTP запроса перед сохранением загаданного слова.
 */
readonly class CreateSecretHttpAdapter
{
    /**
     * Создаст адаптер.
     *
     * @param ValidatorInterface  $validator           Служба валидации.
     * @param CreateSecretProcess $createSecretProcess Процесс сохранения загаданного слова.
     */
    public function __construct(
        private ValidatorInterface $validator,
        private CreateSecretProcess $createSecretProcess,
    ) {
    }

    /**
     * Валидирует HTTP запрос перед сохранением загаданного слова.
     *
     * @param Request $request HTTP запрос.
     *
     * @return SecretInterface
     *
     * @throws ValidatorException
     * @throws \JsonException
     * @throws \Exception
     */
    public function execute(Request $request): SecretInterface
    {
        $body = JsonDecoder::decode($request->getContent());

        $violations = $this->validator->validate($body, $this->getConstraint());
        if ($violations->count() > 0) {
            throw new ValidatorException($violations);
        }

        return $this->createSecretProcess->execute(
            value: \mb_strtoupper($body['data']['attributes']['value']),
            isRealWord: $body['data']['attributes']['isRealWord'] ?? true,
            allowRetry: $body['data']['attributes']['allowRetry'] ?? true,
        );
    }

    /**
     * Ограничения Symfony Validator для тела запроса JSON:API.
     */
    private function getConstraint(): Constraints\Collection
    {
        return JsonApiConstraintFactory::resourceDocument(
            type: SecretResourceType::SECRETS->value,
            attributes: [
                JsonApiConstraintFactory::object([
                    'value' => [
                        new Constraints\NotBlank(message: 'Укажите значение загаданного слова attributes.value.'),
                        new Constraints\Type(
                            type: 'string',
                            message: 'Загаданное слово должно быть строкой.',
                        ),
                        new Constraints\Length(
                            exactly: WordleRules::WORD_LENGTH,
                            exactMessage: 'Загаданное слово должно быть длиной {{ limit }} символов.',
                        ),
                    ],
                    'allowRetry' => new Constraints\Optional([
                        new Constraints\Type(
                            type: 'boolean',
                            message: 'Флаг «разрешить повторы» должен иметь логический тип.',
                        ),
                    ]),
                    'isRealWord' => new Constraints\Optional([
                        new Constraints\Type(
                            type: 'boolean',
                            message: 'Флаг "настоящее слово" должен быть логическим значением.',
                        ),
                    ]),
                ]),
                new Constraints\When(
                    expression: '(value["isRealWord"] ?? true) === true',
                    constraints: [
                        new Constraints\Collection(
                            fields: [
                                'value' => [new WordExists()],
                            ],
                            allowExtraFields: true,
                            allowMissingFields: true,
                        ),
                    ],
                ),
            ],
        );
    }
}
