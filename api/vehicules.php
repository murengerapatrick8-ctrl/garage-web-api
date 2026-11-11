<?php
require_once __DIR__ . '/helpers.php';

crud(
    'vehicules',
    ['immatriculation', 'marque', 'modele', 'annee', 'kilometrage', 'client_id'],
    ['immatriculation', 'marque', 'modele', 'client_id'],
    "SELECT t.*, CONCAT(c.prenom, ' ', c.nom) AS client
     FROM vehicules t
     JOIN clients c ON c.id = t.client_id"
);
