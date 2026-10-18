<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Создание таблицы букв.
 *
 * PostgreSQL 18+: DEFAULT uuidv7(); иначе gen_random_uuid() (UUIDv4).
 */
final class Version005CreateLettersTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create letters table';
    }

    public function up(Schema $schema): void
    {
        $uuidDefault = $this->uuidDefaultExpression();

        $this->addSql(\sprintf(
            'CREATE TABLE letters (id UUID DEFAULT %s NOT NULL, guess_id UUID NOT NULL, "index" INT NOT NULL, value VARCHAR(1) NOT NULL, status VARCHAR(32) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))',
            $uuidDefault,
        ));
        $this->addSql('CREATE INDEX idx_letters_guess_id ON letters (guess_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE letters');
    }

    /**
     * Выражение DEFAULT для UUID в зависимости от версии PostgreSQL.
     */
    private function uuidDefaultExpression(): string
    {
        $version = (int) $this->connection->fetchOne('SHOW server_version_num');

        return $version >= 180000 ? 'uuidv7()' : 'gen_random_uuid()';
    }
}
