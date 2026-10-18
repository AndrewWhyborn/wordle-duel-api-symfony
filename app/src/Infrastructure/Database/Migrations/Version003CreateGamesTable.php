<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Создание таблицы игр.
 *
 * PostgreSQL 18+: DEFAULT uuidv7(); иначе gen_random_uuid() (UUIDv4).
 */
final class Version003CreateGamesTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы games';
    }

    public function up(Schema $schema): void
    {
        $uuidDefault = $this->uuidDefaultExpression();

        $this->addSql(\sprintf(
            'CREATE TABLE games (id UUID DEFAULT %s NOT NULL, secret_id VARCHAR(32) NOT NULL, ip VARCHAR(45) NOT NULL, status VARCHAR(32) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))',
            $uuidDefault,
        ));
        $this->addSql('CREATE INDEX idx_games_secret_id ON games (secret_id)');
        $this->addSql('CREATE INDEX idx_games_ip ON games (ip)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE games');
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
