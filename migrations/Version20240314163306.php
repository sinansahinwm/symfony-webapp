<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314163306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puppeteer_replay ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE puppeteer_replay ADD CONSTRAINT FK_DAB038FEB03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_DAB038FEB03A8386 ON puppeteer_replay (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puppeteer_replay DROP FOREIGN KEY FK_DAB038FEB03A8386');
        $this->addSql('DROP INDEX IDX_DAB038FEB03A8386 ON puppeteer_replay');
        $this->addSql('ALTER TABLE puppeteer_replay DROP created_by_id');
    }
}
