<?php
require_once '../src/init_session.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil SecureLogin</title>
</head>
<body>
    <?php include '../templates/navbar.php'; ?>

    <h1>Bienvenue sur SecureLogin</h1>

    <?php
    // Affiche un message de statut s'il y en a un dans la session
    if (isset($_SESSION['status'])) {
        echo "<p>" . htmlspecialchars($_SESSION['status']) . "</p>";
        unset($_SESSION['status']);  // Effacer le message après l'affichage
    }
    ?>
</body>
</html>
