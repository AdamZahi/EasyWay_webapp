<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250407103655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for existing posts, user, commentaire tables with their foreign keys';
    }

    public function up(Schema $schema): void
    {
        // Since tables already exist, we'll just ensure the foreign keys are correct
        
        // 1. First drop existing foreign keys if they exist (with conditional checks)
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY IF EXISTS FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY IF EXISTS FK_67F068BCD1AA708F');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY IF EXISTS FK_POSTS_USER');
        
        // 2. Recreate the foreign keys with proper constraints
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_COMMENTAIRE_USER 
                      FOREIGN KEY (user_id) REFERENCES user (id_user) ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_COMMENTAIRE_POST 
                      FOREIGN KEY (id_post) REFERENCES posts (id_post) ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_POSTS_USER 
                      FOREIGN KEY (id_user) REFERENCES user (id_user) ON DELETE CASCADE');
        
        // 3. Add payment table foreign key if it doesn't exist
      
        
        // 4. Recreate indexes if they don't exist
        if (!$schema->getTable('commentaire')->hasIndex('IDX_COMMENTAIRE_USER')) {
            $this->addSql('CREATE INDEX IDX_COMMENTAIRE_USER ON commentaire (user_id)');
        }
        
        if (!$schema->getTable('commentaire')->hasIndex('IDX_COMMENTAIRE_POST')) {
            $this->addSql('CREATE INDEX IDX_COMMENTAIRE_POST ON commentaire (id_post)');
        }
        
        if (!$schema->getTable('posts')->hasIndex('IDX_POSTS_USER')) {
            $this->addSql('CREATE INDEX IDX_POSTS_USER ON posts (id_user)');
        }
       
    }

    public function down(Schema $schema): void
    {
        // Safely drop foreign keys if they exist
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY IF EXISTS FK_COMMENTAIRE_USER');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY IF EXISTS FK_COMMENTAIRE_POST');
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY IF EXISTS FK_POSTS_USER');
    
        
        // Drop indexes if they exist
        $this->addSql('DROP INDEX IF EXISTS IDX_COMMENTAIRE_USER ON commentaire');
        $this->addSql('DROP INDEX IF EXISTS IDX_COMMENTAIRE_POST ON commentaire');
        $this->addSql('DROP INDEX IF EXISTS IDX_POSTS_USER ON posts');
      
    }
}