<?php

/*
 *
 * 
 * **** **** **** **** **** **** **** **** ****
 * ********************************************
 * Fichier : index.php 
 * Code type : API - PHP backend 
 * Description : Il s'agit du fichier principale qui sera le point d'entrer de l'APi , donc celui va communiquer avec d'autres fichiers et services externes en fin de traiter la demande de l'utiliseur , ce fichier gere les requetes a envoyer a l'ia .
 * Auteur : Exaustan Malka
 * Created Date : 04 juillet 2025 
 * Modified by : Exaustan Malka
 * Modified Date : 04 juillet 2025
 * version : 1.0.1
 * Projet Name : Mololo plus plateform 
 * PHP version : 8.3 
 * MYSQL version : 8.0 
 * **** **** **** **** **** **** **** **** ****
 * ********************************************
*/

/**
 * 
 * Cette partie je vais inclure les fichiers necessaires , la verification est important mais ici je vais pas le faire car je suis sur que les fichiers existent et sont dans le meme dossier que celui-ci
 * 
 */

 require_once 'function.php'; // Ici je vais inclure le fichier qui contient les fonctions necessaires pour l'API
 require_once '../config.php'; // Ici je vais inclure le fichier qui contient les configurations de l'API


/**
 * 
 * Cette partie nous allons mettre l'api en ecoute des requetes GET uniquement , car l'API ne va pas accepter les requetes POST ou PUT ou DELETE pour eviter les attaques malveillantes
 * 
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Ici je vais recuperer le message de l'utilisateur et le endpoint vers lequel la requete sera envoyer
    $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
    $endpoint = getEndpoint($_REFORM); // Ici je vais recuperer un endpoint aleatoire dans la liste des endpoints

    if (!empty($message) && !empty($endpoint)) {
        // Renvoyer un statut code 
        http_response_code(200); // 200 OK

        // Ici je vais envoyer la requete vers l'endpoint avec le message de l'utilisateur
        $response = sendRequest($message, $endpoint);
        echo json_encode(
            ['status' => 'success', 'message' => $response],
            JSON_UNESCAPED_UNICODE
        );
    } else {
        // Renvoyer un statut code 400 Bad Request
        http_response_code(400); 

        echo json_encode(
            ['status' => 'error', 'message' => 'Message or endpoint is missing'],
            JSON_UNESCAPED_UNICODE
        );
    }
} else {
    // Renvoyer un statut code 405 Method Not Allowed
    http_response_code(405); 
    echo json_encode(
        ['status' => 'error', 'message' => 'Invalid request method'],
        JSON_UNESCAPED_UNICODE
    );
}
?>