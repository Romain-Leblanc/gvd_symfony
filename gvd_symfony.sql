/* Création de la base de données 'gvd_symfony' */
DROP DATABASE IF EXISTS gvd_symfony;
CREATE DATABASE gvd_symfony DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE gvd_symfony;

/* Prise en compte des accents dans les requêtes d'insertions de données */
SET NAMES utf8;

/* Table carburant + insertions */
DROP TABLE IF EXISTS carburant;
CREATE TABLE carburant (
    id INT AUTO_INCREMENT NOT NULL,
    carburant VARCHAR(25) NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO carburant (id, carburant) VALUES
(1, 'Gasoil'),
(2, 'Essence'),
(3, 'Hybride'),
(4, 'Electrique'),
(5, 'Éthanol');


/* Table client + insertions */
DROP TABLE IF EXISTS client;
CREATE TABLE client (
    id INT AUTO_INCREMENT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    tel VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    suite_adresse VARCHAR(50) DEFAULT NULL,
    code_postal VARCHAR(255) NOT NULL,
    ville VARCHAR(255) NOT NULL,
    num_tva VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO client (id, nom, prenom, tel, email, adresse, suite_adresse, code_postal, ville, num_tva) VALUES
(1, 'Deschênes', 'Pierrette', '0433453716', 'pierrettedeschenes@test.com', '68 rue des Soeurs', NULL, '13600', 'LA CIOTAT', NULL),
(2, 'Nutman', 'Lief', '0155580653', 'liefnutman@test.com', '4 Lotheville Lane', NULL, '92600', 'ASNIÈRES-SUR-SEINE', NULL),
(3, 'Richer', 'Roslyn', '0156816643', 'roslynricher@test.com', '79 rue de Lille', NULL, '75017', 'PARIS', NULL),
(4, 'Grivois', 'Thérèse', '0274989065', 'theresegrivois@test.com', '28 Chemin Des Bateliers', NULL, '49100', 'ANGERS', NULL),
(5, 'Riquier', 'Avril', '0126461863', 'avrilriquier@test.com', '97 Square de la Couronne', NULL, '91120', 'PALAISEAU', NULL),
(6, 'Richer', 'Roslyn', '0856570754', 'roslynRicher@test.com', '79 rue de Lille', NULL, '75017', 'PARIS', NULL),
(7, 'Proulx', 'Richard', '0591832547', 'richardproulx@test.com', '9 Avenue des Tuileries', NULL, '23000', 'GUÉRET', NULL),
(8, 'Rochefort', 'Florus', '0235039833', 'florusrochefort@test.com', '79 rue de Lille', NULL, '75017', 'PARIS', NULL),
(9, 'Phaneuf', 'Victoire', '0404209618', 'vctoirepaneuf@test.com', '26 rue Beauvau', NULL, '13003', 'MARSEILLE', NULL),
(10, 'Tétrault', 'Étienne', '0441213577', 'etiennetetrault@test.com', '7 cours Franklin Roosevelt', NULL, '13007', 'MARSEILLE', NULL),
(11, 'Tougas', 'Thibaut', '0163501697', 'thibauttougas@test.com', '79 rue de Lille', NULL, '93270', 'SEVRAN', NULL);


/**
  Table doctrine_migration_versions + insertions
  Ajouté par Symfony
  */
DROP TABLE IF EXISTS doctrine_migration_versions;
CREATE TABLE IF NOT EXISTS doctrine_migration_versions (
  version VARCHAR(191) COLLATE utf8_unicode_ci NOT NULL,
  executed_at DATETIME DEFAULT NULL,
  execution_time INT(11) DEFAULT NULL,
  PRIMARY KEY (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES
('DoctrineMigrations\\Version20221122233318', '2022-11-22 23:39:52', 2035),
('DoctrineMigrations\\Version20221123165143', '2022-11-23 16:52:19', 116),
('DoctrineMigrations\\Version20221123173051', '2022-11-23 17:36:36', 267),
('DoctrineMigrations\\Version20221123211248', '2022-11-23 21:14:00', 507),
('DoctrineMigrations\\Version20221124172330', '2022-11-24 17:24:05', 185),
('DoctrineMigrations\\Version20221203184528', '2022-12-03 18:46:36', 454),
('DoctrineMigrations\\Version20221226162830', '2022-12-28 10:24:16', 592),
('DoctrineMigrations\\Version20221228112802', '2022-12-28 11:29:26', 553);


/* Table etat + insertions */
DROP TABLE IF EXISTS etat;
CREATE TABLE etat (
    id INT AUTO_INCREMENT NOT NULL,
    etat VARCHAR(30) NOT NULL,
    type VARCHAR(30) NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO etat (id, etat, type) VALUES
(1, 'En attente', 'intervention'),
(2, 'Terminé', 'intervention'),
(3, 'Facturé', 'intervention'),
(4, 'Fonctionnel', 'vehicule'),
(5, 'Hors service', 'vehicule');


/* Table facture + insertions */
DROP TABLE IF EXISTS facture;
CREATE TABLE facture (
    id INT AUTO_INCREMENT NOT NULL,
    fk_client_id INT NOT NULL,
    fk_taux_id INT NOT NULL,
    fk_moyen_paiement_id INT DEFAULT NULL,
    date_facture DATE NOT NULL,
    date_paiement DATE DEFAULT NULL,
    montant_ht DOUBLE PRECISION NOT NULL,
    montant_tva DOUBLE PRECISION NOT NULL,
    montant_ttc DOUBLE PRECISION NOT NULL,
    INDEX IDX_FE86641078B2BEB1 (fk_client_id),
    INDEX IDX_FE866410B075317B (fk_taux_id),
    INDEX IDX_FE8664105249AB64 (fk_moyen_paiement_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO facture (id, fk_client_id, fk_taux_id, fk_moyen_paiement_id, date_facture, date_paiement, montant_ht, montant_tva, montant_ttc) VALUES
(1, 1, 1, 1, '2022-11-25', '2022-11-25', 80, 16.4, 98.4),
(2, 2, 1, 3, '2022-11-25', '2022-11-25', 130, 26, 156),
(3, 3, 1, 3, '2022-11-26', '2022-11-27', 120, 24, 144),
(4, 4, 1, 1, '2022-11-26', '2022-11-27', 150, 30, 80),
(5, 3, 1, 1, '2022-12-03', '2022-12-03', 365, 73, 438),
(6, 3, 1, 3, '2023-01-02', '2023-01-02', 115, 23, 138);


/* Table intervention + insertions */
DROP TABLE IF EXISTS intervention;
CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL,
    fk_client_id INT NOT NULL,
    fk_vehicule_id INT NOT NULL,
    fk_facture_id INT DEFAULT NULL,
    fk_etat_id INT NOT NULL,
    date_creation DATE NOT NULL,
    date_intervention DATE NOT NULL,
    duree_intervention SMALLINT NOT NULL,
    detail_intervention VARCHAR(500) NOT NULL,
    montant_ht DOUBLE PRECISION DEFAULT NULL,
    INDEX IDX_D11814AB78B2BEB1 (fk_client_id),
    INDEX IDX_D11814AB23BC9925 (fk_vehicule_id),
    INDEX IDX_D11814AB8F43249B (fk_facture_id),
    INDEX IDX_292FFF1DFD71BBD3 (fk_etat_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO intervention (id, fk_client_id, fk_vehicule_id, fk_facture_id, fk_etat_id, date_creation, date_intervention, duree_intervention, detail_intervention, montant_ht) VALUES
(1, 1, 1, 1, 3, '2022-11-25', '2022-11-25', 1, 'Révision', 80),
(2, 2, 2, 2, 3, '2022-11-25', '2022-11-26', 2, 'Changement pneus\r\nParallélisme\r\nChangement capot moteur', 130),
(3, 3, 3, 3, 3, '2022-11-26', '2022-11-27', 1, 'Contrôle technique', 120),
(4, 4, 4, 4, 3, '2022-11-26', '2022-11-27', 2, 'Ligne echappement', 150),
(5, 3, 3, 5, 3, '2022-12-03', '2022-12-03', 2, 'changement toit et parebrise', 250),
(6, 3, 3, 6, 3, '2023-01-02', '2023-01-02', 1, 'pneus\r\nvidange', 115),
(7, 9, 7, NULL, 2, '2023-01-02', '2023-01-03', 1, 'Changement rétro droit', 65);


/* Table marque + insertions */
DROP TABLE IF EXISTS marque;
CREATE TABLE marque (
    id INT AUTO_INCREMENT NOT NULL,
    marque VARCHAR(50) NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO marque (id, marque) VALUES
(1, 'CITROEN'),
(2, 'DACIA'),
(3, 'FIAT'),
(4, 'FORD'),
(5, 'KIA'),
(6, 'NISSAN'),
(7, 'OPEL'),
(8, 'PEUGEOT'),
(9, 'RENAULT'),
(10, 'VOLKSWAGEN'),
(11, 'TESLA');


/**
  Table messenger_messages
  Ajouté par Symfony
  */
DROP TABLE IF EXISTS messenger_messages;
CREATE TABLE messenger_messages (
    id BIGINT AUTO_INCREMENT NOT NULL,
    body LONGTEXT NOT NULL,
    headers LONGTEXT NOT NULL,
    queue_name VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    available_at DATETIME NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    INDEX IDX_75EA56E0FB7336F0 (queue_name),
    INDEX IDX_75EA56E0E3BD61CE (available_at),
    INDEX IDX_75EA56E016BA31DB (delivered_at),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;


/* Table modele + insertions */
DROP TABLE IF EXISTS modele;
CREATE TABLE modele (
    id INT AUTO_INCREMENT NOT NULL,
    fk_marque_id INT NOT NULL,
    modele VARCHAR(100) NOT NULL,
    INDEX IDX_10028558297E6E22 (fk_marque_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO modele (id, fk_marque_id, modele) VALUES
(1, 1, 'C2'),
(2, 1, 'C3'),
(3, 1, 'C4'),
(4, 1, 'DS3'),
(5, 1, 'DS4'),
(6, 1, 'DS5'),
(7, 1, 'SAXO'),
(8, 1, 'XANTIA'),
(9, 1, 'XSARA'),
(10, 2, 'DUSTER'),
(11, 2, 'LOGAN'),
(12, 2, 'SANDERO'),
(13, 3, '500'),
(14, 3, 'MULTIPLA'),
(15, 4, 'FIESTA'),
(16, 4, 'FOCUS'),
(17, 5, 'CEED'),
(18, 5, 'SPORTAGE'),
(19, 6, 'JUKE'),
(20, 6, 'QASHQAI'),
(21, 6, 'X-TRAIL'),
(22, 7, 'ASTRA'),
(23, 7, 'INSIGNIA'),
(24, 7, 'MERIVA'),
(25, 7, 'MOKKA'),
(26, 7, 'ZAFIRA'),
(27, 8, '206'),
(28, 8, '207'),
(29, 8, '208'),
(30, 8, '307'),
(31, 8, '308'),
(32, 8, '406'),
(33, 8, '407'),
(34, 8, '5008'),
(35, 9, 'CAPTUR'),
(36, 9, 'CLIO'),
(37, 9, 'ESPACE'),
(38, 9, 'LAGUNA'),
(39, 9, 'MEGANE'),
(40, 9, 'SCENIC'),
(41, 10, 'TWINGO'),
(42, 10, 'COCCINELLE'),
(43, 10, 'GOLF'),
(44, 10, 'ID.4'),
(45, 10, 'PASSAT'),
(46, 10, 'POLO'),
(47, 10, 'TIGUAN'),
(48, 10, 'TOUAREG'),
(49, 11, 'MODEL 3');


/* Table moyen_paiement + insertions */
DROP TABLE IF EXISTS moyen_paiement;
CREATE TABLE moyen_paiement (
    id INT AUTO_INCREMENT NOT NULL,
    moyen_paiement VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO moyen_paiement (id, moyen_paiement) VALUES
(1, 'Carte bancaire'),
(2, 'Virement'),
(3, 'Chèque');


/* Table tva + insertions */
DROP TABLE IF EXISTS tva;
CREATE TABLE tva (
    id INT AUTO_INCREMENT NOT NULL,
    taux DOUBLE PRECISION NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO tva (id, taux) VALUES
(1, 20),
(2, 10);


/* Table utilisateur + insertions */
DROP TABLE IF EXISTS utilisateur;
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT NOT NULL,
    email VARCHAR(180) NOT NULL,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO utilisateur (id, email, roles, password, nom, prenom) VALUES
(1, 'utilisateur@gvd.test', '["ROLE_USER"]', '$2y$13$XLR8GIvul6YwfmAmIJeKU.Stzvz2gBL9lBPEYByiAdi83n/cTx4Ry', 'DUPONT', 'Thomas'),
(2, 'administrateur@gvd.test', '["ROLE_ADMIN"]', '$2y$13$UkTKP4gX0DtvrQgDxozgqeX02VaAoHXilctKTC0NVecqdsZpRGGWK', 'DUPOND', 'Pascal');


/* Table vehicule + insertions */
DROP TABLE IF EXISTS vehicule;
CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL,
    fk_client_id INT NOT NULL,
    fk_marque_id INT NOT NULL,
    fk_modele_id INT NOT NULL,
    fk_carburant_id INT NOT NULL,
    immatriculation VARCHAR(10) NOT NULL,
    kilometrage BIGINT NOT NULL,
    annee INT NOT NULL,
    fk_etat_id INT NOT NULL,
    INDEX IDX_292FFF1D78B2BEB1 (fk_client_id),
    INDEX IDX_292FFF1D297E6E22 (fk_marque_id),
    INDEX IDX_292FFF1DCD4D609A (fk_modele_id),
    INDEX IDX_292FFF1D1307AF3D (fk_carburant_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

INSERT INTO vehicule (id, fk_client_id, fk_marque_id, fk_modele_id, fk_carburant_id, immatriculation, kilometrage, annee, fk_etat_id) VALUES
(1, 1, 1, 3, 2, 'TW-012-ET', 70000, 2014, 4),
(2, 2, 1, 3, 1, 'QY-228-JO', 200000, 2017, 4),
(3, 3, 3, 13, 2, 'WK-883-XB', 90000, 2013, 5),
(4, 4, 4, 15, 2, 'ZS-933-GF', 150000, 2015, 4),
(5, 4, 8, 27, 2, 'MI-712-PA', 278564, 2010, 4),
(6, 7, 4, 16, 1, 'PS-671-LY', 312096, 2012, 4),
(7, 9, 9, 36, 2, 'ZZ-321-AA', 264567, 2013, 4);


/* Contraintes pour les tables */
ALTER TABLE facture
    ADD CONSTRAINT FK_FE86641078B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES client (id),
    ADD CONSTRAINT FK_FE866410B075317B FOREIGN KEY (fk_taux_id) REFERENCES tva (id),
    ADD CONSTRAINT FK_FE8664105249AB64 FOREIGN KEY (fk_moyen_paiement_id) REFERENCES moyen_paiement (id);

ALTER TABLE intervention
    ADD CONSTRAINT FK_D11814AB78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES client (id),
    ADD CONSTRAINT FK_D11814AB23BC9925 FOREIGN KEY (fk_vehicule_id) REFERENCES vehicule (id),
    ADD CONSTRAINT FK_D11814AB8F43249B FOREIGN KEY (fk_facture_id) REFERENCES facture (id),
    ADD CONSTRAINT FK_D11814ABFD71BBD3 FOREIGN KEY (fk_etat_id) REFERENCES etat (id);

ALTER TABLE modele ADD CONSTRAINT FK_10028558297E6E22 FOREIGN KEY (fk_marque_id) REFERENCES marque (id);

ALTER TABLE vehicule
    ADD CONSTRAINT FK_292FFF1D78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES client (id),
    ADD CONSTRAINT FK_292FFF1D297E6E22 FOREIGN KEY (fk_marque_id) REFERENCES marque (id),
    ADD CONSTRAINT FK_292FFF1DCD4D609A FOREIGN KEY (fk_modele_id) REFERENCES modele (id),
    ADD CONSTRAINT FK_292FFF1D1307AF3D FOREIGN KEY (fk_carburant_id) REFERENCES carburant (id),
    ADD CONSTRAINT FK_292FFF1DFD71BBD3 FOREIGN KEY (fk_etat_id) REFERENCES etat (id);