<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302153644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE team_ownership (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, team_id INT NOT NULL, UNIQUE INDEX UNIQ_AF95F273A76ED395 (user_id), UNIQUE INDEX UNIQ_AF95F273296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team_ownership ADD CONSTRAINT FK_AF95F273A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE team_ownership ADD CONSTRAINT FK_AF95F273296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_ownership DROP FOREIGN KEY FK_AF95F273A76ED395');
        $this->addSql('ALTER TABLE team_ownership DROP FOREIGN KEY FK_AF95F273296CD8AE');
        $this->addSql('DROP TABLE team_ownership');
    }
}
