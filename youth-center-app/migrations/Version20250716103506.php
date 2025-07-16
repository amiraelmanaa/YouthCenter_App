<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716103506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE center ADD manager_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE center ADD CONSTRAINT FK_40F0EB24569B5E6D FOREIGN KEY (manager_id_id) REFERENCES center_manager (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40F0EB24569B5E6D ON center (manager_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE center DROP FOREIGN KEY FK_40F0EB24569B5E6D');
        $this->addSql('DROP INDEX UNIQ_40F0EB24569B5E6D ON center');
        $this->addSql('ALTER TABLE center DROP manager_id_id');
    }
}
