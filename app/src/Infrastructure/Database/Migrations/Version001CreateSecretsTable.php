<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Создание таблицы загаданных слов.
 */
final class Version001CreateSecretsTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы secrets';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE secrets (id VARCHAR(32) NOT NULL, value VARCHAR(10) NOT NULL, is_real_word BOOLEAN NOT NULL, allow_retry BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE secrets');
    }
}
