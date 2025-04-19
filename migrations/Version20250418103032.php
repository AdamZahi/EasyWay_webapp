<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418103032 extends AbstractMigration
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
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at VARCHAR(255) NOT NULL, available_at VARCHAR(255) NOT NULL, delivered_at VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, pay_id VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, res_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, reclamation_id INT DEFAULT NULL, contenu LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_5FB6DEC72D6BA2D9 (reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, depart VARCHAR(255) NOT NULL, arret VARCHAR(255) NOT NULL, vehicule VARCHAR(255) NOT NULL, nb INT NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCD1AA708F FOREIGN KEY (id_post) REFERENCES posts (id_post)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC72D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE bus DROP FOREIGN KEY bus_ibfk_1');
        $this->addSql('ALTER TABLE metro DROP FOREIGN KEY metro_ibfk_1');
        $this->addSql('ALTER TABLE train DROP FOREIGN KEY train_ibfk_1');
        $this->addSql('DROP TABLE bus');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE metro');
        $this->addSql('DROP TABLE train');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('ALTER TABLE `admin` CHANGE id_admin id_admin INT AUTO_INCREMENT NOT NULL, CHANGE telephonne telephonne INT NOT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT NULL, ADD PRIMARY KEY (id_admin)');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D766B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D766B3CA4B ON `admin` (id_user)');
        $this->addSql('ALTER TABLE categorie CHANGE id id INT NOT NULL, CHANGE type type VARCHAR(250) NOT NULL');
        $this->addSql('ALTER TABLE conducteur DROP INDEX id_user, ADD UNIQUE INDEX UNIQ_236771436B3CA4B (id_user)');
        $this->addSql('ALTER TABLE conducteur DROP FOREIGN KEY conducteur_ibfk_1');
        $this->addSql('ALTER TABLE conducteur ADD nb_trajet_effectues INT NOT NULL, ADD nb_passagers_transportes INT NOT NULL, DROP numero_permis, DROP experience, CHANGE telephonne telephonne VARCHAR(255) NOT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT \'default_profile.png\' NOT NULL');
        $this->addSql('ALTER TABLE conducteur ADD CONSTRAINT FK_236771436B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE ligne ADD admin_id INT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE depart depart VARCHAR(255) NOT NULL, CHANGE arret arret VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE passager DROP INDEX id_user, ADD UNIQUE INDEX UNIQ_BFF42EE96B3CA4B (id_user)');
        $this->addSql('ALTER TABLE passager ADD nb_trajet_effectues INT NOT NULL, DROP nbTrajetsEffectues, DROP role, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT \'default_profile.png\' NOT NULL');
        $this->addSql('ALTER TABLE passager ADD CONSTRAINT FK_BFF42EE96B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE payment MODIFY  paymentId INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON payment');
        $this->addSql('ALTER TABLE payment ADD transaction_id INT NOT NULL, DROP transactionId, CHANGE  paymentId payment_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE payment ADD PRIMARY KEY (payment_id)');
        $this->addSql('ALTER TABLE posts CHANGE nombreDePlaces nombre_de_places INT NOT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE posts RENAME INDEX id_user TO IDX_885DBAFA6B3CA4B');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY reclamation_ibfk_1');
        $this->addSql('DROP INDEX fk_user_rec ON reclamation');
        $this->addSql('DROP INDEX fk_cat_rec ON reclamation');
        $this->addSql('ALTER TABLE reclamation CHANGE date_creation date_creation DATETIME NOT NULL, CHANGE user_id category_id_id INT DEFAULT NULL, CHANGE categorieId id_user INT NOT NULL, CHANGE statu statut VARCHAR(250) NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064049777D11E FOREIGN KEY (category_id_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064046B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('CREATE INDEX IDX_CE6064049777D11E ON reclamation (category_id_id)');
        $this->addSql('CREATE INDEX IDX_CE6064046B3CA4B ON reclamation (id_user)');
        $this->addSql('ALTER TABLE station CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE localisation localisation VARCHAR(255) NOT NULL, CHANGE id_ligne id_ligne INT DEFAULT NULL, CHANGE id_trajet id_admin INT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1B9759AB3 FOREIGN KEY (id_ligne) REFERENCES ligne (id)');
        $this->addSql('CREATE INDEX IDX_9F39F8B1B9759AB3 ON station (id_ligne)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL, DROP mot_de_passe, DROP date_creation_compte, DROP role, CHANGE email email VARCHAR(180) NOT NULL, CHANGE telephonne telephonne VARCHAR(255) NOT NULL, CHANGE photo_profil photo_profil VARCHAR(255) DEFAULT \'default_profile.png\' NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bus (id INT NOT NULL, nombrePortes INT DEFAULT NULL, typeService VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, nombreDePlaces INT DEFAULT NULL, compagnie VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, climatisation TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evenement (id_evenement INT NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME DEFAULT CURRENT_TIMESTAMP, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, ligne_affectee INT NOT NULL, statut VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'EN_COUR\' COLLATE `utf8mb4_general_ci`, id_createur INT NOT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE metro (id INT NOT NULL, longueurReseau DOUBLE PRECISION DEFAULT NULL, nombreLignes INT DEFAULT NULL, nombreRames INT DEFAULT NULL, proprietaire VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE train (id INT NOT NULL, longueurReseau DOUBLE PRECISION DEFAULT NULL, nombreLignes INT DEFAULT NULL, nombreWagons INT DEFAULT NULL, vitesseMaximale DOUBLE PRECISION DEFAULT NULL, proprietaire VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE trajet (id INT NOT NULL, duree TIME NOT NULL, distance INT NOT NULL, heure_depart TIME NOT NULL, heure_arrive TIME NOT NULL, depart VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, arret VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, etat VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, immatriculation VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, capacite INT DEFAULT NULL, etat VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, typeVehicule VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, id_conducteur INT DEFAULT NULL, idTrajet INT DEFAULT NULL, UNIQUE INDEX unique_immatriculation (immatriculation), INDEX fk_vehicule_trajet (idTrajet), INDEX fk_conducteur (id_conducteur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE bus ADD CONSTRAINT bus_ibfk_1 FOREIGN KEY (id) REFERENCES vehicule (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE metro ADD CONSTRAINT metro_ibfk_1 FOREIGN KEY (id) REFERENCES vehicule (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE train ADD CONSTRAINT train_ibfk_1 FOREIGN KEY (id) REFERENCES vehicule (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC6B3CA4B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCD1AA708F');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC72D6BA2D9');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('ALTER TABLE `admin` MODIFY id_admin INT NOT NULL');
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D766B3CA4B');
        $this->addSql('DROP INDEX UNIQ_880E0D766B3CA4B ON `admin`');
        $this->addSql('DROP INDEX `primary` ON `admin`');
        $this->addSql('ALTER TABLE `admin` CHANGE id_admin id_admin INT NOT NULL, CHANGE telephonne telephonne VARCHAR(255) NOT NULL, CHANGE photo_profil photo_profil LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE categorie CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE conducteur DROP INDEX UNIQ_236771436B3CA4B, ADD INDEX id_user (id_user)');
        $this->addSql('ALTER TABLE conducteur DROP FOREIGN KEY FK_236771436B3CA4B');
        $this->addSql('ALTER TABLE conducteur ADD numero_permis VARCHAR(255) NOT NULL, ADD experience VARCHAR(255) NOT NULL, DROP nb_trajet_effectues, DROP nb_passagers_transportes, CHANGE telephonne telephonne VARCHAR(14) NOT NULL, CHANGE photo_profil photo_profil LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE conducteur ADD CONSTRAINT conducteur_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON ligne');
        $this->addSql('ALTER TABLE ligne DROP admin_id, CHANGE id id INT NOT NULL, CHANGE depart depart VARCHAR(150) NOT NULL, CHANGE arret arret VARCHAR(150) NOT NULL, CHANGE type type VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE passager DROP INDEX UNIQ_BFF42EE96B3CA4B, ADD INDEX id_user (id_user)');
        $this->addSql('ALTER TABLE passager DROP FOREIGN KEY FK_BFF42EE96B3CA4B');
        $this->addSql('ALTER TABLE passager ADD nbTrajetsEffectues INT DEFAULT 0, ADD role VARCHAR(255) NOT NULL, DROP nb_trajet_effectues, CHANGE photo_profil photo_profil LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE payment MODIFY payment_id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON payment');
        $this->addSql('ALTER TABLE payment ADD transactionId VARCHAR(255) NOT NULL, DROP transaction_id, CHANGE payment_id  paymentId INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE payment ADD PRIMARY KEY ( paymentId)');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA6B3CA4B');
        $this->addSql('ALTER TABLE posts CHANGE nombre_de_places nombreDePlaces INT NOT NULL');
        $this->addSql('ALTER TABLE posts RENAME INDEX idx_885dbafa6b3ca4b TO id_user');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064049777D11E');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064046B3CA4B');
        $this->addSql('DROP INDEX IDX_CE6064049777D11E ON reclamation');
        $this->addSql('DROP INDEX IDX_CE6064046B3CA4B ON reclamation');
        $this->addSql('ALTER TABLE reclamation CHANGE date_creation date_creation VARCHAR(250) NOT NULL, CHANGE category_id_id user_id INT DEFAULT NULL, CHANGE id_user categorieId INT NOT NULL, CHANGE statut statu VARCHAR(250) NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT reclamation_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX fk_user_rec ON reclamation (user_id)');
        $this->addSql('CREATE INDEX fk_cat_rec ON reclamation (categorieId)');
        $this->addSql('ALTER TABLE station MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1B9759AB3');
        $this->addSql('DROP INDEX IDX_9F39F8B1B9759AB3 ON station');
        $this->addSql('DROP INDEX `primary` ON station');
        $this->addSql('ALTER TABLE station CHANGE id id INT NOT NULL, CHANGE id_ligne id_ligne INT NOT NULL, CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE localisation localisation VARCHAR(200) NOT NULL, CHANGE id_admin id_trajet INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD date_creation_compte DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD role VARCHAR(255) NOT NULL, DROP roles, CHANGE email email VARCHAR(255) NOT NULL, CHANGE telephonne telephonne INT NOT NULL, CHANGE photo_profil photo_profil LONGTEXT NOT NULL, CHANGE password mot_de_passe VARCHAR(255) NOT NULL');
    }
}
