-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 02 nov. 2026 à 22:45
-- Version du serveur :  10.4.13-MariaDB
-- Version de PHP : 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `garage_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `telephone`, `email`) VALUES
(1, 'Irakoze', 'Doris', '79456789', 'david.irakoze@mail.com'),
(2, 'Uwimana', 'Claire', '79567890', 'claire.uwimana@mail.com'),
(3, 'Bizimana', 'Eric', '79678901', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `interventions`
--

CREATE TABLE `interventions` (
  `id` int(11) NOT NULL,
  `vehicule_id` int(11) NOT NULL,
  `mecanicien_id` int(11) NOT NULL,
  `date_intervention` date NOT NULL,
  `description` text NOT NULL,
  `cout` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` enum('en_attente','en_cours','terminee') NOT NULL DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `interventions`
--

INSERT INTO `interventions` (`id`, `vehicule_id`, `mecanicien_id`, `date_intervention`, `description`, `cout`, `statut`) VALUES
(1, 1, 1, '2026-08-05', 'Vidange et remplacement du filtre à huile', '45000.00', 'terminee'),
(2, 2, 2, '2026-08-18', 'Remplacement de la batterie', '120000.00', 'terminee'),
(3, 3, 3, '2026-09-02', 'Changement des plaquettes de frein', '85000.00', 'terminee'),
(4, 1, 1, '2026-09-20', 'Réparation de la courroie de distribution', '250000.00', 'en_cours'),
(5, 4, 2, '2026-10-01', 'Diagnostic électronique', '30000.00', 'en_attente');

-- --------------------------------------------------------

--
-- Structure de la table `mecaniciens`
--

CREATE TABLE `mecaniciens` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `specialite` varchar(80) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `mecaniciens`
--

INSERT INTO `mecaniciens` (`id`, `nom`, `prenom`, `telephone`, `specialite`, `date_embauche`) VALUES
(1, 'UMUKURU', 'kabura', '79123456', 'Moteur', '2019-03-12'),
(2, 'Niyonkuru', 'Aline', '79234567', 'Électricité', '2021-06-01'),
(3, 'MUREGERA ', 'Pascal', '79345678', 'Freinage et suspension', '2022-09-15');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(2, 'jean', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1H7M3g1T5sV4g3t5T6u7V8w9X0y1Z2a3', 'mecanicien', '2026-11-02 11:06:58'),
(3, 'admin', '$2y$10$8hlEotR/LCBhAu2HqvHFAe6TrAv0vL2Xcj1wbG7f5vzPfcF.ACQDG', 'admin', '2026-11-02 14:11:01');

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL,
  `immatriculation` varchar(20) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `annee` smallint(6) DEFAULT NULL,
  `kilometrage` int(11) DEFAULT 0,
  `client_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `immatriculation`, `marque`, `modele`, `annee`, `kilometrage`, `client_id`) VALUES
(1, 'A1234B', 'Toyota', 'Corolla', 2025, 120000, 1),
(2, 'C5678D', 'Suzuki', 'Swift', 2018, 65000, 3),
(3, 'E1012F', 'Toyota', 'Hilux', 2012, 210000, 3),
(4, 'G3456H', 'Nissan', 'Qashqai', 2019, 48000, 1),
(5, '45samaza ', 'jeep', 'toyota', 2026, 15, 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `interventions`
--
ALTER TABLE `interventions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_interv_vehicule` (`vehicule_id`),
  ADD KEY `fk_interv_mecanicien` (`mecanicien_id`);

--
-- Index pour la table `mecaniciens`
--
ALTER TABLE `mecaniciens`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `immatriculation` (`immatriculation`),
  ADD KEY `fk_vehicule_client` (`client_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `interventions`
--
ALTER TABLE `interventions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `mecaniciens`
--
ALTER TABLE `mecaniciens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `interventions`
--
ALTER TABLE `interventions`
  ADD CONSTRAINT `fk_interv_mecanicien` FOREIGN KEY (`mecanicien_id`) REFERENCES `mecaniciens` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_interv_vehicule` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD CONSTRAINT `fk_vehicule_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
