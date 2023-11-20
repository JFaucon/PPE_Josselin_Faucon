<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231116142033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report (id INT IDENTITY NOT NULL, userr_id INT NOT NULL, subject NVARCHAR(255) NOT NULL, object NVARCHAR(2048) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C42F7784DF0FD358 ON report (userr_id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784DF0FD358 FOREIGN KEY (userr_id) REFERENCES [user] (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784DF0FD358');
        $this->addSql('DROP TABLE report');
    }
}
