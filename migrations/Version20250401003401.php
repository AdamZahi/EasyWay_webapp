<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250401070000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Final fix for foreign keys using correct column names';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        
        // 1. Drop existing constraints
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY IF EXISTS FK_POSTS_USER_NEW');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY IF EXISTS FK_67F068BCBF396750');
        
        // 2. Add correct foreign keys using the proper column names
        // For posts table (references user.id)
        $this->addSql('ALTER TABLE posts 
            ADD CONSTRAINT FK_POSTS_USER_CORRECT 
            FOREIGN KEY (id_user) REFERENCES user (id) 
            ON DELETE CASCADE');
            
        // For commentaire table (references user.id)
        $this->addSql('ALTER TABLE commentaire 
            ADD CONSTRAINT FK_COMMENTAIRE_USER_CORRECT 
            FOREIGN KEY (id_user) REFERENCES user (id) 
            ON DELETE CASCADE');
            
        // For commentaire table (references posts.id_post)
        $this->addSql('ALTER TABLE commentaire 
            ADD CONSTRAINT FK_COMMENTAIRE_POST_CORRECT 
            FOREIGN KEY (id_post) REFERENCES posts (id_post) 
            ON DELETE CASCADE');
            
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_POSTS_USER_CORRECT');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_COMMENTAIRE_USER_CORRECT');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_COMMENTAIRE_POST_CORRECT');
    }
}