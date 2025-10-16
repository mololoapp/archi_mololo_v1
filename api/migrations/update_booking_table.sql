-- Migration: Améliorer la table booking pour le système artiste/client
-- Ajouter les colonnes manquantes pour la gestion des statuts et timestamps

-- Ajouter la colonne status pour gérer l'état des bookings
ALTER TABLE `booking` 
ADD COLUMN `status` enum('en_attente','accepte','refuse') NOT NULL DEFAULT 'en_attente' 
AFTER `read_at`;

-- Ajouter les colonnes de timestamp
ALTER TABLE `booking` 
ADD COLUMN `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP 
AFTER `status`;

ALTER TABLE `booking` 
ADD COLUMN `updated_at` datetime DEFAULT NULL 
AFTER `created_at`;

-- Mettre à jour les bookings existants
UPDATE `booking` 
SET `status` = 'en_attente', 
    `created_at` = NOW() 
WHERE `status` IS NULL OR `status` = '';

-- Ajouter des index pour améliorer les performances
ALTER TABLE `booking` 
ADD INDEX `idx_client_id` (`client_id`),
ADD INDEX `idx_user_id` (`user_id`),
ADD INDEX `idx_status` (`status`),
ADD INDEX `idx_date` (`date`);

-- Ajouter des contraintes de clés étrangères si elles n'existent pas
-- (Vérifier d'abord si les contraintes existent)
-- ALTER TABLE `booking` 
-- ADD CONSTRAINT `fk_booking_client` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
-- ADD CONSTRAINT `fk_booking_artiste` FOREIGN KEY (`user_id`) REFERENCES `artiste` (`id`);
