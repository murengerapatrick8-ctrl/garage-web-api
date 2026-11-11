<?php
require_once __DIR__ . '/helpers.php';

crud(
    'clients',
    ['nom', 'prenom', 'telephone', 'email'],
    ['nom', 'prenom'],
    'SELECT t.* FROM clients t'
);
