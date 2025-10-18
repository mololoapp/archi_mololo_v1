<?php

/*
 *
 * 
 * **** **** **** **** **** **** **** **** ****
 * ********************************************
 * Fichier : function.php 
 * Code type : API - PHP backend 
 * Description : ce fichier a pour but de contenir toutes les fonctions necessaires pour la gestion de l'API qui vava generer des PDF qui seront stocker dans le dossier tmp
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
 * La fonction pour generer et telecharger les pdf et le placer directement dans le dossier concerner 
 * 
 */

function pdfshift($url){

    /**
     * 
     * Ici nous allons preparer la requete avant qu'il soit envoyer 
     * 
     * 
     */
   
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => PDF_API,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => json_encode(array("source" => $url, "landscape" => false, "use_print" => false)),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        CURLOPT_USERPWD => PDF_API_KEY
    ));

    // Executer la requete 

    $response = curl_exec($curl);

    // Vérifier si la requête a réussi
    if($response !== false){
        // Telecharger le fichier pdf et le placer directement dans le dossier tempory 
        $pdf_edith = PDF_DIR."epk_mololoplus" . rand(100, 99999) . ".pdf";
        file_put_contents($pdf_edith, $response);
        return $pdf_edith;
    }else{
        return false;  
    }

}

?>