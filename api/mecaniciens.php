<?php
require_once __DIR__ . '/helpers.php';

crud(
    'mecaniciens',
    ['nom', 'prenom', 'telephone', 'specialite', 'date_embauche'],
    ['nom', 'prenom'],
    'SELECT t.* FROM mecaniciens t'
);
