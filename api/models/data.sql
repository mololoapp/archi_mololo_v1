-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 16 oct. 2025 à 23:18
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mololo`
--

-- --------------------------------------------------------

--
-- Structure de la table `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `nom_concert` varchar(200) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `heure` time NOT NULL,
  `description` varchar(5000) NOT NULL,
  `montant` varchar(200) NOT NULL,
  `nombre_personne` varchar(500) NOT NULL,
  `mise_jour` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agenda`
--

INSERT INTO `agenda` (`id`, `user_id`, `date`, `nom_concert`, `adresse`, `heure`, `description`, `montant`, `nombre_personne`, `mise_jour`) VALUES
(1, 4, '2024-12-20 21:00:00', 'Electronic Night', 'Salle Pleyel, Paris', '21:00:00', 'Concert électronique avec invités spéciaux', '50€', '500', '2025-10-08 12:24:51'),
(2, 5, '2024-12-20 21:00:00', 'Electronic', 'Salle Pleyel, Paris', '21:00:00', 'Concert électronique avec invités spéciaux', '50€', '500', '2025-10-13 12:50:02');

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

--
-- Déchargement des données de la table `artiste`
--

INSERT INTO `artiste` (`id`, `nom`, `nom_artiste`, `numero`, `email`, `style_musique`, `password`, `date_inscription`) VALUES
(1, 'jhon', 'dj jhon', '+33123456789', 'jhondoe@example.com', 'electronic', '$2y$10$gtMoRJ3ZSkpeEswkwDbdludOoRFqm9.h2HZhUTF82/.JrLCuBS0wW', '2025-10-02 20:54:09'),
(2, 'Jn Doe', 'DJ John', '+32123456789', 'doe@example.com', 'Electronic', '$2y$10$Omu4AQfKIsaZNRMeWr8J0epId9NB3bmbSDoR4b.fzsZwIRddFn3wa', '2025-10-06 19:10:35'),
(3, 'GBEL', 'SALVA', '+34123456789', 'GB@example.com', 'Electronic', '$2y$10$mbwuZb5hZxVJcxxlBwsNSu06AIej08AOSPKWCRlYQxZMN4.6muVqe', '2025-10-06 19:22:59'),
(4, 'John Doe', 'DJ John', '+33123456789', 'john.doe@example.com', 'Electronic', '$2y$10$/9NpViQVSriHo4jA1zPI0uw5ViaCcE4MXf6cFUTyx9JJHTp9ARzEC', '2025-10-08 12:20:50'),
(5, 'papa', 'gedeon', '+35123456789', 'papa@example.com', 'Electronic', '$2y$10$zQLL3nqixcDo4lGQb47S1eGT71HNWzXPkd75/G.lW5S6zXGCdgP5m', '2025-10-08 12:39:21');

-- --------------------------------------------------------

--
-- Structure de la table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `nom_utilisateur` varchar(200) NOT NULL,
  `lieux` varchar(200) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `montant` varchar(200) NOT NULL,
  `heure` time NOT NULL,
  `date` datetime NOT NULL,
  `message` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` datetime DEFAULT NULL,
  `status` enum('en_attente','accepte','refuse') NOT NULL DEFAULT 'en_attente',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `booking`
--

