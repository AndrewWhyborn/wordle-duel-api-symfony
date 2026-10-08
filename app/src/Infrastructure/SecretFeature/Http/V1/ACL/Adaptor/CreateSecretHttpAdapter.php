<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Http\V1\ACL\Adaptor;

use App\Application\SecretFeature\Process\CreateSecretProcess;
use App\Domain\SecretFeature\Model\SecretInterface;
use App\Infrastructure\Common\Http\V1\Constraint\CollectionConstraintMessage;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\DictionaryFeature\Constraint\WordExists;
use App\Infrastructure\SecretFeature\Http\V1\Enum\SecretResourceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints;

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
     * @throws \Exception
     */
    public function execute(Request $request): SecretInterface
    {
        $body  = \json_decode($request->getContent(), true) ?? [];

        $violations = $this->validator->validate($body, $this->getConstraint());
        if ($violations->count() > 0) {
            throw new ValidatorException($violations);
        }

        return $this->createSecretProcess->execute(
            value: $body['data']['attributes']['value'],
            isRealWord: $body['data']['attributes']['isRealWord'] ?? true,
        );
    }

    /**
     * Ограничения Symfony Validator для тела запроса JSON:API.
     */
    private function getConstraint(): Constraints\Collection
    {
        return new Constraints\Collection(
            fields: [
                'data' => new Constraints\Collection(
                    fields: [
                        'type' => [
                            new Constraints\NotBlank(message: 'Укажите тип ресурса data.type.'),
                            new Constraints\Choice(
                                choices: [SecretResourceType::SECRETS->value],
                                message: 'Недопустимый тип ресурса data.type.',
                            ),
                        ],
                        'attributes' => [
                            new Constraints\Collection(
                                fields: [
                                    'value' => [
                                        new Constraints\NotBlank(message: 'Укажите значение загаданного слова attributes.value.'),
                                        new Constraints\Type(
                                            type: 'string',
                                            message: 'Загаданное слово должно быть строкой.',
                                        ),
                                        new Constraints\Length(
                                            exactly: 5,
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
                                ],
                                allowExtraFields: false,
                                allowMissingFields: false,
                                extraFieldsMessage: CollectionConstraintMessage::EXTRA,
                                missingFieldsMessage: CollectionConstraintMessage::MISSING,
                            ),
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
                    ],
                    allowExtraFields: false,
                    allowMissingFields: false,
                    extraFieldsMessage: CollectionConstraintMessage::EXTRA,
                    missingFieldsMessage: CollectionConstraintMessage::MISSING,
                ),
            ],
            allowExtraFields: false,
            allowMissingFields: false,
            extraFieldsMessage: CollectionConstraintMessage::EXTRA,
            missingFieldsMessage: CollectionConstraintMessage::MISSING,
        );
    }
}
