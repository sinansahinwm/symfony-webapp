<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314141556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puppeteer_replay CHANGE status status ENUM(\'UPLOAD\', \'PROCESSING\', \'COMPLETED\') DEFAULT NULL COMMENT \'(DC2Type:puppeteer_replay_status)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puppeteer_replay CHANGE status status ENUM(\'UPLOAD\', \'PROCESSING\', \'COMPLETED\') NOT NULL COMMENT \'(DC2Type:puppeteer_replay_status)\'');
    }
}
