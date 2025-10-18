<?php
// Configuration SMTP pour envoi d'emails (Brevo example)
$smtp_host = 'smtp-relay.brevo.com';
$smtp_port = 587;
$smtp_username = '9982bc001@smtp-brevo.com';
$smtp_password = 'Tx7m6DtKrjv48q2P';
$smtp_from = $smtp_username; // Valeur utilisée pour l'en-tête From

// NOTE: Ces identifiants sont fournis depuis l'ancien dossier `api_exert`.
// Pour la production, déplacez ces valeurs dans des variables d'environnement
// ou un fichier de configuration sécurisé et évitez de committer des secrets.
?>
