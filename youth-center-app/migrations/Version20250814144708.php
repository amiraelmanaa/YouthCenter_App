<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250814144708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carte_bancaire (id INT AUTO_INCREMENT NOT NULL, booking_id_id INT NOT NULL, nom_du_titulaire VARCHAR(255) NOT NULL, numero_de_carte VARCHAR(255) NOT NULL, date_dexpiration VARCHAR(255) NOT NULL, code_cvv VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, INDEX IDX_59E3C22DEE3863E2 (booking_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE virement (id INT AUTO_INCREMENT NOT NULL, booking_id_id INT NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, INDEX IDX_2D4DCFA6EE3863E2 (booking_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carte_bancaire ADD CONSTRAINT FK_59E3C22DEE3863E2 FOREIGN KEY (booking_id_id) REFERENCES booking (id)');
        $this->addSql('ALTER TABLE virement ADD CONSTRAINT FK_2D4DCFA6EE3863E2 FOREIGN KEY (booking_id_id) REFERENCES booking (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_bancaire DROP FOREIGN KEY FK_59E3C22DEE3863E2');
        $this->addSql('ALTER TABLE virement DROP FOREIGN KEY FK_2D4DCFA6EE3863E2');
        $this->addSql('DROP TABLE carte_bancaire');
        $this->addSql('DROP TABLE virement');
    }
}
