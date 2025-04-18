<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412214838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE evenement (id_evenement INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, PRIMARY KEY(id_evenement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1A9862E3
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9F39F8B1A9862E3 ON station
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station ADD id_ligne_id INT DEFAULT NULL, DROP id_ligne
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1A9862E3 FOREIGN KEY (id_ligne_id) REFERENCES ligne (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9F39F8B1A9862E3 ON station (id_ligne_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE evenement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1A9862E3
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_9F39F8B1A9862E3 ON station
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station ADD id_ligne INT NOT NULL, DROP id_ligne_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1A9862E3 FOREIGN KEY (id_ligne) REFERENCES ligne (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9F39F8B1A9862E3 ON station (id_ligne)
        SQL);
    }
}
