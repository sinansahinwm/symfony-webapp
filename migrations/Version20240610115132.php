<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610115132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE marketplace (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, search_url VARCHAR(255) NOT NULL, search_handler_type ENUM(\'STEPS\', \'NAVIGATION\') NOT NULL COMMENT \'(DC2Type:marketplace_search_handler)\', search_selectors LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, marketplace_id INT NOT NULL, identity VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_D34A04AD7078ABE4 (marketplace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7078ABE4 FOREIGN KEY (marketplace_id) REFERENCES marketplace (id)');
        $this->addSql('ALTER TABLE web_scraping_request ADD consumed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD xhrlog LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7078ABE4');
        $this->addSql('DROP TABLE marketplace');
        $this->addSql('DROP TABLE product');
        $this->addSql('ALTER TABLE web_scraping_request DROP consumed_at, DROP xhrlog');
    }
}
