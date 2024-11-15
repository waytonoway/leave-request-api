<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241115091631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE leave_request (id INT AUTO_INCREMENT NOT NULL, leave_type INT NOT NULL, user_id INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, INDEX IDX_7DC8F778E2BC4391 (leave_type), INDEX IDX_7DC8F778A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leave_type (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_E2BC43918CDE5729 (type), INDEX IDX_E2BC43918CDE5729 (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, position VARCHAR(50) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D649E7927C74 (email), INDEX IDX_8D93D649462CE4F5 (position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE leave_request ADD CONSTRAINT FK_7DC8F778E2BC4391 FOREIGN KEY (leave_type) REFERENCES leave_type (id)');
        $this->addSql('ALTER TABLE leave_request ADD CONSTRAINT FK_7DC8F778A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE leave_request DROP FOREIGN KEY FK_7DC8F778E2BC4391');
        $this->addSql('ALTER TABLE leave_request DROP FOREIGN KEY FK_7DC8F778A76ED395');
        $this->addSql('DROP TABLE leave_request');
        $this->addSql('DROP TABLE leave_type');
        $this->addSql('DROP TABLE user');
    }
}
