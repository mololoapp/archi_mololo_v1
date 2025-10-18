<?php

/*
 *
 * 
 * **** **** **** **** **** **** **** **** ****
 * ********************************************
 * Fichier : function.php 
 * Code type : API - PHP backend 
 * Description :ce fichier va contenir toute les fonctions necessaires pour le bon fonctionnnement de l'API qui gere l'IA de l'app .
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
 * cette fonction va prendre en entrer comme parametre un tableau et ce tableau sera tout simplement une liste des endpoint vers lesquels les requetes seront envoyer et la fonction va retourner un endpoint aleatoire dans ce tableau
 * 
 * 
 */
function getEndpoint($endpoints){
    if(is_array($endpoints) && count($endpoints) > 0){
        $randomIndex = array_rand($endpoints);
        return $endpoints[$randomIndex];
    } else {
        return null; // Si le tableau est vide ou n'est pas un tableau, retourner null
    }
}

/**
 * 
 * Cette fonction va gerer l'envoie de la requete vers les endpoint , il va prendre en entrer deux parametres
 * le message et le endpoint dans lequel le message va devoir etre envoyer .
 * 
 * 
 */
function sendRequest($message,$endpoint){
    // Construire l'URL de l'API avec les parametres requis
    $_URL = rtrim($endpoint,'/').'?message='.urlencode($message);

    /**
     * 
     *  Ici nous allons preparer la requete et l'envoyer en utilisant curl et configurer d'autres parametres de la requete comme les entetes et le timeout .
     * 
     * 
     */

     $ch = curl_init();

     curl_setopt($ch, CURLOPT_URL , $_URL);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     //curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 secondes
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
     
     $response = curl_exec($ch);

     // verifier s'il ya erreur
     if(curl_errno($ch)){
        return 'Erreur cURL '.curl_error($ch);
        curl_close($ch);
        // return false;
     }

     // Fermer la session cURL 
     curl_close($ch);

     $re = json_decode($response, true); // Retourner la reponse decodée en tableau associatif

     return $re['message'] ?? $message; // Retourner le message de la reponse ou le message d'origine si la reponse n'est pas valide
     
}
?>