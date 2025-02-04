-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 04 fév. 2025 à 14:51
-- Version du serveur : 10.11.6-MariaDB-0+deb12u1
-- Version de PHP : 8.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `Locamat`
--

-- --------------------------------------------------------

--
-- Structure de la table `materiel`
--

CREATE TABLE `materiel` (
  `id_materiel` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `version` varchar(15) NOT NULL,
  `ref` varchar(6) NOT NULL,
  `numero_telephone` varchar(10) DEFAULT NULL,
  `categorie` enum('Autres','PC','Téléphone','Tablette','Écran','Disque Dur') NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Déchargement des données de la table `materiel`
--

INSERT INTO `materiel` (`id_materiel`, `nom`, `version`, `ref`, `numero_telephone`, `categorie`, `description`) VALUES
(1, 'Samsung', 'V8.8', 'S8V88', '0610203040', 'Téléphone', 'Un téléphone avec un écran de 5.8\", 4 Go de RAM, 128 Go de Stockage et un rechargement par USB-C.'),
(2, 'PC Gamer', 'V1', 'TEST56', '', 'PC', 'PC avec un Processeur Intel I9, 32GB de RAM DDR5, une carte graphique NVIDIA 4090 - 25GB et 1 TO de SSD'),
(3, 'iPad Pro', 'V3.54', 'IPD001', '', 'Tablette', 'Une tablette avec un écran de 11\", 16 Go de RAM, 128 Go de Stockage et un rechargement par Câble Lightning'),
(4, 'iPhone 14', 'V14.5', 'IPH001', '0645669032', 'Téléphone', 'Une tablette avec un écran de 6.1\", 6 Go de RAM, 128 Go de Stockage et un rechargement par Câble Lightning'),
(5, 'Ecran 4K', 'V4.4E', 'EPC005', '', 'Écran', 'Un écran de 24\" avec un taux de rafraîchissement de 120 Hz et 2 connexion HDMI et DisplayPort'),
(6, 'Disque Dur 1To', 'V1', 'SSD007', '', 'Disque Dur', 'SSD d\'un capacité de 1To compatible USB 3.0 compatible avec Windows, macOS et Linux'),
(7, 'Casque VR Quest 3', 'V3.6', 'CVR003', '', 'Autres', 'Une résolution de 2064 x 2208 px par œil, un taux de rafraîchissement de 90 Hz et une autonomie de 3 h'),
(8, 'AC 130', 'V2.4', 'AC130F', '', 'Autres', 'Avion avec 4 turbopropulseurs Allison T56-A-15 une vitesse de 480 km/h et une autonomie de 4 000 km'),
(9, 'Minitel', 'V0.1', 'MIN389', '', 'PC', 'Sa place est dans un musée !!!\r\n -- Indiana Jones --');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id_reservation` int(11) NOT NULL,
  `id_materiel` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id_reservation`, `id_materiel`, `id_utilisateur`, `date_debut`, `date_fin`) VALUES
(3, 2, 2, '2025-01-12', '2025-01-15'),
(4, 2, 2, '2025-01-23', '2025-02-14'),
(5, 2, 2, '2025-02-15', '2025-02-16'),
(7, 2, 2, '2025-02-17', '2025-02-22'),
(8, 2, 2, '2025-02-23', '2025-02-24'),
(11, 1, 2, '2025-02-02', '2025-02-03'),
(12, 1, 2, '2025-02-04', '2025-02-05'),
(13, 1, 2, '2025-02-06', '2025-02-07'),
(14, 2, 2, '2025-02-25', '2025-02-26'),
(15, 2, 2, '2025-03-27', '2025-03-29');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `matricule` varchar(7) NOT NULL,
  `role` int(11) NOT NULL,
  `password` text NOT NULL,
  `iv` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `matricule`, `role`, `password`, `iv`, `tag`) VALUES
(1, 'ZORZETTO', 'DAMIEN', 'damien@mail.fr', '19E3XB4', 0, '', '', ''),
(2, 'Bayol', 'Romain', 'romain.bayol@icloud.com', 'B27FLI6', 1, 'VG8wcFBBPT0=', 'cXxo+5umEWDEcz/A', 'TX2Vi1nng/VEEFdcrFsNnQ=='),
(4, 'Mahut', 'Vivien', 'mahut.vivien@gmail.com', 'B27FLI5', 1, 'VG8wcFBBPT0=', 'cXxo+5umEWDEcz/A', 'TX2Vi1nng/VEEFdcrFsNnQ=='),
(11, 'Ledys', 'Pierrick', 'pierrick.ledys@gmail.com', 'TGDQ5', 0, 'aUhKU01YbmorMmc9', 'qGt5uRI1QiUvzPPO', 'nRX3WxjYV6Vwy07bC2BJ7Q==');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `materiel`
--
ALTER TABLE `materiel`
  ADD PRIMARY KEY (`id_materiel`),
  ADD UNIQUE KEY `ref` (`ref`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `id_materiel` (`id_materiel`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `adresse_email` (`email`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `materiel`
--
ALTER TABLE `materiel`
  MODIFY `id_materiel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`id_materiel`) REFERENCES `materiel` (`id_materiel`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
