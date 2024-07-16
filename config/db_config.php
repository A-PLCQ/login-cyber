<?php
$host = 'localhost';        // Hôte de la base de données
$db = 'login';       // Nom de la base de données
$user = 'root';             // Nom d'utilisateur de la base de données
$pass = '';                 // Mot de passe de la base de données
$charset = 'utf8mb4';       // Jeu de caractères utilisé pour la connexion

// Construction du Data Source Name (DSN) pour la connexion PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options pour la connexion PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activer les exceptions pour les erreurs PDO
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Définir le mode de récupération par défaut sur associative
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactiver l'émulation des requêtes préparées pour des raisons de sécurité
];

// Créer une instance PDO pour se connecter à la base de données avec les paramètres définis
$pdo = new PDO($dsn, $user, $pass, $options);
?>
