<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711093010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activities_center (activities_id INT NOT NULL, center_id INT NOT NULL, INDEX IDX_5FE9E1692A4DB562 (activities_id), INDEX IDX_5FE9E1695932F377 (center_id), PRIMARY KEY(activities_id, center_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activities_center ADD CONSTRAINT FK_5FE9E1692A4DB562 FOREIGN KEY (activities_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activities_center ADD CONSTRAINT FK_5FE9E1695932F377 FOREIGN KEY (center_id) REFERENCES center (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activities_center DROP FOREIGN KEY FK_5FE9E1692A4DB562');
        $this->addSql('ALTER TABLE activities_center DROP FOREIGN KEY FK_5FE9E1695932F377');
        $this->addSql('DROP TABLE activities_center');
    }
}
