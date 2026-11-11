# Gestion de garage : application web et API REST

Solution de gestion d'un garage automobile : une application web et une API REST en PHP (réponses JSON) connectées à une base MySQL. Cette API est consommée par une application Android (Java, Retrofit).

## Fonctionnalités
- Authentification
- Gestion des mécaniciens, des clients, des véhicules et des interventions (réparations)
- Statistiques d'activité

## Technologies
PHP · MySQL · API REST (JSON) · HTML/CSS

## Installation
1. Installer XAMPP et démarrer **Apache** et **MySQL**.
2. Copier le dossier du projet dans `C:\xampp\htdocs\GARAGE`.
3. Ouvrir phpMyAdmin, créer la base `garage_db` et importer le fichier `garage_db.sql`.
4. Configuration par défaut (utilisateur `root`, sans mot de passe) dans `config/database.php` : à modifier si votre MySQL est différent.
5. Ouvrir `http://localhost/GARAGE/` dans le navigateur.

## Application mobile
L'application Android qui utilise cette API est dans un dépôt séparé. Elle doit pointer vers l'adresse IP de votre PC (`BASE_URL`) sur le même réseau.

## Auteur
MURENGERA Patrick · github.com/murengerapatrick8-ctrl
