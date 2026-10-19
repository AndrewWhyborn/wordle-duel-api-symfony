<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\ACL\Adaptor;

use App\Application\GameFeature\DTO\GameWithGuesses;
use App\Application\GameFeature\Service\GetGameService;
use App\Domain\GameFeature\Exception\GameNotFoundException;
use App\Infrastructure\Common\Http\V1\Constraint\JsonApiConstraintFactory;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Адаптер HTTP-запроса перед получением игры.
 */
readonly class GetGameHttpAdapter
{
    /**
     * Создаст адаптер.
     *
     * @param ValidatorInterface $validator      Служба валидации.
     * @param GetGameService     $getGameService Служба получения игры.
     */
    public function __construct(
        private ValidatorInterface $validator,
        private GetGameService $getGameService,
    ) {
    }

    /**
     * Валидирует query-параметры и вернёт игру с догадками.
     *
     * @param Request $request HTTP-запрос.
     *
     * @return GameWithGuesses
     *
     * @throws ValidatorException
     * @throws GameNotFoundException
     */
    public function execute(Request $request): GameWithGuesses
    {
        $query = [
            'filter' => $request->query->all('filter'),
        ];

        $violations = $this->validator->validate($query, $this->getConstraint());
        if ($violations->count() > 0) {
            throw new ValidatorException($violations);
        }

        return $this->getGameService->execute(
            secretId: (string) $query['filter']['secretId'],
            ip: (string) $request->getClientIp(),
        );
    }

    /**
     * Ограничения Symfony Validator для query-параметров JSON:API filter.
     */
    private function getConstraint(): Constraints\Collection
    {
        return JsonApiConstraintFactory::object([
            'filter' => [
                new Constraints\NotBlank(message: 'Укажите фильтр filter.'),
                new Constraints\Type(
                    type: 'array',
                    message: 'Фильтр filter должен быть объектом.',
                ),
                JsonApiConstraintFactory::object([
                    'secretId' => JsonApiConstraintFactory::secretId('filter[secretId]'),
                ]),
            ],
        ]);
    }
}
