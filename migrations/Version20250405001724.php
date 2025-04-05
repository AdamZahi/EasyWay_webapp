<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250405001724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for posts, commentaire, user and messenger tables';
    }

    public function up(Schema $schema): void
    {
        // First handle orphaned records
        $this->handleOrphanedRecords();
        
        // Process core tables
        $this->processPostsTable($schema);
        $this->processCommentairesTable($schema);
        
        // Add messenger tables
        $this->addMessengerTables($schema);
    }

    private function handleOrphanedRecords(): void
    {
        // Delete orphaned posts
        $this->addSql('DELETE FROM posts WHERE id_user NOT IN (SELECT id_user FROM user)');
        
        // Delete orphaned commentaires
        $this->addSql('DELETE FROM commentaire WHERE id_user NOT IN (SELECT id_user FROM user)');
        $this->addSql('DELETE FROM commentaire WHERE id_post NOT IN (SELECT id_post FROM posts)');
    }

    private function processPostsTable(Schema $schema): void
    {
        $table = $schema->getTable('posts');
        
        if (!$table->hasForeignKey('FK_POSTS_USER')) {
            $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_POSTS_USER FOREIGN KEY (id_user) REFERENCES user (id_user)');
        }
    }

    private function processCommentairesTable(Schema $schema): void
    {
        $table = $schema->getTable('commentaire');
        
        if (!$table->hasForeignKey('FK_COMMENTAIRES_USER')) {
            $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_COMMENTAIRES_USER FOREIGN KEY (id_user) REFERENCES user (id_user)');
        }
        
        if (!$table->hasForeignKey('FK_COMMENTAIRES_POST')) {
            $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_COMMENTAIRES_POST FOREIGN KEY (id_post) REFERENCES posts (id_post)');
        }
    }

    private function addMessengerTables(Schema $schema): void
    {
        // Create messenger_messages table if it doesn't exist
        if (!$schema->hasTable('messenger_messages')) {
            $this->addSql('CREATE TABLE messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL,
                body LONGTEXT NOT NULL,
                headers LONGTEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                INDEX IDX_75EA56E0FB7336F0 (queue_name),
                INDEX IDX_75EA56E0E3BD61CE (available_at),
                INDEX IDX_75EA56E016BA31DB (delivered_at),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }

        // Create messenger_messages_metadata table if it doesn't exist
        if (!$schema->hasTable('messenger_messages_metadata')) {
            $this->addSql('CREATE TABLE messenger_messages_metadata (
                id INT AUTO_INCREMENT NOT NULL,
                message_id BIGINT NOT NULL,
                metadata LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\',
                created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                INDEX IDX_7586F6FB537A1329 (message_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            
            $this->addSql('ALTER TABLE messenger_messages_metadata ADD CONSTRAINT FK_7586F6FB537A1329 FOREIGN KEY (message_id) REFERENCES messenger_messages (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys for core tables
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY IF EXISTS FK_POSTS_USER');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY IF EXISTS FK_COMMENTAIRES_USER');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY IF EXISTS FK_COMMENTAIRES_POST');
        
        // Drop messenger tables
        $this->addSql('DROP TABLE IF EXISTS messenger_messages_metadata');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
    }
}