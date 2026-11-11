<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// Corps de la requête : JSON (application/json) ou formulaire classique
function input(): array {
    $json = json_decode(file_get_contents('php://input'), true);
    return is_array($json) ? $json : $_POST;
}

function requireAuth(): void {
    // 🛡️ MODIFIÉ POUR ANDROID : On désactive la vérification de session
    // if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    // if (empty($_SESSION['user_id'])) fail('Authentification requise', 401);
}

/**
 * CRUD générique.
 */
function crud(string $table, array $fields, array $required, string $select, array $filters = []): void {
    // requireAuth(); // 🛡️ COMMENTÉ POUR PERMETTRE À ANDROID DE LIRE LES DONNÉES
    
    $pdo = getPDO();
    $method = $_SERVER['REQUEST_METHOD'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $fetchOne = function (int $id) use ($pdo, $select) {
        $st = $pdo->prepare($select . ' WHERE t.id = ?');
        $st->execute([$id]);
        return $st->fetch();
    };

    try {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $row = $fetchOne($id);
                    $row ? respond($row) : fail('Ressource introuvable', 404);
                }
                $where = []; $params = [];
                foreach ($filters as $param => $col) {
                    if (isset($_GET[$param]) && $_GET[$param] !== '') {
                        $where[] = "$col = ?";
                        $params[] = $_GET[$param];
                    }
                }
                $sql = $select . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY t.id DESC';
                $st = $pdo->prepare($sql);
                $st->execute($params);
                respond($st->fetchAll());

            case 'POST':
                $data = input();
                foreach ($required as $f) {
                    if (!isset($data[$f]) || $data[$f] === '') fail("Champ obligatoire manquant : $f", 422);
                }
                $cols = array_values(array_filter($fields, fn($f) => array_key_exists($f, $data)));
                $sql = "INSERT INTO $table (" . implode(',', $cols) . ') VALUES (' .
                       implode(',', array_fill(0, count($cols), '?')) . ')';
                $pdo->prepare($sql)->execute(array_map(fn($c) => $data[$c], $cols));
                respond($fetchOne((int)$pdo->lastInsertId()), 201);

            case 'PUT':
                if (!$id) fail('Paramètre id requis', 422);
                $data = input();
                $cols = array_values(array_filter($fields, fn($f) => array_key_exists($f, $data)));
                if (!$cols) fail('Aucun champ à modifier', 422);
                $sql = "UPDATE $table SET " . implode(', ', array_map(fn($c) => "$c = ?", $cols)) . ' WHERE id = ?';
                $params = array_map(fn($c) => $data[$c], $cols);
                $params[] = $id;
                $pdo->prepare($sql)->execute($params);
                $row = $fetchOne($id);
                $row ? respond($row) : fail('Ressource introuvable', 404);

            case 'DELETE':
                if (!$id) fail('Paramètre id requis', 422);
                $st = $pdo->prepare("DELETE FROM $table WHERE id = ?");
                $st->execute([$id]);
                $st->rowCount() ? respond(['deleted' => $id]) : fail('Ressource introuvable', 404);

            default:
                fail('Méthode non autorisée', 405);
        }
    } catch (PDOException $e) {
        $driverCode = (int)($e->errorInfo[1] ?? 0);
        if ($driverCode === 1062 && $table === 'vehicules') {
            fail('Cette immatriculation est déjà utilisée par un autre véhicule.', 409);
        }
        if ($driverCode === 1451) {
            $message = [
                'clients' => 'Ce client possède encore des véhicules. Réattribuez ou supprimez ces véhicules avant de supprimer le client.',
                'vehicules' => 'Ce véhicule possède encore des interventions. Supprimez ou réattribuez ces interventions avant de supprimer le véhicule.',
                'mecaniciens' => 'Ce mécanicien est encore affecté à des interventions. Réattribuez ou supprimez ces interventions avant de supprimer le mécanicien.',
            ][$table] ?? 'Cette donnée est encore utilisée par d’autres enregistrements.';
            fail($message, 409);
        }
        if ($driverCode === 1452) {
            $message = [
                'vehicules' => 'Le client sélectionné n’existe plus. Actualisez la page et choisissez un client existant.',
                'interventions' => 'Le véhicule ou le mécanicien sélectionné n’existe plus. Actualisez la page et vérifiez vos sélections.',
            ][$table] ?? 'Une donnée liée n’existe pas. Actualisez la page et vérifiez vos sélections.';
            fail($message, 422);
        }
        if ($e->getCode() === '23000') {
            fail('Cette opération ne respecte pas les contraintes de la base de données. Vérifiez les doublons et les enregistrements liés.', 409);
        }
        fail('Erreur serveur', 500);
    }
}