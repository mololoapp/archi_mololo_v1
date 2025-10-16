<?php
// Simple API tester for MoloLo+ (JWT Version)
// - Choose endpoint and method
// - Send request via PHP cURL
// - Persist JWT tokens using cookies.txt in this directory
// - Support for Bearer token authentication

function get_base_url() {
    // Adjust to your local setup if needed
    $candidates = [
        'http://localhost/api',
        'http://localhost/archi_mololo_v1/api',
    ];
    // Allow override via query/env
    if (!empty($_POST['base_url'])) {
        return rtrim($_POST['base_url'], '/');
    }
    foreach ($candidates as $url) {
        return $url; // default to first candidate
    }
}

// JWT Token management
function get_stored_tokens() {
    $tokenFile = __DIR__ . DIRECTORY_SEPARATOR . 'jwt_tokens.json';
    if (!file_exists($tokenFile)) {
        return ['access_token' => '', 'refresh_token' => ''];
    }
    
    $content = file_get_contents($tokenFile);
    $tokens = json_decode($content, true);
    
    if (!$tokens || !is_array($tokens)) {
        return ['access_token' => '', 'refresh_token' => ''];
    }
    
    return [
        'access_token' => $tokens['access_token'] ?? '',
        'refresh_token' => $tokens['refresh_token'] ?? ''
    ];
}

function store_tokens($access_token, $refresh_token) {
    $tokenFile = __DIR__ . DIRECTORY_SEPARATOR . 'jwt_tokens.json';
    $tokens = [
        'access_token' => $access_token,
        'refresh_token' => $refresh_token,
        'stored_at' => date('Y-m-d H:i:s')
    ];
    file_put_contents($tokenFile, json_encode($tokens, JSON_PRETTY_PRINT));
}

function clear_tokens() {
    $tokenFile = __DIR__ . DIRECTORY_SEPARATOR . 'jwt_tokens.json';
    if (file_exists($tokenFile)) {
        unlink($tokenFile);
    }
}

