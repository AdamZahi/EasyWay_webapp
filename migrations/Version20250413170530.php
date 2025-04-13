<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413170530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE evenement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conducteur ADD CONSTRAINT FK_236771436B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE passager ADD CONSTRAINT FK_BFF42EE96B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_user_id
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_user_id ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_user_id FOREIGN KEY (user_id) REFERENCES user (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1A9862E3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station CHANGE id_ligne id_ligne INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_9f39f8b1a9862e3 ON station
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9F39F8B1B9759AB3 ON station (id_ligne)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1A9862E3 FOREIGN KEY (id_ligne) REFERENCES ligne (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE evenement (id_evenement INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, PRIMARY KEY(id_evenement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conducteur DROP FOREIGN KEY FK_236771436B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE passager DROP FOREIGN KEY FK_BFF42EE96B3CA4B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_42c84955a76ed395 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_user_id ON reservation (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id_user)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1B9759AB3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station CHANGE id_ligne id_ligne INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_9f39f8b1b9759ab3 ON station
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9F39F8B1A9862E3 ON station (id_ligne)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1B9759AB3 FOREIGN KEY (id_ligne) REFERENCES ligne (id)
        SQL);
    }
}
