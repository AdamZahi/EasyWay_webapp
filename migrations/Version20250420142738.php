<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250420142738 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('payment')) {
            // Création initiale de la table
            $this->addSql('CREATE TABLE payment (
                payment_id INT AUTO_INCREMENT NOT NULL,
                transaction_id VARCHAR(255) NOT NULL,
                amount DOUBLE PRECISION NOT NULL,
                email VARCHAR(255) NOT NULL,
                PRIMARY KEY(payment_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        } else {
            // Modification si la table existe déjà
            $this->addSql('ALTER TABLE payment CHANGE transaction_id transaction_id VARCHAR(255) NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE payment');
    }
}