<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250814143656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pay_pal (id INT AUTO_INCREMENT NOT NULL, booking_id_id INT NOT NULL, INDEX IDX_B9135125EE3863E2 (booking_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pay_pal ADD CONSTRAINT FK_B9135125EE3863E2 FOREIGN KEY (booking_id_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE booking ADD payment_method VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pay_pal DROP FOREIGN KEY FK_B9135125EE3863E2');
        $this->addSql('DROP TABLE pay_pal');
        $this->addSql('ALTER TABLE booking DROP payment_method');
    }
}
