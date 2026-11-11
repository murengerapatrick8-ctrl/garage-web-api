<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    $pdo = getPDO();
    $totaux = [
        'mecaniciens' => (int)$pdo->query('SELECT COUNT(*) FROM mecaniciens')->fetchColumn(),
        'clients' => (int)$pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn(),
        'vehicules' => (int)$pdo->query('SELECT COUNT(*) FROM vehicules')->fetchColumn(),
        'interventions' => (int)$pdo->query('SELECT COUNT(*) FROM interventions')->fetchColumn(),
        'chiffre_affaires' => (float)$pdo->query(
            "SELECT COALESCE(SUM(cout), 0) FROM interventions WHERE statut = 'terminee'"
        )->fetchColumn(),
    ];

    $statuts = [
        'en_attente' => 0,
        'en_cours' => 0,
        'terminee' => 0,
    ];
    $rows = $pdo->query('SELECT statut, COUNT(*) AS total FROM interventions GROUP BY statut')->fetchAll();
    foreach ($rows as $row) {
        if (array_key_exists($row['statut'], $statuts)) {
            $statuts[$row['statut']] = (int)$row['total'];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'totaux' => $totaux,
            'statuts' => $statuts,
        ],
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Impossible de charger les statistiques.',
    ], JSON_UNESCAPED_UNICODE);
}
