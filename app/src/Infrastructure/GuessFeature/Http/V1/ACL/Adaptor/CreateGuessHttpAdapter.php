<?php

declare(strict_types=1);

namespace App\Infrastructure\GuessFeature\Http\V1\ACL\Adaptor;

use App\Application\GuessFeature\DTO\GuessWithLetters;
use App\Application\GuessFeature\Process\CreateGuessProcess;
use App\Domain\WordleFeature\WordleRules;
use App\Infrastructure\Common\Http\V1\Constraint\JsonApiConstraintFactory;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\Common\JsonEncoding\JsonDecoder;
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
     * @throws \JsonException
     */
    public function execute(Request $request): GuessWithLetters
    {
        $body = JsonDecoder::decode($request->getContent());

        $violations = $this->validator->validate($body, $this->getConstraint());
        if ($violations->count() > 0) {
            throw new ValidatorException($violations);
        }

        /** @var list<string> $letters */
        $letters = $body['data']['attributes']['letters'];

        return $this->createGuessProcess->execute(
            gameId: (string) $body['data']['attributes']['gameId'],
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
        return JsonApiConstraintFactory::resourceDocument(
            type: GuessResourceType::GUESSES->value,
            attributes: JsonApiConstraintFactory::object([
                'gameId' => [
                    new Constraints\NotBlank(message: 'Укажите идентификатор игры attributes.gameId.'),
                    new Constraints\Type(
                        type: 'string',
                        message: 'Идентификатор игры должен быть строкой.',
                    ),
                    new Constraints\Uuid(
                        message: 'Идентификатор игры должен быть корректным UUID.',
                    ),
                ],
                'letters' => [
                    new Constraints\NotBlank(message: 'Необходимо ввести слово.'),
                    new Constraints\Type(
                        type: 'array',
                        message: 'Слово должно быть передано в виде массива букв.',
                    ),
                    new Constraints\Count(
                        exactly: WordleRules::WORD_LENGTH,
                        exactMessage: \sprintf(
                            'Слово должно быть длиной в %d букв.',
                            WordleRules::WORD_LENGTH,
                        ),
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
                            message: 'Разрешено вводить только кириллические символы.',
                        ),
                    ]),
                ],
            ]),
        );
    }
}
