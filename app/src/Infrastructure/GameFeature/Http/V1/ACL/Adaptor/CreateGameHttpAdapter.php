<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\ACL\Adaptor;

use App\Application\GameFeature\Process\CreateGameProcess;
use App\Domain\GameFeature\Model\GameInterface;
use App\Infrastructure\Common\Http\V1\Constraint\CollectionConstraintMessage;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\GameFeature\Http\V1\Enum\GameResourceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Адаптер HTTP запроса перед созданием игры.
 */
readonly class CreateGameHttpAdapter
{
    /**
     * Создаст адаптер.
     *
     * @param ValidatorInterface $validator         Служба валидации.
     * @param CreateGameProcess  $createGameProcess Процесс создания игры.
     */
    public function __construct(
        private ValidatorInterface $validator,
        private CreateGameProcess $createGameProcess,
    ) {
    }

    /**
     * Валидирует HTTP запрос перед созданием игры.
     *
     * @param Request $request HTTP запрос.
     *
     * @return GameInterface
     *
     * @throws ValidatorException
     */
    public function execute(Request $request): GameInterface
    {
        $body = \json_decode($request->getContent(), true) ?? [];

        $violations = $this->validator->validate($body, $this->getConstraint());
        if ($violations->count() > 0) {
            throw new ValidatorException($violations);
        }

        return $this->createGameProcess->execute(
            secretId: $body['data']['attributes']['secretId'],
            ip: (string) $request->getClientIp(),
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
                                choices: [GameResourceType::GAMES->value],
                                message: 'Недопустимый тип ресурса data.type.',
                            ),
                        ],
                        'attributes' => new Constraints\Collection(
                            fields: [
                                'secretId' => [
                                    new Constraints\NotBlank(message: 'Укажите идентификатор загаданного слова attributes.secretId.'),
                                    new Constraints\Type(
                                        type: 'string',
                                        message: 'Идентификатор загаданного слова должен быть строкой.',
                                    ),
                                    new Constraints\Length(
                                        max: 32,
                                        maxMessage: 'Идентификатор загаданного слова должен быть не длиннее {{ limit }} символов.',
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
                ),
            ],
            allowExtraFields: false,
            allowMissingFields: false,
            extraFieldsMessage: CollectionConstraintMessage::EXTRA,
            missingFieldsMessage: CollectionConstraintMessage::MISSING,
        );
    }
}
