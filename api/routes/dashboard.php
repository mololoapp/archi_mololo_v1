<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../utils/jwt.php';

// Vérifier l'authentification JWT
$user_id = require_jwt_auth();

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // ========== PROCHAIN CONCERT ==========
    $next_concert = null;
    $stmt = $pdo->prepare("
        SELECT nom_concert, date, adresse, description 
        FROM agenda 
        WHERE user_id = ? AND date > NOW() 
        ORDER BY date ASC 
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $concert = $stmt->fetch();
    
    if ($concert) {
        $next_concert = [
            'title' => $concert['nom_concert'],
            'date' => date('j M', strtotime($concert['date'])),
            'location' => $concert['adresse'],
            'description' => $concert['description']
        ];
    }
    
    // ========== COMPLÉTUDE DU PROFIL ==========
    $profile_completion = calculateProfileCompletion($pdo, $user_id);
    
    // ========== STATISTIQUES EPK ==========
    $epk_stats = calculateEPKStats($pdo, $user_id);
    
    // ========== BOOKINGS NON LUS ==========
    $unread_bookings = 0;
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM booking 
        WHERE user_id = ? AND (read_at IS NULL OR read_at = '0000-00-00 00:00:00')
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    $unread_bookings = (int)$result['count'];
    
    // ========== STATISTIQUES GÉNÉRALES ==========
    $general_stats = calculateGeneralStats($pdo, $user_id);
    
    // ========== RÉPONSE ==========
    echo json_encode([
        'success' => true,
        'data' => [
            'next_concert' => $next_concert,
            'profile_completion' => $profile_completion,
            'epk_stats' => $epk_stats,
            'unread_bookings' => $unread_bookings,
            'general_stats' => $general_stats
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Erreur dashboard : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur'
    ]);
}

// ========== FONCTIONS UTILITAIRES ==========

function calculateProfileCompletion($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();
    
    if (!$profile) {
        return [
            'percentage' => 0,
            'status' => 'À compléter',
            'completed_steps' => 0,
            'total_steps' => 5,
            'steps' => [
                'photo_profile' => false,
                'bio' => false,
                'titles' => false,
                'social_networks' => false,
                'required_fields' => false
            ],
            'message' => 'Ton profil est visible par les pros dès qu\'il atteint 90%'
        ];
    }
    
    $steps = [
        'photo_profile' => !empty($profile['photo_profile']),
        'bio' => !empty($profile['bio_courte']) || !empty($profile['bio_detailles']),
        'titles' => !empty($profile['style_musique']),
        'social_networks' => !empty($profile['instagram']) || !empty($profile['facebook']) || 
                           !empty($profile['twitter']) || !empty($profile['youtube']),
        'required_fields' => !empty($profile['ville']) && !empty($profile['bio_courte'])
    ];
    
    $completed = array_sum($steps);
    $percentage = ($completed / 5) * 100;
    
    $status = $percentage >= 90 ? 'Complet' : ($percentage >= 60 ? 'Bien avancé' : 'À compléter');
    
    return [
        'percentage' => round($percentage),
        'status' => $status,
        'completed_steps' => $completed,
        'total_steps' => 5,
        'steps' => $steps,
        'message' => 'Ton profil est visible par les pros dès qu\'il atteint 90%'
    ];
}

function calculateEPKStats($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM epk WHERE user_id = ? ORDER BY date DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $epk = $stmt->fetch();
    
    if (!$epk) {
        return [
            'percentage' => 0,
            'completed' => 0,
            'total' => 7,
            'status' => 'À créer',
            'steps' => [
                'nom_artiste' => false,
                'genre_musical' => false,
                'biographie' => false,
                'discographie' => false,
                'photo' => false,
                'videos' => false,
                'contact' => false
            ]
        ];
    }
    
    $steps = [
        'nom_artiste' => !empty($epk['Nom_d\'artiste']),
        'genre_musical' => !empty($epk['Genre_musical']),
        'biographie' => !empty($epk['biographie']),
        'discographie' => !empty($epk['discographie']),
        'photo' => !empty($epk['photo']),
        'videos' => !empty($epk['videos']),
        'contact' => !empty($epk['conctact'])
    ];
    
    $completed = array_sum($steps);
    $percentage = ($completed / 7) * 100;
    
    return [
        'percentage' => round($percentage),
        'completed' => $completed,
        'total' => 7,
        'status' => $percentage >= 80 ? 'Complet' : ($percentage >= 50 ? 'En cours' : 'À compléter'),
        'steps' => $steps
    ];
}

function calculateGeneralStats($pdo, $user_id) {
    // Nombre total de concerts
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM agenda WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_concerts = (int)$stmt->fetch()['total'];
    
    // Nombre de concerts à venir
    $stmt = $pdo->prepare("SELECT COUNT(*) as upcoming FROM agenda WHERE user_id = ? AND date > NOW()");
    $stmt->execute([$user_id]);
    $upcoming_concerts = (int)$stmt->fetch()['upcoming'];
    
    // Nombre total de bookings
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM booking WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_bookings = (int)$stmt->fetch()['total'];
    
    // Nombre d'EPK
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM epk WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_epk = (int)$stmt->fetch()['total'];
    
    // Nombre de notifications
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notification WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_notifications = (int)$stmt->fetch()['total'];
    
    return [
        'total_concerts' => $total_concerts,
        'upcoming_concerts' => $upcoming_concerts,
        'total_bookings' => $total_bookings,
        'total_epk' => $total_epk,
        'total_notifications' => $total_notifications
    ];
}
?>
