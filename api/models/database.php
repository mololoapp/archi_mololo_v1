<?php
$host = 'localhost';        // Adresse du serveur MySQL
$dbname = 'nom_de_la_base'; // Nom de la base de données
$username = 'utilisateur';  // Nom d'utilisateur MySQL
$password = 'mot_de_passe'; // Mot de passe MySQL

try {
    // Création de l'objet PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configuration des options PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>