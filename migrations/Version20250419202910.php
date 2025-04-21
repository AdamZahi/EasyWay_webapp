<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419202910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire (id_com INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_post INT NOT NULL, contenu VARCHAR(255) NOT NULL, date_creat DATE NOT NULL, INDEX IDX_67F068BC6B3CA4B (id_user), INDEX IDX_67F068BCD1AA708F (id_post), PRIMARY KEY(id_com)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne (id INT AUTO_INCREMENT NOT NULL, depart VARCHAR(255) NOT NULL, arret VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, admin_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, pay_id VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, res_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (payment_id INT AUTO_INCREMENT NOT NULL, transaction_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(payment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE posts (id_post INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, ville_depart VARCHAR(255) NOT NULL, ville_arrivee VARCHAR(255) NOT NULL, date DATE NOT NULL, message VARCHAR(255) NOT NULL, nombre_de_places INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_885DBAFA6B3CA4B (id_user), PRIMARY KEY(id_post)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, depart VARCHAR(255) NOT NULL, arret VARCHAR(255) NOT NULL, vehicule VARCHAR(255) NOT NULL, nb INT NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE station (id INT AUTO_INCREMENT NOT NULL, id_ligne INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, id_admin INT NOT NULL, INDEX IDX_9F39F8B1B9759AB3 (id_ligne), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCD1AA708F FOREIGN KEY (id_post) REFERENCES posts (id_post)');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1B9759AB3 FOREIGN KEY (id_ligne) REFERENCES ligne (id)');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D766B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE reclamation CHANGE id_user id_user INT NOT NULL, CHANGE date_creation date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement (id INT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC6B3CA4B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCD1AA708F');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA6B3CA4B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1B9759AB3');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE ligne');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE posts');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE station');
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D766B3CA4B');
        $this->addSql('ALTER TABLE reclamation CHANGE id_user id_user INT DEFAULT NULL, CHANGE date_creation date_creation DATETIME NOT NULL');
    }
}
