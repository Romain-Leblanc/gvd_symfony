-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 06 jan. 2023 à 10:27
-- Version du serveur :  5.7.31
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gvd_symfony`
--

-- --------------------------------------------------------

--
-- Structure de la table `carburant`
--

DROP TABLE IF EXISTS `carburant`;
CREATE TABLE IF NOT EXISTS `carburant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carburant` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `carburant`
--

INSERT INTO `carburant` (`id`, `carburant`) VALUES
(1, 'Gasoil'),
(2, 'Essence'),
(3, 'Hybride'),
(4, 'Electrique'),
(5, 'Éthanol');

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suite_adresse` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_postal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_tva` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id`, `nom`, `prenom`, `tel`, `email`, `adresse`, `suite_adresse`, `code_postal`, `ville`, `num_tva`) VALUES
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

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20221122233318', '2022-11-22 23:39:52', 2035),
('DoctrineMigrations\\Version20221123165143', '2022-11-23 16:52:19', 116),
('DoctrineMigrations\\Version20221123173051', '2022-11-23 17:36:36', 267),
('DoctrineMigrations\\Version20221123211248', '2022-11-23 21:14:00', 507),
('DoctrineMigrations\\Version20221124172330', '2022-11-24 17:24:05', 185),
('DoctrineMigrations\\Version20221203184528', '2022-12-03 18:46:36', 454),
('DoctrineMigrations\\Version20221226162830', '2022-12-28 10:24:16', 592),
('DoctrineMigrations\\Version20221228112802', '2022-12-28 11:29:26', 553);

-- --------------------------------------------------------

--
-- Structure de la table `etat`
--

DROP TABLE IF EXISTS `etat`;
CREATE TABLE IF NOT EXISTS `etat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `etat` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etat`
--

