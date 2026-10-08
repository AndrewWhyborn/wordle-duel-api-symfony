<?php

declare(strict_types=1);

namespace App\Infrastructure\SecretFeature\Factory;

use App\Domain\SecretFeature\Factory\SecretFactoryInterface;
use App\Domain\SecretFeature\Model\SecretInterface;
use App\Infrastructure\SecretFeature\Doctrine\Entity\Secret;

/**
 * @inheritDoc
 */
class SecretFactory implements SecretFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(string $id, string $value, bool $isRealWord, bool $allowRetry): SecretInterface
    {
        return new Secret($id, $value, $isRealWord, $allowRetry);
    }
}
