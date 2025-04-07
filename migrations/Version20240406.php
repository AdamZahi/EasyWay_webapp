<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create evenement table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE evenement (
            id_evenement INT AUTO_INCREMENT NOT NULL,
            type VARCHAR(255) NOT NULL,
            status VARCHAR(255) NOT NULL,
            description LONGTEXT NOT NULL,
            date_debut DATETIME NOT NULL,
            date_fin DATETIME NOT NULL,
            PRIMARY KEY(id_evenement)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE evenement');
    }
} 