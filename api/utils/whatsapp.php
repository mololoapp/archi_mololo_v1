<?php
require_once __DIR__ . '/../config/whatsapp.php';

function sendWhatsAppMessage(string $number, string $message, ?string $instance_id = null, ?string $access_token = null) {
    $instance_id = $instance_id ?: $GLOBALS['whatsapp_instance_id'] ?? null;
    $access_token = $access_token ?: $GLOBALS['whatsapp_access_token'] ?? null;

    if (!$instance_id || !$access_token) {
        error_log('WhatsApp config missing');
        return false;
    }

    $url = 'https://wachap.app/api/send?number=' . urlencode($number) . '&type=text&message=' . urlencode($message) . '&instance_id=' . urlencode($instance_id) . '&access_token=' . urlencode($access_token);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        error_log('WhatsApp curl error: ' . $err);
        return false;
    }

    $decoded = json_decode($response, true);
    return $decoded ?: ['http_code' => $http_code, 'body' => $response];
}
