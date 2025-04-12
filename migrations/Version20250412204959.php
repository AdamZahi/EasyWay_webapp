<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412204959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP date_creation_compte, DROP role, DROP is_verified, CHANGE telephonne telephonne INT NOT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT 'default_profile.png' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_identifier_email ON user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD date_creation_compte DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD role VARCHAR(255) NOT NULL, ADD is_verified TINYINT(1) NOT NULL, CHANGE telephonne telephonne VARCHAR(255) NOT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_8d93d649e7927c74 ON user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)
        SQL);
    }
}