function to_pretty_json($value) {
    if ($value === null || $value === '') {
        return '';
    }
    $decoded = json_decode($value, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    return $value; // not JSON, return raw
}

$cookieFile = __DIR__ . DIRECTORY_SEPARATOR . 'cookies.txt';
$defaultHeaders = [
    'Accept: application/json'
];

$predefined = [
    // ========== ENDPOINTS PUBLICS ==========
    [ 'label' => 'GET /status', 'method' => 'GET', 'path' => '/status', 'ctype' => '' , 'body' => '', 'auth' => false ],
    [ 'label' => 'GET /artistes', 'method' => 'GET', 'path' => '/artistes', 'ctype' => '' , 'body' => '', 'auth' => false ],
    [ 'label' => 'GET /artiste/{id}', 'method' => 'GET', 'path' => '/artiste/1', 'ctype' => '' , 'body' => '', 'auth' => false ],
    [ 'label' => 'GET /opportunites', 'method' => 'GET', 'path' => '/opportunites', 'ctype' => '' , 'body' => '', 'auth' => false ],
    
    // ========== AUTHENTIFICATION JWT ==========
    [ 'label' => 'POST /inscription (x-www-form-urlencoded)', 'method' => 'POST', 'path' => '/inscription', 'ctype' => 'application/x-www-form-urlencoded', 'body' => http_build_query([
        'nom' => 'John Doe',
        'nom_artiste' => 'DJ John',
        'email' => 'john.doe@example.com',
        'numero' => '+33123456789',
        'style_musique' => 'Electronic',
        'password' => 'motdepasse123',
    ]), 'auth' => false ],
    [ 'label' => 'POST /connexion (JWT)', 'method' => 'POST', 'path' => '/connexion', 'ctype' => 'application/x-www-form-urlencoded', 'body' => http_build_query([
        'identifiant' => 'john.doe@example.com',
        'password' => 'motdepasse123',
    ]), 'auth' => false ],
    [ 'label' => 'POST /refresh-token (JWT)', 'method' => 'POST', 'path' => '/refresh-token', 'ctype' => 'application/json', 'body' => json_encode([
        'refresh_token' => '{{REFRESH_TOKEN}}'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => false ],
    [ 'label' => 'POST /deconnexion (JWT)', 'method' => 'POST', 'path' => '/deconnexion', 'ctype' => 'application/json', 'body' => json_encode([
        'refresh_token' => '{{REFRESH_TOKEN}}'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => false ],
    
    // ========== ENDPOINTS PROTÉGÉS (JWT) ==========
    [ 'label' => 'GET /profile (JWT)', 'method' => 'GET', 'path' => '/profile', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'POST /profile (JWT)', 'method' => 'POST', 'path' => '/profile', 'ctype' => 'application/json', 'body' => json_encode([
        'ville' => 'Paris',
        'bio_courte' => 'Artiste électronique passionné',
        'bio_detailles' => 'Plus de 10 ans d\'expérience...',
        'instagram' => '@djjohn',
        'facebook' => 'facebook.com/djjohn',
        'style_musique' => 'Electronic, House',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    [ 'label' => 'PUT /profile (JWT)', 'method' => 'PUT', 'path' => '/profile', 'ctype' => 'application/json', 'body' => json_encode([
        'ville' => 'Lyon',
        'bio_courte' => 'Update bio courte',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    
    [ 'label' => 'GET /epk (JWT)', 'method' => 'GET', 'path' => '/epk', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'POST /epk (JWT)', 'method' => 'POST', 'path' => '/epk', 'ctype' => 'application/json', 'body' => json_encode([
        'nom_artiste' => 'DJ John',
        'genre_musical' => 'Electronic',
        'localisation' => 'Paris, France',
        'biographie' => 'Artiste électronique depuis 2010...',
        'discographie' => 'Album 1: Future Sounds (2020), Single: Night Vibes (2024)',
        'contact' => 'john.doe@example.com',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    
    [ 'label' => 'GET /booking (JWT)', 'method' => 'GET', 'path' => '/booking', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'POST /booking (JWT)', 'method' => 'POST', 'path' => '/booking', 'ctype' => 'application/json', 'body' => json_encode([
        'nom_utilisateur' => 'John Doe',
        'lieux' => 'Club XYZ',
        'adresse' => '123 Rue de la Musique, Paris',
        'montant' => '500€',
        'heure' => '22:00:00',
        'date' => '2024-12-15 22:00:00',
        'message' => 'Soirée électronique, 3h de set',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    
    [ 'label' => 'GET /agenda (JWT)', 'method' => 'GET', 'path' => '/agenda', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'POST /agenda (JWT)', 'method' => 'POST', 'path' => '/agenda', 'ctype' => 'application/json', 'body' => json_encode([
        'nom_concert' => 'Electronic Night',
        'date' => '2024-12-20 21:00:00',
        'heure' => '21:00:00',
        'adresse' => 'Salle Pleyel, Paris',
        'description' => 'Concert électronique avec invités spéciaux',
        'montant' => '50€',
        'nombre_personne' => '500',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    
    [ 'label' => 'GET /galerie (JWT)', 'method' => 'GET', 'path' => '/galerie', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'GET /notifications (JWT)', 'method' => 'GET', 'path' => '/notifications', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'GET /smartlink (JWT)', 'method' => 'GET', 'path' => '/smartlink', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'GET /dashboard (JWT)', 'method' => 'GET', 'path' => '/dashboard', 'ctype' => '', 'body' => '', 'auth' => true ],
    
    // ========== BOOKING ARTISTE ==========
    [ 'label' => 'GET /booking-artiste (JWT)', 'method' => 'GET', 'path' => '/booking-artiste', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'PATCH /booking-artiste/{id}/read (JWT)', 'method' => 'PATCH', 'path' => '/booking-artiste/1/read', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'PATCH /booking-artiste/{id}/status (JWT)', 'method' => 'PATCH', 'path' => '/booking-artiste/1/status', 'ctype' => 'application/json', 'body' => json_encode(['status' => 'accepte'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    
    // ========== BOOKING CLIENT ==========
    [ 'label' => 'GET /booking-client (JWT)', 'method' => 'GET', 'path' => '/booking-client', 'ctype' => '', 'body' => '', 'auth' => true ],
    [ 'label' => 'POST /booking-client (JWT)', 'method' => 'POST', 'path' => '/booking-client', 'ctype' => 'application/json', 'body' => json_encode([
        'artiste_id' => 4,
        'lieux' => 'Club XYZ',
        'adresse' => '123 Rue de la Musique, Paris',
        'montant' => '500€',
        'heure' => '22:00:00',
        'date' => '2024-12-15 22:00:00',
        'message' => 'Soirée électronique, 3h de set'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    [ 'label' => 'PUT /booking-client/{id} (JWT)', 'method' => 'PUT', 'path' => '/booking-client/1', 'ctype' => 'application/json', 'body' => json_encode([
        'lieux' => 'Nouveau Club',
        'montant' => '600€',
        'message' => 'Mise à jour du booking'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'auth' => true ],
    [ 'label' => 'DELETE /booking-client/{id} (JWT)', 'method' => 'DELETE', 'path' => '/booking-client/1', 'ctype' => '', 'body' => '', 'auth' => true ],
];

$responseData = null;
$errorData = null;
$tokens = get_stored_tokens();

// Handle clear tokens request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_tokens'])) {
    clear_tokens();
    $tokens = get_stored_tokens();
    $successMessage = "Tokens JWT effacés avec succès";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $baseUrl = rtrim($_POST['base_url'] ?? get_base_url(), '/');
    $method = strtoupper(trim($_POST['method'] ?? 'GET'));
    $path = '/' . ltrim($_POST['path'] ?? '/status', '/');
    $url = $baseUrl . $path;

    $contentType = trim($_POST['content_type'] ?? '');
    $rawBody = $_POST['body'] ?? '';
    
    // Replace token placeholders
    $rawBody = str_replace('{{REFRESH_TOKEN}}', $tokens['refresh_token'], $rawBody);

    $headers = $defaultHeaders;
    if ($contentType !== '') {
        $headers[] = 'Content-Type: ' . $contentType;
    }
    
    // Add JWT Bearer token for protected endpoints
    $useAuth = $_POST['use_auth'] ?? false;
    if ($useAuth && !empty($tokens['access_token'])) {
        $headers[] = 'Authorization: Bearer ' . $tokens['access_token'];
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);

    if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawBody);
    }

    $rawResponse = curl_exec($ch);
    if ($rawResponse === false) {
        $errorData = 'cURL error: ' . curl_error($ch);
    } else {
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseHeaders = substr($rawResponse, 0, $headerSize);
        $responseBody = substr($rawResponse, $headerSize);
        
        // Auto-extract and store JWT tokens from response
        $responseJson = json_decode($responseBody, true);
        if ($responseJson && isset($responseJson['access_token']) && isset($responseJson['refresh_token'])) {
            store_tokens($responseJson['access_token'], $responseJson['refresh_token']);
            $tokens = get_stored_tokens(); // Update current tokens
            $tokenMessage = "🔑 Tokens JWT mis à jour automatiquement";
        }
        
        $responseData = [
            'request' => [
                'url' => $url,
                'method' => $method,
                'headers' => $headers,
                'body' => $rawBody,
                'auth_used' => $useAuth,
            ],
            'response' => [
                'status' => $statusCode,
                'headers' => $responseHeaders,
                'body_raw' => $responseBody,
                'body_pretty' => to_pretty_json($responseBody),
            ],
        ];
    }
    curl_close($ch);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tester API MoloLo+ (PHP)</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 20px; }
        .row { display: flex; gap: 12px; flex-wrap: wrap; }
        .col { flex: 1 1 300px; min-width: 280px; }
        label { display: block; margin: 8px 0 4px; font-weight: 600; }
        input[type="text"], select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        textarea { min-height: 140px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
        pre { background: #0b1020; color: #e6edf3; padding: 12px; overflow: auto; border-radius: 6px; }
        .hdr { font-size: 18px; margin: 0 0 6px; }
        .btn { padding: 10px 14px; background: #0d6efd; border: none; color: #fff; border-radius: 6px; cursor: pointer; }
        .btn:disabled { opacity: .6; cursor: not-allowed; }
        .note { color: #555; font-size: 13px; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 12px; margin-bottom: 16px; }
    </style>
    <script>
        function loadPreset() {
            var sel = document.getElementById('preset');
            var item = JSON.parse(sel.value);
            document.getElementById('method').value = item.method;
            document.getElementById('path').value = item.path;
            document.getElementById('content_type').value = item.ctype;
            document.getElementById('body').value = item.body;
            document.getElementById('use_auth').checked = item.auth || false;
        }
    </script>
    </head>
<body>
    <h2>Tester API MoloLo+ (JWT Version)</h2>
    <p class="note">Les tokens JWT sont conservés via un fichier <code>jwt_tokens.json</code> dans ce dossier après une <strong>connexion</strong>.</p>
    
    <?php if (isset($successMessage)): ?>
        <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724;">
            <strong>✅ <?php echo htmlspecialchars($successMessage, ENT_QUOTES); ?></strong>
        </div>
    <?php endif; ?>
    
    <?php if (isset($tokenMessage)): ?>
        <div class="card" style="background: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
            <strong><?php echo htmlspecialchars($tokenMessage, ENT_QUOTES); ?></strong>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($tokens['access_token'])): ?>
        <div class="card" style="background: #e8f5e8; border-color: #28a745;">
            <strong>🔑 Tokens JWT actifs :</strong><br>
            <small>Access Token: <?php echo htmlspecialchars(substr($tokens['access_token'], 0, 50) . '...', ENT_QUOTES); ?></small><br>
            <small>Refresh Token: <?php echo htmlspecialchars(substr($tokens['refresh_token'], 0, 50) . '...', ENT_QUOTES); ?></small><br>
            <form method="post" style="margin-top: 10px; display: inline;">
                <button type="submit" name="clear_tokens" class="btn" style="background: #dc3545; font-size: 12px; padding: 5px 10px;">
                    🗑️ Effacer les tokens
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="card" style="background: #fff3cd; border-color: #ffc107;">
            <strong>⚠️ Aucun token JWT actif</strong><br>
            <small>Connectez-vous d'abord pour obtenir des tokens JWT</small>
            <?php if (file_exists(__DIR__ . '/jwt_tokens.json')): ?>
                <br><small style="color: #856404;">Fichier jwt_tokens.json existe mais ne contient pas de tokens valides</small>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="card">
            <div class="row">
                <div class="col">
                    <label for="base_url">Base URL</label>
                    <input type="text" id="base_url" name="base_url" placeholder="http://localhost/api" value="<?php echo htmlspecialchars($_POST['base_url'] ?? get_base_url(), ENT_QUOTES); ?>" />
                </div>
                <div class="col">
                    <label for="preset">Préconfigurations</label>
                    <select id="preset" onchange="loadPreset()">
                        <option value="">— Choisir une requête —</option>
                        <?php foreach ($predefined as $p): ?>
                            <option value='{"method":"<?php echo $p['method']; ?>","path":"<?php echo addslashes($p['path']); ?>","ctype":"<?php echo $p['ctype']; ?>","body":<?php echo json_encode($p['body']); ?>}'><?php echo htmlspecialchars($p['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="row">
                <div class="col">
                    <label for="method">Méthode</label>
                    <select id="method" name="method">
                        <?php foreach (['GET','POST','PUT','PATCH','DELETE'] as $m): ?>
                            <option value="<?php echo $m; ?>" <?php echo (($_POST['method'] ?? '')===$m?'selected':''); ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col">
                    <label for="path">Chemin</label>
                    <input type="text" id="path" name="path" placeholder="/status" value="<?php echo htmlspecialchars($_POST['path'] ?? '/status', ENT_QUOTES); ?>" />
                </div>
                <div class="col">
                    <label for="content_type">Content-Type (optionnel)</label>
                    <input type="text" id="content_type" name="content_type" placeholder="application/json ou application/x-www-form-urlencoded" value="<?php echo htmlspecialchars($_POST['content_type'] ?? '', ENT_QUOTES); ?>" />
                </div>
            </div>
            <label for="body">Corps de la requête</label>
            <textarea id="body" name="body" placeholder="JSON ou form-urlencoded selon Content-Type"><?php echo htmlspecialchars($_POST['body'] ?? '', ENT_QUOTES); ?></textarea>
        </div>

        <div class="card">
            <label>
                <input type="checkbox" id="use_auth" name="use_auth" value="1" <?php echo (($_POST['use_auth'] ?? false) ? 'checked' : ''); ?> />
                Utiliser l'authentification JWT (Bearer Token)
            </label>
            <p class="note">Cochez cette case pour les endpoints protégés. Le token sera automatiquement ajouté à l'en-tête Authorization.</p>
        </div>

        <button type="submit" name="send_request" class="btn">Envoyer la requête</button>
        
        <?php if (empty($tokens['access_token'])): ?>
            <button type="button" onclick="quickConnect()" class="btn" style="background: #28a745; margin-left: 10px;">
                🚀 Connexion rapide
            </button>
        <?php endif; ?>
    </form>
    
    <script>
        function quickConnect() {
            // Auto-fill and submit connection form
            document.getElementById('method').value = 'POST';
            document.getElementById('path').value = '/connexion';
            document.getElementById('content_type').value = 'application/x-www-form-urlencoded';
            document.getElementById('body').value = 'identifiant=john.doe@example.com&password=motdepasse123';
            document.getElementById('use_auth').checked = false;
            document.querySelector('form').submit();
        }
    </script>

    <?php if ($errorData): ?>
        <div class="card">
            <h3 class="hdr">Erreur</h3>
            <pre><?php echo htmlspecialchars($errorData, ENT_QUOTES); ?></pre>
        </div>
    <?php endif; ?>

    <?php if ($responseData): ?>
        <div class="card">
            <h3 class="hdr">Requête</h3>
            <pre><?php echo htmlspecialchars($responseData['request']['method'] . ' ' . $responseData['request']['url'], ENT_QUOTES); ?></pre>
            <pre><?php echo htmlspecialchars(implode("\n", $responseData['request']['headers']), ENT_QUOTES); ?></pre>
            <?php if ($responseData['request']['auth_used']): ?>
                <p><strong>🔑 Authentification JWT utilisée</strong></p>
            <?php endif; ?>
            <?php if (!empty($responseData['request']['body'])): ?>
                <pre><?php echo htmlspecialchars($responseData['request']['body'], ENT_QUOTES); ?></pre>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 class="hdr">Réponse (HTTP <?php echo (int)$responseData['response']['status']; ?>)</h3>
            <pre><?php echo htmlspecialchars($responseData['response']['headers'], ENT_QUOTES); ?></pre>
            <?php if (!empty($responseData['response']['body_pretty'])): ?>
                <pre><?php echo htmlspecialchars($responseData['response']['body_pretty'], ENT_QUOTES); ?></pre>
            <?php else: ?>
                <pre><?php echo htmlspecialchars($responseData['response']['body_raw'], ENT_QUOTES); ?></pre>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</body>
</html>


