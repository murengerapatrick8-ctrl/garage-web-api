<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');
$password = (string)($data['password'] ?? '');

if ($username === '' || $password === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Identifiants requis']);
    exit;
}

// Connexion à la base (adaptez si besoin)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=garage_db;charset=utf8mb4', 'root', '');
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur BDD']);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
    exit;
}

// ON RENVOIE EXACTEMENT LE FORMAT QUE L'APPLICATION ATTEND
echo json_encode([
    'success' => true,
    'data' => [
        'user' => [
            'id' => (int)$user['id'],
            'username' => $user['username']
        ]
    ]
]);