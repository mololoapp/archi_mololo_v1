<?php
require_once __DIR__ . '/../config/pdf.php';

function pdfshift(string $url) {
    // validation basique
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $payload = json_encode([
        'source' => $url,
        'landscape' => false,
        'use_print' => false
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => PDF_API,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_USERPWD => PDF_API_KEY
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        error_log('pdfshift curl error: ' . $err);
        return false;
    }

    if ($http_code < 200 || $http_code >= 300) {
        error_log('pdfshift http error: ' . $http_code . ' body: ' . substr($response, 0, 200));
        return false;
    }

    $filename = 'epk_' . time() . '_' . rand(100, 99999) . '.pdf';
    $path = rtrim(PDF_OUTPUT_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    $written = @file_put_contents($path, $response);
    if ($written === false) {
        error_log('pdfshift write error: cannot write to ' . $path);
        return false;
    }

    return $path;
}
