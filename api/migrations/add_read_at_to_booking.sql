-- Migration: Ajouter la colonne read_at à la table booking
-- Pour pouvoir compter les bookings non lus dans le dashboard

ALTER TABLE `booking` 
ADD COLUMN `read_at` datetime DEFAULT NULL 
AFTER `user_id`;

-- Mettre à jour les bookings existants comme non lus
UPDATE `booking` 
SET `read_at` = NULL 
WHERE `read_at` IS NULL OR `read_at` = '0000-00-00 00:00:00';
