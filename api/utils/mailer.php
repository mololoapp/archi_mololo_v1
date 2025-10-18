<?php
// Simple mailer utility ported from api_exert
function sendEmailSMTP($to, $subject, $message, $from, $smtp_host = null, $smtp_port = null, $username = null, $password = null) {
    // For now we use PHP mail() with headers. If SMTP auth is required,
    // consider using PHPMailer or SwiftMailer in the future.
    $headers = "From: {$from}\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $ok = mail($to, $subject, $message, $headers);
    if ($ok) return ['success' => true, 'message' => 'Email envoyé'];
    return ['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'email'];
}
