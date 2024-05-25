<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525110632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_plan ADD included_features LONGTEXT NOT NULL, ADD not_included_features LONGTEXT DEFAULT NULL, DROP plan_features, DROP plan_features_not_included');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_plan ADD plan_features LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD plan_features_not_included LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP included_features, DROP not_included_features');
    }
}
