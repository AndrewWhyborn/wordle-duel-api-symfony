<?php

declare(strict_types=1);

namespace App\Infrastructure\GameFeature\Http\V1\ACL\Adaptor;

use App\Application\GameFeature\Process\CreateGameProcess;
use App\Domain\GameFeature\Model\GameInterface;
use App\Infrastructure\Common\Http\V1\Constraint\JsonApiConstraintFactory;
use App\Infrastructure\Common\Http\V1\Exception\ValidatorException;
use App\Infrastructure\Common\JsonEncoding\JsonDecoder;
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
     * @throws \JsonException
     */
    public function execute(Request $request): GameInterface
    {
        $body = JsonDecoder::decode($request->getContent());

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
        return JsonApiConstraintFactory::resourceDocument(
            type: GameResourceType::GAMES->value,
            attributes: JsonApiConstraintFactory::object([
                'secretId' => JsonApiConstraintFactory::secretId('attributes.secretId'),
            ]),
        );
    }
}
