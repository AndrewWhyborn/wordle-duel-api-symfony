<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Http\V1\ACL\Adaptor;

use App\Application\GuessFeature\DTO\GuessWithLetters;
use App\Application\GuessFeature\Process\CreateGuessProcess;
use App\Infrastructure\Common\Http\V1\Constraint\CollectionConstraintMessage;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\GuessFeature\Http\V1\Enum\GuessResourceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Адаптер HTTP запроса перед созданием догадки.
 */
readonly class CreateGuessHttpAdapter
{
    /**
     * Создаст адаптер.
     *
     * @param ValidatorInterface $validator          Служба валидации.
     * @param CreateGuessProcess $createGuessProcess Процесс создания догадки.
     */
    public function __construct(
        private ValidatorInterface $validator,
        private CreateGuessProcess $createGuessProcess,
    ) {
    }

    /**
     * Валидирует HTTP запрос перед созданием догадки.
     *
     * @param Request $request HTTP запрос.
     *
     * @return GuessWithLetters
     *
     * @throws ValidatorException
     */
    public function execute(Request $request): GuessWithLetters
    {
        $body = \json_decode($request->getContent(), true) ?? [];

        $violations = $this->validator->validate($body, $this->getConstraint());
        if ($violations->count() > 0) {
            throw new ValidatorException($violations);
        }

        /** @var list<string> $letters */
        $letters = $body['data']['attributes']['letters'];

        return $this->createGuessProcess->execute(
            gameId: (int) $body['data']['attributes']['gameId'],
            letters: $letters,
        );
    }

    /**
     * Ограничения Symfony Validator для тела запроса JSON:API.
     *
     * Буквы передаются в attributes.letters (входные данные создания),
     * а не через relationships — у новых букв ещё нет id.
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
                                choices: [GuessResourceType::GUESSES->value],
                                message: 'Недопустимый тип ресурса data.type.',
                            ),
                        ],
                        'attributes' => new Constraints\Collection(
                            fields: [
                                'gameId' => [
                                    new Constraints\NotBlank(message: 'Укажите идентификатор игры attributes.gameId.'),
                                    new Constraints\Type(
                                        type: 'integer',
                                        message: 'Идентификатор игры должен быть целым числом.',
                                    ),
                                    new Constraints\Positive(
                                        message: 'Идентификатор игры должен быть положительным числом.',
                                    ),
                                ],
                                'letters' => [
                                    new Constraints\NotBlank(message: 'Необходимо ввести слово.'),
                                    new Constraints\Type(
                                        type: 'array',
                                        message: 'Слово должно быть передано в виде массива букв.',
                                    ),
                                    new Constraints\Count(
                                        exactly: 5,
                                        exactMessage: 'Слово должно быть длиной в 5 букв.',
                                    ),
                                    new Constraints\All([
                                        new Constraints\Type(
                                            type: 'string',
                                            message: 'Слово должно быть передано в виде массива букв.',
                                        ),
                                        new Constraints\Length(
                                            exactly: 1,
                                            exactMessage: 'Слово должно быть передано в виде массива букв.',
                                        ),
                                        new Constraints\Regex(
                                            pattern: '/^[А-Яа-яЁё]$/u',
                                            message: 'Разрешено вводить только русские буквы.',
                                        ),
                                    ]),
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
