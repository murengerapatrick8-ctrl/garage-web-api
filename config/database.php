<?php
// Paramètres de connexion (XAMPP/WAMP par défaut : root sans mot de passe)
const DB_HOST = 'localhost';
const DB_NAME = 'garage_db';
const DB_USER = 'root';
const DB_PASS = '';

function getPDO(): PDO {
    try {
        return new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Connexion à la base impossible']);
        exit;
    }
}
