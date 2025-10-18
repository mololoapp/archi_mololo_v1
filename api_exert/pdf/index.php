<?php

/*
 *
 * 
 * **** **** **** **** **** **** **** **** ****
 * ********************************************
 * Fichier : index.php 
 * Code type : API - PHP backend 
 * Description : Ce fichier sera le point d'entrée de l'API , il va gerer les requetes de generation d'API 
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
 * Nous allons faire appel aux fichiers de configurations et des fonctions 
 * 
 */
require_once '../config.php';
require_once 'function.php';

/** 
 * 
 * Nous allons definir les constantes qui seront demander pour etre utiliser par l'API 
 * 
 */
define('PDF_DIR','tmp/');

/**
 * 
 * Ici nous allons mettre l'API en ecoute des requetes HTTP GET entrant 
 * 
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Récupération des paramètres de la requête
    if(isset($_GET['url'])){
        // Extraire l'URL du paramètre URL
        $_url = $_GET['url'];
        // Vérifier si l'URL est valide
        if (filter_var($_url, FILTER_VALIDATE_URL)) {
            // generer et telecharger le pdf 
            $pdf_name = pdfshift($_url);

            // Mettre le pdf a la disposition de l'equipe
            http_response_code(200);
            echo json_encode(
                ['status' => 'success','message' => 'PDF génerer avec succès , votre fichier PDF est desormais pret a etre telecharger','file' => $pdf_name],
                JSON_UNESCAPED_UNICODE
            );
        }else{
            // URL est malformater
            http_response_code(400);
            echo json_encode(
                ['status' => 'error','message' => 'Veuillez entrer une URL correct'],
                JSON_UNESCAPED_UNICODE
            );
        }   
    }else{
        // Erreur de parametre url 
        http_response_code(400);
        echo json_encode(
            ['status' => 'error','Veuillez entrer un parametre valide'],
            JSON_UNESCAPED_UNICODE
        );
    }
}else{
    // Renvoie une erreur et Statut d'erreur
    http_response_code(405);
    echo json_encode(
        ['status' => 'error', 'message' => 'Méthode non autorisée'],
        JSON_UNESCAPED_UNICODE
    );
}

?>