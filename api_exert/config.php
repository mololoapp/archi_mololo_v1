<?php

header("Content-Type: application/json; charset=UTF-8"); // Ici on indique au serveur que le contenu à renvoyer soit du JSON avec un encodage UTF-8 
header("Access-Control-Allow-Origin: *"); // Ici on indique au serveur que l'API accepte les requêtes de n'importe quelle origine pour éviter que certaines IP soient bloquées 
header("Access-Control-Allow-Methods: GET, POST"); // Pour la sécurité, les opérations PUT et DELETE se feront via POST ou GET pour éviter des attaques via requêtes malveillantes



define('PDF_API_KEY','api:sk_ee993e1ee88127e4887acbe28d3b6316166b68ce'); // Ici je vais mettre la cle API utiliser pour le service de generation des PDF via du HTML content 


// Des API vers des ressources ia pour reformuler pour l'utilisateurs certains details
$_REFORM = [
    "https://chat.onestepcom00.workers.dev/chat",
    "https://chat.onestepcom00.workers.dev/chat",
];


// Des API vers des ressources PDF 
define('PDF_API', 'https://api.pdfshift.io/v3/convert/pdf'); // Point de terminaison de l'API de génération des PDF via HTML content


// Configuration des API
$whatsapp_instance_id = 'YOUR_INSTANCE_ID'; // Remplacez par votre instance_id réel
$whatsapp_access_token = 'YOUR_ACCESS_TOKEN'; // Remplacez par votre access_token réel


?>
