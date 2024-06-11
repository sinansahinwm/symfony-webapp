<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611114817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE url url LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE web_scraping_request CHANGE completed_handle completed_handle ENUM(\'HANDLE_NULL\', \'HANDLE_EXTRACT_PRODUCTS\') DEFAULT NULL COMMENT \'(DC2Type:web_scraping_request_completed_handle)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE url url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE web_scraping_request CHANGE completed_handle completed_handle VARCHAR(255) DEFAULT NULL');
    }
}
