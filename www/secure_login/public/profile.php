<?php
require_once '../src/init_session.php';


// Vérifier si l'utilisateur est connecté en vérifiant la variable de session 'user_id'
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, définir un message de statut et rediriger vers la page de connexion
    $_SESSION['status'] = 'Veuillez vous connecter pour accéder à cette page.';
    header('Location: login.php');
    exit;
}

// Préparer une requête SQL pour obtenir l'email de l'utilisateur connecté depuis la base de données
$stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
</head>
<body>
    <!-- Inclure la barre de navigation -->
    <?php include '../templates/navbar.php'; ?>

    <!-- Afficher les informations de profil de l'utilisateur -->
    <h1>Profil</h1>
    <p>Email : <?= htmlspecialchars($user['email']) ?></p>

    <!-- Ajout d'un bouton pour changer le mot de passe -->
    <a href="change_password.php">Changer le mot de passe</a>
</body>
</html>
