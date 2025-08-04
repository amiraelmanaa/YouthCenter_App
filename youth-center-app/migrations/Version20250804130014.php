<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250804130014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA783E3463 FOREIGN KEY (manager_id) REFERENCES center_manager (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BAE6C5D496 FOREIGN KEY (technician_id) REFERENCES technician (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BA783E3463');
        $this->addSql('ALTER TABLE assignment DROP FOREIGN KEY FK_30C544BAE6C5D496');
        $this->addSql('DROP TABLE assignment');
    }
}
