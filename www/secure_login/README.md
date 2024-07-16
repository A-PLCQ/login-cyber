# SecureLogin

## Description
SecureLogin est un système d'authentification sécurisé avec gestion des utilisateurs, changement de mot de passe, et protection contre les attaques par force brute.

## Prérequis
- Serveur WAMP avec PHP et MySQL
- Extensions PHP : PDO, OpenSSL

## Installation
1. **Cloner le dépôt** :
   ```bash
   git clone [URL du dépôt]
   cd secure_login

## Configurer la base de données :
1. Créez une base de données MySQL.
2. Exécutez les requêtes suivantes pour créer les tables nécessaires :

- CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

- CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

## Configuration de l'application

1. Modifiez `db_config.php` avec vos informations de base de données.

## Démarrer le serveur

1. Placez les fichiers dans le répertoire `www` de WAMP.
2. Démarrez WAMP et accédez à [http://localhost/secure_login](http://localhost/secure_login).

## Utilisation

- **Inscription** : Allez sur la page d'inscription, entrez un email et un mot de passe pour créer un compte.
- **Connexion** : Utilisez vos identifiants pour vous connecter.
- **Changer le mot de passe** : Une fois connecté, accédez à la page de changement de mot de passe.
- **Déconnexion** : Cliquez sur le lien de déconnexion dans la barre de navigation.

## Sécurité

- **Protection CSRF** : Des tokens CSRF sont utilisés pour sécuriser les formulaires.
- **Anti-Brute Force** : Les tentatives de connexion échouées sont enregistrées et limitées.
- **Hachage sécurisé des mots de passe** : Les mots de passe sont hachés avec BCRYPT.

## Notes

- Assurez-vous que le serveur WAMP est configuré pour afficher les erreurs pendant le développement.
- Changez les configurations de sécurité pour la production.
