<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250407185949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix foreign keys and ensure proper column names for relationships';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');

        // 1. Drop existing constraints
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY IF EXISTS FK_880E0D766B3CA4B');
        $this->addSql('ALTER TABLE conducteur DROP FOREIGN KEY IF EXISTS FK_236771436B3CA4B');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY IF EXISTS FK_B26681E1ED9918B');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY IF EXISTS FK_B26681EAA033611');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY IF EXISTS FK_B1DC7A1E4670E604');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY IF EXISTS FK_B1DC7A1EA76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY IF EXISTS FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY IF EXISTS FK_9F39F8B1B9759AB3');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY IF EXISTS FK_9F39F8B1668B4C46');
        $this->addSql('ALTER TABLE train DROP FOREIGN KEY IF EXISTS FK_XXXXXXXX'); // Replace with actual key name if needed

        // 2. Add correct foreign keys using the proper column names
        $this->addSql('ALTER TABLE admin 
            ADD CONSTRAINT FK_880E0D766B3CA4B 
            FOREIGN KEY (id_user) REFERENCES user (id_user) 
            ON DELETE CASCADE');

        $this->addSql('ALTER TABLE conducteur 
            ADD CONSTRAINT FK_236771436B3CA4B 
            FOREIGN KEY (id_user) REFERENCES user (id_user) 
            ON DELETE CASCADE');

        $this->addSql('ALTER TABLE evenement 
            ADD CONSTRAINT FK_B26681E1ED9918B 
            FOREIGN KEY (ligne_affectee) REFERENCES ligne (id) 
            ON DELETE CASCADE');
            
        $this->addSql('ALTER TABLE evenement 
            ADD CONSTRAINT FK_B26681EAA033611 
            FOREIGN KEY (id_createur) REFERENCES admin (id_admin) 
            ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE paiement 
            ADD CONSTRAINT FK_B1DC7A1E4670E604 
            FOREIGN KEY (res_id) REFERENCES reservation (id) 
            ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE paiement 
            ADD CONSTRAINT FK_B1DC7A1EA76ED395 
            FOREIGN KEY (user_id) REFERENCES user (id_user) 
            ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE reservation 
            ADD CONSTRAINT FK_42C84955A76ED395 
            FOREIGN KEY (user_id) REFERENCES user (id_user) 
            ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE station 
            ADD CONSTRAINT FK_9F39F8B1B9759AB3 
            FOREIGN KEY (id_ligne) REFERENCES ligne (id) 
            ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE station 
            ADD CONSTRAINT FK_9F39F8B1668B4C46 
            FOREIGN KEY (id_admin) REFERENCES admin (id_admin) 
            ON DELETE CASCADE');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        // Drop the foreign keys in reverse order
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D766B3CA4B');
        $this->addSql('ALTER TABLE conducteur DROP FOREIGN KEY FK_236771436B3CA4B');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E1ED9918B');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681EAA033611');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E4670E604');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EA76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1B9759AB3');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1668B4C46');
        
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
