-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 01 oct. 2025 à 11:58
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mololo_plus`
--

-- --------------------------------------------------------

--
-- Structure de la table `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `nom_concert` varchar(200) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `heure` time NOT NULL,
  `description` varchar(5000) NOT NULL,
  `montant` varchar(200) NOT NULL,
  `nombre_personne` varchar(500) NOT NULL,
  `mise_jour` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `artiste`
--

CREATE TABLE `artiste` (
  `id` int(11) NOT NULL,
  `nom` varchar(200) NOT NULL,
  `nom_artiste` varchar(200) NOT NULL,
  `numero` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `style_musique` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `date_inscription` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `nom_utilisateur` varchar(200) NOT NULL,
  `lieux` varchar(200) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `montant` varchar(200) NOT NULL,
  `heure` time NOT NULL,
  `date` datetime NOT NULL,
  `message` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `epk`
--

CREATE TABLE `epk` (
  `id` int(11) NOT NULL,
  `Nom_d'artiste` varchar(200) NOT NULL,
  `Genre_musical` varchar(200) NOT NULL,
  `localisation` varchar(200) NOT NULL,
  `Annees_dactivite` datetime NOT NULL,
  `artiste_model` varchar(5000) NOT NULL,
  `biographie` varchar(5000) NOT NULL,
  `discographie` varchar(5000) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `videos` varchar(100) NOT NULL,
  `presse` varchar(100) NOT NULL,
  `fiche` varchar(100) NOT NULL,
  `conctact` varchar(200) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `galerie`
--

CREATE TABLE `galerie` (
  `id` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `video` varchar(200) NOT NULL,
  `tire` varchar(200) NOT NULL,
  `favorie` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `details` varchar(200) NOT NULL,
  `date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `notification` varchar(200) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `opportunite`
--

CREATE TABLE `opportunite` (
  `id` int(11) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `montant` varchar(200) NOT NULL,
  `date` datetime NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `photo_couverture` varchar(200) NOT NULL,
  `photo_profile` varchar(200) NOT NULL,
  `SmartLink` varchar(200) NOT NULL,
  `ville` varchar(200) NOT NULL,
  `bio_courte` varchar(200) NOT NULL,
  `bio_detailles` varchar(200) NOT NULL,
  `instagram` varchar(200) NOT NULL,
  `tiktok` varchar(200) NOT NULL,
  `twitter` varchar(200) NOT NULL,
  `linkeding` varchar(200) NOT NULL,
  `facebook` varchar(200) NOT NULL,
  `Spotify` varchar(200) NOT NULL,
  `apple_music` varchar(200) NOT NULL,
  `youtube` varchar(200) NOT NULL,
  `Deezer` varchar(200) NOT NULL,
  `Audiomack` varchar(200) NOT NULL,
  `style_musique` varchar(200) NOT NULL,
  `bio` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `session` varchar(200) NOT NULL,
  `ip_adresse` varchar(200) NOT NULL,
  `token` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `smartlink`
--

CREATE TABLE `smartlink` (
  `id` int(11) NOT NULL,
  `smartlink` varchar(200) NOT NULL,
  `smartlink_whatsapp` varchar(200) NOT NULL,
  `smartlink_email` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `token` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `artiste`
--
ALTER TABLE `artiste`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `epk`
--
ALTER TABLE `epk`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `galerie`
--
ALTER TABLE `galerie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `opportunite`
--
ALTER TABLE `opportunite`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `smartlink`
--
ALTER TABLE `smartlink`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `artiste`
--
ALTER TABLE `artiste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `epk`
--
ALTER TABLE `epk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `galerie`
--
ALTER TABLE `galerie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `opportunite`
--
ALTER TABLE `opportunite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `smartlink`
--
ALTER TABLE `smartlink`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
