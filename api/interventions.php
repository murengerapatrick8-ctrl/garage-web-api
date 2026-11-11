<?php
require_once __DIR__ . '/helpers.php';

crud(
    'interventions',
    ['vehicule_id', 'mecanicien_id', 'date_intervention', 'description', 'cout', 'statut'],
    ['vehicule_id', 'mecanicien_id', 'date_intervention', 'description'],
    "SELECT t.*, v.immatriculation, v.marque, v.modele,
            CONCAT(m.prenom, ' ', m.nom) AS mecanicien
     FROM interventions t
     JOIN vehicules v ON v.id = t.vehicule_id
     JOIN mecaniciens m ON m.id = t.mecanicien_id"
);
