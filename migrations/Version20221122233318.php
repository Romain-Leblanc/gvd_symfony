<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221122233318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carburant (id INT AUTO_INCREMENT NOT NULL, carburant VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, suite_adresse VARCHAR(50) DEFAULT NULL, code_postal VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, num_tva VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etat (id INT AUTO_INCREMENT NOT NULL, etat VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, fk_client_id INT NOT NULL, fk_taux_id INT NOT NULL, fk_moyen_paiement_id INT NOT NULL, date_facture DATE NOT NULL, date_paiement DATE NOT NULL, montant_ht DOUBLE PRECISION NOT NULL, montant_tva DOUBLE PRECISION NOT NULL, montant_ttc DOUBLE PRECISION NOT NULL, INDEX IDX_FE86641078B2BEB1 (fk_client_id), INDEX IDX_FE866410B075317B (fk_taux_id), INDEX IDX_FE8664105249AB64 (fk_moyen_paiement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, fk_client_id INT NOT NULL, fk_vehicule_id INT NOT NULL, fk_facture_id INT DEFAULT NULL, fk_etat_id INT NOT NULL, date_creation DATE NOT NULL, date_intervention DATE NOT NULL, duree_intervention SMALLINT NOT NULL, detail_intervention VARCHAR(500) NOT NULL, montant_ht DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D11814AB78B2BEB1 (fk_client_id), INDEX IDX_D11814AB23BC9925 (fk_vehicule_id), INDEX IDX_D11814AB8F43249B (fk_facture_id), INDEX IDX_D11814ABFD71BBD3 (fk_etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modele (id INT AUTO_INCREMENT NOT NULL, fk_marque_id INT NOT NULL, INDEX IDX_10028558297E6E22 (fk_marque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moyen_paiement (id INT AUTO_INCREMENT NOT NULL, moyen_paiement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tva (id INT AUTO_INCREMENT NOT NULL, taux DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, fk_client_id INT NOT NULL, fk_marque_id INT NOT NULL, fk_modele_id INT NOT NULL, fk_carburant_id INT NOT NULL, immatriculation VARCHAR(10) NOT NULL, kilometrage BIGINT NOT NULL, INDEX IDX_292FFF1D78B2BEB1 (fk_client_id), INDEX IDX_292FFF1D297E6E22 (fk_marque_id), INDEX IDX_292FFF1DCD4D609A (fk_modele_id), INDEX IDX_292FFF1D1307AF3D (fk_carburant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641078B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410B075317B FOREIGN KEY (fk_taux_id) REFERENCES tva (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664105249AB64 FOREIGN KEY (fk_moyen_paiement_id) REFERENCES moyen_paiement (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB23BC9925 FOREIGN KEY (fk_vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB8F43249B FOREIGN KEY (fk_facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABFD71BBD3 FOREIGN KEY (fk_etat_id) REFERENCES etat (id)');
        $this->addSql('ALTER TABLE modele ADD CONSTRAINT FK_10028558297E6E22 FOREIGN KEY (fk_marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D297E6E22 FOREIGN KEY (fk_marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DCD4D609A FOREIGN KEY (fk_modele_id) REFERENCES modele (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D1307AF3D FOREIGN KEY (fk_carburant_id) REFERENCES carburant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641078B2BEB1');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410B075317B');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664105249AB64');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB78B2BEB1');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB23BC9925');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB8F43249B');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABFD71BBD3');
        $this->addSql('ALTER TABLE modele DROP FOREIGN KEY FK_10028558297E6E22');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D78B2BEB1');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D297E6E22');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DCD4D609A');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D1307AF3D');
        $this->addSql('DROP TABLE carburant');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE etat');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE intervention');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE modele');
        $this->addSql('DROP TABLE moyen_paiement');
        $this->addSql('DROP TABLE tva');
        $this->addSql('DROP TABLE vehicule');
    }
}