INSERT INTO `booking` (`id`, `client_id`, `nom_utilisateur`, `lieux`, `adresse`, `montant`, `heure`, `date`, `message`, `user_id`, `read_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'John Doe', 'Club XYZ', '123 Rue de la Musique, Paris', '500€', '22:00:00', '2024-12-15 22:00:00', 'Soirée électronique, 3h de set', 4, NULL, 'en_attente', '2025-10-16 23:17:27', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `epk`
--

CREATE TABLE `epk` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
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

--
-- Déchargement des données de la table `epk`
--

INSERT INTO `epk` (`id`, `user_id`, `Nom_d'artiste`, `Genre_musical`, `localisation`, `Annees_dactivite`, `artiste_model`, `biographie`, `discographie`, `photo`, `videos`, `presse`, `fiche`, `conctact`, `date`) VALUES
(1, 4, 'DJ John', 'Electronic', 'Paris, France', '0000-00-00 00:00:00', '', 'Artiste électronique depuis 2010...', 'Album 1: Future Sounds (2020), Single: Night Vibes (2024)', '', '', '', '', 'john.doe@example.com', '2025-10-09 11:08:26');

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
  `date` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jwt_refresh_tokens`
--

CREATE TABLE `jwt_refresh_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jwt_refresh_tokens`
--

INSERT INTO `jwt_refresh_tokens` (`id`, `user_id`, `token_hash`, `expires_at`, `revoked`, `created_at`) VALUES
(1, 4, '8dcefb6cfad109dd4065df87957a27ee5aac8edabfe3438517cdd3bda52864b8', '2025-11-12 19:43:57', 0, '2025-10-13 19:43:57'),
(2, 4, 'fb319b5f1e8336d1c49eab5acf21fc0768e35062fa34e781be6f309ac34ddafd', '2025-11-12 22:14:06', 0, '2025-10-13 22:14:06'),
(3, 4, 'c2efebf42fdaf1abd4b518383f4f0e5f81606d28c18a86ed90288b74c3c81fec', '2025-11-12 22:15:45', 0, '2025-10-13 22:15:45'),
(4, 4, '22caee487c44f6fc481b54b2e355ff51bad37e3bee0ab23650df5602a3bfcd3f', '2025-11-12 22:16:34', 0, '2025-10-13 22:16:34'),
(5, 4, '3d7667a4263b386b5007fb26c028f3e1a81d472d0b60ddbefc417a2e782e8b1a', '2025-11-12 22:21:25', 0, '2025-10-13 22:21:25');

-- --------------------------------------------------------

--
-- Structure de la table `jwt_refresh_tokens_user`
--

CREATE TABLE `jwt_refresh_tokens_user` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jwt_refresh_tokens_user`
--

INSERT INTO `jwt_refresh_tokens_user` (`id`, `user_id`, `token_hash`, `expires_at`, `revoked`, `created_at`) VALUES
(1, 4, '811e4cfd2fde128190896eec1da0bc595136beb5eddc5b975e37f2018bccd3a7', '2025-11-15 10:33:54', 0, '2025-10-16 10:33:54'),
(2, 4, '270b7d132845de9172e29ea68b58a85bf6ac4e64e2b39fb83103c02163970bd2', '2025-11-15 13:53:26', 0, '2025-10-16 13:53:26');

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `notification` varchar(200) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL
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
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `channel` enum('email','sms') NOT NULL,
  `salt` varbinary(32) NOT NULL,
  `otp_hash` char(64) NOT NULL,
  `otp_expires_at` datetime NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `consumed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `channel`, `salt`, `otp_hash`, `otp_expires_at`, `attempts`, `consumed`, `created_at`) VALUES
(1, 4, 'email', 0x228677ecb299d1db6b91be59c1d4f0dc, '6b93d6ccdc4b0fbd87fc93994a4945e5e3f5559da2784b1850833edf670b6df6', '2025-10-10 14:29:57', 1, 0, '2025-10-10 14:19:57'),
(2, 4, 'email', 0x87c16023b802393edc048b5e323d8ff1, 'fb99429491577b74a7335eb274a26940dbe2f0cc90377fb01e64de835bed49f5', '2025-10-10 14:41:41', 0, 0, '2025-10-10 14:31:41');

-- --------------------------------------------------------

--
-- Structure de la table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
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

--
-- Déchargement des données de la table `profile`
--

INSERT INTO `profile` (`id`, `user_id`, `photo_couverture`, `photo_profile`, `SmartLink`, `ville`, `bio_courte`, `bio_detailles`, `instagram`, `tiktok`, `twitter`, `linkeding`, `facebook`, `Spotify`, `apple_music`, `youtube`, `Deezer`, `Audiomack`, `style_musique`, `bio`) VALUES
(1, 4, '', '', '', 'pekin', 'Update bio courte', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(2, 5, '', '', '', 'Lyon', 'Update bio courte', '', '', '', '', '', '', '', '', '', '', '', '', '');

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
  `smartlink_email` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `token` varchar(500) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `numero`, `password`, `created_at`) VALUES
(1, 'John Doe', 'DJ John', 'john.doe@example.com', '+33123456789', '$2y$10$AgHBU.YM5N0ByxKHjBgDrOhAKBT5XS3cY3vlxLkJLR99MoyD3qkuG', '2025-10-16 07:29:17');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`date`);

--
-- Index pour la table `epk`
--
ALTER TABLE `epk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_epk` (`user_id`);

--
-- Index pour la table `galerie`
--
ALTER TABLE `galerie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_galerie` (`user_id`);

--
-- Index pour la table `jwt_refresh_tokens`
--
ALTER TABLE `jwt_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `jwt_refresh_tokens_user`
--
ALTER TABLE `jwt_refresh_tokens_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_hash` (`token_hash`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_notification` (`user_id`);

--
-- Index pour la table `opportunite`
--
ALTER TABLE `opportunite`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_profil` (`user_id`);

--
-- Index pour la table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `smartlink`
--
ALTER TABLE `smartlink`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_smartlink` (`user_id`);

--
-- Index pour la table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_token` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `artiste`
--
ALTER TABLE `artiste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `epk`
--
ALTER TABLE `epk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `galerie`
--
ALTER TABLE `galerie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jwt_refresh_tokens`
--
ALTER TABLE `jwt_refresh_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `jwt_refresh_tokens_user`
--
ALTER TABLE `jwt_refresh_tokens_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_user_booking` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`);

--
-- Contraintes pour la table `epk`
--
ALTER TABLE `epk`
  ADD CONSTRAINT `fk_user_epk` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`);

--
-- Contraintes pour la table `galerie`
--
ALTER TABLE `galerie`
  ADD CONSTRAINT `fk_user_galerie` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`);

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_user_notification` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`);

--
-- Contraintes pour la table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `fk_user_profil` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `smartlink`
--
ALTER TABLE `smartlink`
  ADD CONSTRAINT `fk_user_smartlink` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`);

--
-- Contraintes pour la table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_user_token` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