INSERT INTO `etat` (`id`, `etat`, `type`) VALUES
(1, 'En attente', 'intervention'),
(2, 'Terminé', 'intervention'),
(3, 'Facturé', 'intervention'),
(4, 'Fonctionnel', 'vehicule'),
(5, 'Hors service', 'vehicule');

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_client_id` int(11) NOT NULL,
  `fk_taux_id` int(11) NOT NULL,
  `fk_moyen_paiement_id` int(11) DEFAULT NULL,
  `date_facture` date NOT NULL,
  `date_paiement` date DEFAULT NULL,
  `montant_ht` double NOT NULL,
  `montant_tva` double NOT NULL,
  `montant_ttc` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FE86641078B2BEB1` (`fk_client_id`),
  KEY `IDX_FE866410B075317B` (`fk_taux_id`),
  KEY `IDX_FE8664105249AB64` (`fk_moyen_paiement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `facture`
--

INSERT INTO `facture` (`id`, `fk_client_id`, `fk_taux_id`, `fk_moyen_paiement_id`, `date_facture`, `date_paiement`, `montant_ht`, `montant_tva`, `montant_ttc`) VALUES
(1, 1, 1, 1, '2022-11-25', '2022-11-25', 80, 16.4, 98.4),
(2, 2, 1, 3, '2022-11-25', '2022-11-25', 130, 26, 156),
(3, 3, 1, 3, '2022-11-26', '2022-11-27', 120, 24, 144),
(4, 4, 1, 1, '2022-11-26', '2022-11-27', 150, 30, 80),
(5, 3, 1, 1, '2022-12-03', '2022-12-03', 365, 73, 438),
(6, 3, 1, 3, '2023-01-02', '2023-01-02', 115, 23, 138);

-- --------------------------------------------------------

--
-- Structure de la table `intervention`
--

DROP TABLE IF EXISTS `intervention`;
CREATE TABLE IF NOT EXISTS `intervention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_client_id` int(11) NOT NULL,
  `fk_vehicule_id` int(11) NOT NULL,
  `fk_facture_id` int(11) DEFAULT NULL,
  `fk_etat_id` int(11) NOT NULL,
  `date_creation` date NOT NULL,
  `date_intervention` date NOT NULL,
  `duree_intervention` smallint(6) NOT NULL,
  `detail_intervention` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant_ht` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D11814AB78B2BEB1` (`fk_client_id`),
  KEY `IDX_D11814AB23BC9925` (`fk_vehicule_id`),
  KEY `IDX_D11814AB8F43249B` (`fk_facture_id`),
  KEY `IDX_D11814ABFD71BBD3` (`fk_etat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `intervention`
--

INSERT INTO `intervention` (`id`, `fk_client_id`, `fk_vehicule_id`, `fk_facture_id`, `fk_etat_id`, `date_creation`, `date_intervention`, `duree_intervention`, `detail_intervention`, `montant_ht`) VALUES
(1, 1, 1, 1, 3, '2022-11-25', '2022-11-25', 1, 'Révision', 80),
(2, 2, 2, 2, 3, '2022-11-25', '2022-11-26', 2, 'Changement pneus\r\nParallélisme\r\nChangement capot moteur', 130),
(3, 3, 3, 3, 3, '2022-11-26', '2022-11-27', 1, 'Contrôle technique', 120),
(4, 4, 4, 4, 3, '2022-11-26', '2022-11-27', 2, 'Ligne echappement', 150),
(5, 3, 3, 5, 3, '2022-12-03', '2022-12-03', 2, 'changement toit et parebrise', 250),
(6, 3, 3, 6, 3, '2023-01-02', '2023-01-02', 1, 'pneus\r\nvidange', 115),
(7, 9, 7, NULL, 2, '2023-01-02', '2023-01-03', 1, 'Changement rétro droit', 65);

-- --------------------------------------------------------

--
-- Structure de la table `marque`
--

DROP TABLE IF EXISTS `marque`;
CREATE TABLE IF NOT EXISTS `marque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marque` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `marque`
--

INSERT INTO `marque` (`id`, `marque`) VALUES
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

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `modele`
--

DROP TABLE IF EXISTS `modele`;
CREATE TABLE IF NOT EXISTS `modele` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_marque_id` int(11) NOT NULL,
  `modele` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_10028558297E6E22` (`fk_marque_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `modele`
--

INSERT INTO `modele` (`id`, `fk_marque_id`, `modele`) VALUES
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

-- --------------------------------------------------------

--
-- Structure de la table `moyen_paiement`
--

DROP TABLE IF EXISTS `moyen_paiement`;
CREATE TABLE IF NOT EXISTS `moyen_paiement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moyen_paiement` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `moyen_paiement`
--

INSERT INTO `moyen_paiement` (`id`, `moyen_paiement`) VALUES
(1, 'Carte bancaire'),
(2, 'Virement'),
(3, 'Chèque');

-- --------------------------------------------------------

--
-- Structure de la table `tva`
--

DROP TABLE IF EXISTS `tva`;
CREATE TABLE IF NOT EXISTS `tva` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taux` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tva`
--

INSERT INTO `tva` (`id`, `taux`) VALUES
(1, 20),
(2, 10);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `email`, `roles`, `password`, `nom`, `prenom`) VALUES
(1, 'utilisateur@gvd.test', '{\"1\": \"ROLE_USER\"}', '$2y$13$8WeflDctpdvXugKI/HoAiOTyLnwoBOfy6cCaQSS1mM4LGkoZ6GMGK', 'DUPONT', 'Thomas'),
(2, 'administrateur@gvd.test', '[\"ROLE_ADMIN\"]', '$2y$13$UkTKP4gX0DtvrQgDxozgqeX02VaAoHXilctKTC0NVecqdsZpRGGWK', 'DUPOND', 'Pascal');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

DROP TABLE IF EXISTS `vehicule`;
CREATE TABLE IF NOT EXISTS `vehicule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_client_id` int(11) NOT NULL,
  `fk_marque_id` int(11) NOT NULL,
  `fk_modele_id` int(11) NOT NULL,
  `fk_carburant_id` int(11) NOT NULL,
  `immatriculation` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kilometrage` bigint(20) NOT NULL,
  `annee` int(11) NOT NULL,
  `fk_etat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_292FFF1D78B2BEB1` (`fk_client_id`),
  KEY `IDX_292FFF1D297E6E22` (`fk_marque_id`),
  KEY `IDX_292FFF1DCD4D609A` (`fk_modele_id`),
  KEY `IDX_292FFF1D1307AF3D` (`fk_carburant_id`),
  KEY `IDX_292FFF1DFD71BBD3` (`fk_etat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `fk_client_id`, `fk_marque_id`, `fk_modele_id`, `fk_carburant_id`, `immatriculation`, `kilometrage`, `annee`, `fk_etat_id`) VALUES
(1, 1, 1, 3, 2, 'TW-012-ET', 70000, 2014, 4),
(2, 2, 1, 3, 1, 'QY-228-JO', 200000, 2017, 4),
(3, 3, 3, 13, 2, 'WK-883-XB', 90000, 2013, 5),
(4, 4, 4, 15, 2, 'ZS-933-GF', 150000, 2015, 4),
(5, 4, 8, 27, 2, 'MI-712-PA', 278564, 2010, 4),
(6, 7, 4, 16, 1, 'PS-671-LY', 312096, 2012, 4),
(7, 9, 9, 36, 2, 'ZZ-321-AA', 264567, 2013, 4);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `FK_FE8664105249AB64` FOREIGN KEY (`fk_moyen_paiement_id`) REFERENCES `moyen_paiement` (`id`),
  ADD CONSTRAINT `FK_FE86641078B2BEB1` FOREIGN KEY (`fk_client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_FE866410B075317B` FOREIGN KEY (`fk_taux_id`) REFERENCES `tva` (`id`);

--
-- Contraintes pour la table `intervention`
--
ALTER TABLE `intervention`
  ADD CONSTRAINT `FK_D11814AB23BC9925` FOREIGN KEY (`fk_vehicule_id`) REFERENCES `vehicule` (`id`),
  ADD CONSTRAINT `FK_D11814AB78B2BEB1` FOREIGN KEY (`fk_client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_D11814AB8F43249B` FOREIGN KEY (`fk_facture_id`) REFERENCES `facture` (`id`),
  ADD CONSTRAINT `FK_D11814ABFD71BBD3` FOREIGN KEY (`fk_etat_id`) REFERENCES `etat` (`id`);

--
-- Contraintes pour la table `modele`
--
ALTER TABLE `modele`
  ADD CONSTRAINT `FK_10028558297E6E22` FOREIGN KEY (`fk_marque_id`) REFERENCES `marque` (`id`);

--
-- Contraintes pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD CONSTRAINT `FK_292FFF1D1307AF3D` FOREIGN KEY (`fk_carburant_id`) REFERENCES `carburant` (`id`),
  ADD CONSTRAINT `FK_292FFF1D297E6E22` FOREIGN KEY (`fk_marque_id`) REFERENCES `marque` (`id`),
  ADD CONSTRAINT `FK_292FFF1D78B2BEB1` FOREIGN KEY (`fk_client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `FK_292FFF1DCD4D609A` FOREIGN KEY (`fk_modele_id`) REFERENCES `modele` (`id`),
  ADD CONSTRAINT `FK_292FFF1DFD71BBD3` FOREIGN KEY (`fk_etat_id`) REFERENCES `etat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
