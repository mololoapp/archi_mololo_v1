<?php
// Configuration pour l'envoi WhatsApp via wachap.app proxy
$whatsapp_instance_id = getenv('WHATSAPP_INSTANCE_ID') ?: 'YOUR_INSTANCE_ID';
$whatsapp_access_token = getenv('WHATSAPP_ACCESS_TOKEN') ?: 'YOUR_ACCESS_TOKEN';

// Note: set WHATSAPP_INSTANCE_ID and WHATSAPP_ACCESS_TOKEN in environment for production
?>
