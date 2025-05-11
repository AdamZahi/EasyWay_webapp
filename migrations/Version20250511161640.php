<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511161640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ligne ADD CONSTRAINT FK_57F0DB83642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id_admin)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_57F0DB83642B8210 ON ligne (admin_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ligne DROP FOREIGN KEY FK_57F0DB83642B8210
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_57F0DB83642B8210 ON ligne
        SQL);
    }
}
