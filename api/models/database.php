<?php
$host = 'localhost';        // Adresse du serveur MySQL
$dbname = 'root'; // Nom de la base de données
$username = '';  // Nom d'utilisateur MySQL
$password = ''; // Mot de passe MySQL

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