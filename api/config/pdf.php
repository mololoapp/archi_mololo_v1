<?php
// Configuration pour le service de génération de PDF (pdfshift)
// Preferer définir PDF_API_KEY via une variable d'environnement en production.
define('PDF_API', getenv('PDF_API') ?: 'https://api.pdfshift.io/v3/convert/pdf');
define('PDF_API_KEY', getenv('PDF_API_KEY') ?: 'api:sk_ee993e1ee88127e4887acbe28d3b6316166b68ce');

// Dossier relatif où seront stockés les PDF générés (doit être accessible en lecture via le web si vous voulez servir les fichiers)
define('PDF_OUTPUT_DIR', __DIR__ . '/../pdf/tmp/');

// Assurez-vous que le dossier existe
if (!is_dir(PDF_OUTPUT_DIR)) {
    @mkdir(PDF_OUTPUT_DIR, 0755, true);
}

?>
