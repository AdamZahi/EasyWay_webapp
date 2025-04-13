<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412225628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation ADD user VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reponse CHANGE reclamation_id reclamation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse RENAME INDEX fk_reponse_reclamation TO IDX_5FB6DEC72D6BA2D9');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP user');
        $this->addSql('ALTER TABLE reponse CHANGE reclamation_id reclamation_id INT NOT NULL');
        $this->addSql('ALTER TABLE reponse RENAME INDEX idx_5fb6dec72d6ba2d9 TO FK_REPONSE_RECLAMATION');
    }
}
