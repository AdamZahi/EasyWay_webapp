<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412203915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conducteur ADD nb_trajet_effectues INT NOT NULL, ADD nb_passagers_transportes INT NOT NULL, DROP numero_permis, DROP experience, CHANGE telephonne telephonne INT NOT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT 'default_profile.png' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE passager CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT 'default_profile.png' NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conducteur ADD numero_permis VARCHAR(255) NOT NULL, ADD experience VARCHAR(255) NOT NULL, DROP nb_trajet_effectues, DROP nb_passagers_transportes, CHANGE telephonne telephonne VARCHAR(255) NOT NULL, CHANGE photo_profil photo_profil VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE passager CHANGE photo_profil photo_profil VARCHAR(255) NOT NULL
        SQL);
    }
}
