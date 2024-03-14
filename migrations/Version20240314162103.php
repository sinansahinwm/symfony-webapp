<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314162103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE puppeteer_replay_hook_record (id INT AUTO_INCREMENT NOT NULL, replay_id INT NOT NULL, step LONGTEXT NOT NULL, screenshot LONGTEXT NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_3E3BA9AB186CE3E1 (replay_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE puppeteer_replay_hook_record ADD CONSTRAINT FK_3E3BA9AB186CE3E1 FOREIGN KEY (replay_id) REFERENCES puppeteer_replay (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puppeteer_replay_hook_record DROP FOREIGN KEY FK_3E3BA9AB186CE3E1');
        $this->addSql('DROP TABLE puppeteer_replay_hook_record');
    }
}
