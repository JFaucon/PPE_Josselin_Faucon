<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016124752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE baie (id INT IDENTITY NOT NULL, nb_spot INT NOT NULL, code NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE forfait (id INT IDENTITY NOT NULL, name NVARCHAR(255) NOT NULL, price INT NOT NULL, nb_slot INT NOT NULL, discount INT NOT NULL, nb_month INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE reservation (id INT IDENTITY NOT NULL, userr_id INT NOT NULL, forfait_id INT NOT NULL, number NVARCHAR(255) NOT NULL, begin_date DATE NOT NULL, end_date DATE, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_42C84955DF0FD358 ON reservation (userr_id)');
        $this->addSql('CREATE INDEX IDX_42C84955906D5F2C ON reservation (forfait_id)');
        $this->addSql('CREATE TABLE unite (id INT IDENTITY NOT NULL, baie_id INT NOT NULL, reservation_id INT, num_spot NVARCHAR(255) NOT NULL, available BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_1D64C11843375062 ON unite (baie_id)');
        $this->addSql('CREATE INDEX IDX_1D64C118B83297E7 ON unite (reservation_id)');
        $this->addSql('CREATE TABLE [user] (id INT IDENTITY NOT NULL, email NVARCHAR(180) NOT NULL, roles VARCHAR(MAX) NOT NULL, password NVARCHAR(255) NOT NULL, first_name NVARCHAR(255), last_name NVARCHAR(255), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON [user] (email) WHERE email IS NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', roles');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT IDENTITY NOT NULL, body VARCHAR(MAX) NOT NULL, headers VARCHAR(MAX) NOT NULL, queue_name NVARCHAR(190) NOT NULL, created_at DATETIME2(6) NOT NULL, available_at DATETIME2(6) NOT NULL, delivered_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'messenger_messages\', N\'COLUMN\', created_at');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'messenger_messages\', N\'COLUMN\', available_at');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'messenger_messages\', N\'COLUMN\', delivered_at');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DF0FD358 FOREIGN KEY (userr_id) REFERENCES [user] (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955906D5F2C FOREIGN KEY (forfait_id) REFERENCES forfait (id)');
        $this->addSql('ALTER TABLE unite ADD CONSTRAINT FK_1D64C11843375062 FOREIGN KEY (baie_id) REFERENCES baie (id)');
        $this->addSql('ALTER TABLE unite ADD CONSTRAINT FK_1D64C118B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955DF0FD358');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955906D5F2C');
        $this->addSql('ALTER TABLE unite DROP CONSTRAINT FK_1D64C11843375062');
        $this->addSql('ALTER TABLE unite DROP CONSTRAINT FK_1D64C118B83297E7');
        $this->addSql('DROP TABLE baie');
        $this->addSql('DROP TABLE forfait');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE unite');
        $this->addSql('DROP TABLE [user]');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
