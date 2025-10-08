<?php
// Simple API tester for MoloLo+
// - Choose endpoint and method
// - Send request via PHP cURL
// - Persist session using cookies.txt in this directory

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
    [ 'label' => 'GET /status', 'method' => 'GET', 'path' => '/status', 'ctype' => '' , 'body' => '' ],
    [ 'label' => 'GET /artistes', 'method' => 'GET', 'path' => '/artistes', 'ctype' => '' , 'body' => '' ],
    [ 'label' => 'GET /artiste/{id}', 'method' => 'GET', 'path' => '/artiste/1', 'ctype' => '' , 'body' => '' ],
    [ 'label' => 'POST /inscription (x-www-form-urlencoded)', 'method' => 'POST', 'path' => '/inscription', 'ctype' => 'application/x-www-form-urlencoded', 'body' => http_build_query([
        'nom' => 'John Doe',
        'nom_artiste' => 'DJ John',
        'email' => 'john.doe@example.com',
        'numero' => '+33123456789',
        'style_musique' => 'Electronic',
        'password' => 'motdepasse123',
    ]) ],
    [ 'label' => 'POST /connexion (x-www-form-urlencoded)', 'method' => 'POST', 'path' => '/connexion', 'ctype' => 'application/x-www-form-urlencoded', 'body' => http_build_query([
        'identifiant' => 'john.doe@example.com',
        'password' => 'motdepasse123',
    ]) ],
    [ 'label' => 'POST /deconnexion', 'method' => 'POST', 'path' => '/deconnexion', 'ctype' => 'application/json', 'body' => '' ],
    [ 'label' => 'GET /profile', 'method' => 'GET', 'path' => '/profile', 'ctype' => '', 'body' => '' ],
    [ 'label' => 'POST /profile (JSON)', 'method' => 'POST', 'path' => '/profile', 'ctype' => 'application/json', 'body' => json_encode([
        'ville' => 'Paris',
        'bio_courte' => 'Artiste électronique passionné',
        'bio_detailles' => 'Plus de 10 ans dexpérience...',
        'instagram' => '@djjohn',
        'facebook' => 'facebook.com/djjohn',
        'style_musique' => 'Electronic, House',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ],
    [ 'label' => 'PUT /profile (JSON)', 'method' => 'PUT', 'path' => '/profile', 'ctype' => 'application/json', 'body' => json_encode([
        'ville' => 'Lyon',
        'bio_courte' => 'Update bio courte',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ],
    [ 'label' => 'POST /epk (JSON)', 'method' => 'POST', 'path' => '/epk', 'ctype' => 'application/json', 'body' => json_encode([
        'nom_artiste' => 'DJ John',
        'genre_musical' => 'Electronic',
        'localisation' => 'Paris, France',
        'biographie' => 'Artiste électronique depuis 2010...',
        'discographie' => 'Album 1: Future Sounds (2020), Single: Night Vibes (2024)',
        'contact' => 'john.doe@example.com',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ],
    [ 'label' => 'GET /booking', 'method' => 'GET', 'path' => '/booking', 'ctype' => '', 'body' => '' ],
    [ 'label' => 'POST /booking (JSON)', 'method' => 'POST', 'path' => '/booking', 'ctype' => 'application/json', 'body' => json_encode([
        'nom_utilisateur' => 'John Doe',
        'lieux' => 'Club XYZ',
        'adresse' => '123 Rue de la Musique, Paris',
        'montant' => '500€',
        'heure' => '22:00:00',
        'date' => '2024-12-15 22:00:00',
        'message' => 'Soirée électronique, 3h de set',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ],
    [ 'label' => 'GET /agenda', 'method' => 'GET', 'path' => '/agenda', 'ctype' => '', 'body' => '' ],
    [ 'label' => 'POST /agenda (JSON)', 'method' => 'POST', 'path' => '/agenda', 'ctype' => 'application/json', 'body' => json_encode([
        'nom_concert' => 'Electronic Night',
        'date' => '2024-12-20 21:00:00',
        'heure' => '21:00:00',
        'adresse' => 'Salle Pleyel, Paris',
        'description' => 'Concert électronique avec invités spéciaux',
        'montant' => '50€',
        'nombre_personne' => '500',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ],
];

$responseData = null;
$errorData = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $baseUrl = rtrim($_POST['base_url'] ?? get_base_url(), '/');
    $method = strtoupper(trim($_POST['method'] ?? 'GET'));
    $path = '/' . ltrim($_POST['path'] ?? '/status', '/');
    $url = $baseUrl . $path;

    $contentType = trim($_POST['content_type'] ?? '');
    $rawBody = $_POST['body'] ?? '';

    $headers = $defaultHeaders;
    if ($contentType !== '') {
        $headers[] = 'Content-Type: ' . $contentType;
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
        $responseData = [
            'request' => [
                'url' => $url,
                'method' => $method,
                'headers' => $headers,
                'body' => $rawBody,
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
        }
    </script>
    </head>
<body>
    <h2>Tester API MoloLo+ (PHP)</h2>
    <p class="note">La session est conservée via un fichier <code>cookies.txt</code> dans ce dossier après une <strong>connexion</strong>.</p>

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

        <button type="submit" name="send_request" class="btn">Envoyer la requête</button>
    </form>

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


