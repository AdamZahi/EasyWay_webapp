<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419170037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_comment (id INT AUTO_INCREMENT NOT NULL, event INT DEFAULT NULL, user INT DEFAULT NULL, comment VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1123FBC33BAE0AA7 (event), INDEX IDX_1123FBC38D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (payment_id INT AUTO_INCREMENT NOT NULL, transaction_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(payment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_comment ADD CONSTRAINT FK_1123FBC33BAE0AA7 FOREIGN KEY (event) REFERENCES evenement (id_evenement)');
        $this->addSql('ALTER TABLE event_comment ADD CONSTRAINT FK_1123FBC38D93D649 FOREIGN KEY (user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY evenement_ibfk_1');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY evenement_ibfk_2');
        $this->addSql('ALTER TABLE evenement DROP date_creation, CHANGE ligne_affectee ligne_affectee INT DEFAULT NULL, CHANGE id_createur id_createur INT DEFAULT NULL');
        $this->addSql('DROP INDEX ligne_affectee ON evenement');
        $this->addSql('CREATE INDEX IDX_B26681E1ED9918B ON evenement (ligne_affectee)');
        $this->addSql('DROP INDEX id_createur ON evenement');
        $this->addSql('CREATE INDEX IDX_B26681EAA033611 ON evenement (id_createur)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT evenement_ibfk_1 FOREIGN KEY (ligne_affectee) REFERENCES ligne (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT evenement_ibfk_2 FOREIGN KEY (id_createur) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE ligne DROP FOREIGN KEY ligne_ibfk_1');
        $this->addSql('DROP INDEX admin_id ON ligne');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT NOT NULL, CHANGE created_at created_at VARCHAR(255) NOT NULL, CHANGE available_at available_at VARCHAR(255) NOT NULL, CHANGE delivered_at delivered_at VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY paiement_ibfk_1');
        $this->addSql('DROP INDEX user_id ON paiement');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY fk_user_id');
        $this->addSql('ALTER TABLE reclamation CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064046B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY station_ibfk_1');
        $this->addSql('DROP INDEX id_admin ON station');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1B9759AB3 FOREIGN KEY (id_ligne) REFERENCES ligne (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_comment DROP FOREIGN KEY FK_1123FBC33BAE0AA7');
        $this->addSql('ALTER TABLE event_comment DROP FOREIGN KEY FK_1123FBC38D93D649');
        $this->addSql('DROP TABLE event_comment');
        $this->addSql('DROP TABLE payment');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E1ED9918B');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EAA033611');
        $this->addSql('ALTER TABLE evenement ADD date_creation DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE ligne_affectee ligne_affectee INT NOT NULL, CHANGE id_createur id_createur INT DEFAULT 10 NOT NULL');
        $this->addSql('DROP INDEX idx_b26681e1ed9918b ON evenement');
        $this->addSql('CREATE INDEX ligne_affectee ON evenement (ligne_affectee)');
        $this->addSql('DROP INDEX idx_b26681eaa033611 ON evenement');
        $this->addSql('CREATE INDEX id_createur ON evenement (id_createur)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E1ED9918B FOREIGN KEY (ligne_affectee) REFERENCES ligne (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EAA033611 FOREIGN KEY (id_createur) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE ligne ADD CONSTRAINT ligne_ibfk_1 FOREIGN KEY (admin_id) REFERENCES admin (id_admin)');
        $this->addSql('CREATE INDEX admin_id ON ligne (admin_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT paiement_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (id_user)');
        $this->addSql('CREATE INDEX user_id ON paiement (user_id)');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064046B3CA4B');
        $this->addSql('ALTER TABLE reclamation CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT fk_user_id FOREIGN KEY (id_user) REFERENCES user (id_user) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1B9759AB3');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT station_ibfk_1 FOREIGN KEY (id_admin) REFERENCES admin (id_admin)');
        $this->addSql('CREATE INDEX id_admin ON station (id_admin)');
    }
}
