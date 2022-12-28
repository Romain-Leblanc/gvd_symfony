<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221228112802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat ADD type VARCHAR(30) NOT NULL');
        // Ajoute/met à jour les valeurs pour l'entité 'Etat'
        $this->addSql('UPDATE etat SET type="intervention"');
        $this->addSql('INSERT INTO etat (etat, type) VALUES ("Fonctionnel", "vehicule"), ("Hors service", "vehicule")');
        $this->addSql('ALTER TABLE vehicule ADD fk_etat_id INT NOT NULL');
        // Définit l'état des véhicules
        $this->addSql('UPDATE vehicule SET fk_etat_id=4');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DFD71BBD3 FOREIGN KEY (fk_etat_id) REFERENCES etat (id)');
        $this->addSql('CREATE INDEX IDX_292FFF1DFD71BBD3 ON vehicule (fk_etat_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat DROP type');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DFD71BBD3');
        $this->addSql('DROP INDEX IDX_292FFF1DFD71BBD3 ON vehicule');
        $this->addSql('ALTER TABLE vehicule DROP fk_etat_id');
    }
}
