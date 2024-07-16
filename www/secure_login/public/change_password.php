<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../src/init_session.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['status'] = 'Veuillez vous connecter pour accéder à cette page.';
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['status'] = 'Échec de la vérification CSRF.';
        header('Location: change_password.php');
        exit;
    }

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Validation du nouveau mot de passe
    if (strlen($new_password) < 8) {
        $_SESSION['status'] = 'Le nouveau mot de passe doit comporter au moins 8 caractères.';
        header('Location: change_password.php');
        exit;
    }

    if (!preg_match('/[A-Z]/', $new_password)) {
        $_SESSION['status'] = 'Le nouveau mot de passe doit comporter au moins une majuscule.';
        header('Location: change_password.php');
        exit;
    }

    if (!preg_match('/[\W_]/', $new_password)) {
        $_SESSION['status'] = 'Le nouveau mot de passe doit comporter au moins un caractère spécial.';
        header('Location: change_password.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && password_verify($current_password, $user['password'])) {
        if (password_verify($new_password, $user['password'])) {
            $_SESSION['status'] = 'Le nouveau mot de passe ne peut pas être identique à l\'ancien.';
            header('Location: change_password.php');
            exit;
        }

        $options = ['cost' => 12];
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT, $options);

        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

        $_SESSION['status'] = 'Mot de passe changé avec succès.';
        header('Location: profile.php');
        exit;
    } else {
        $_SESSION['status'] = 'Le mot de passe actuel est incorrect.';
        header('Location: change_password.php');
        exit;
    }
}

// Générer un token CSRF pour protéger contre les attaques CSRF
$csrf_token = generate_csrf_token();

// Vérifier s'il y a un message de statut dans la session et le stocker dans une variable
$status = isset($_SESSION['status']) ? $_SESSION['status'] : '';
unset($_SESSION['status']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changer le mot de passe</title>
</head>
<body>
    <!-- Inclure la barre de navigation -->
    <?php include '../templates/navbar.php'; ?>

    <!-- Afficher le message de statut s'il existe -->
    <?php if ($status): ?>
        <p><?= htmlspecialchars($status) ?></p>
    <?php endif; ?>

    <h2>Changer le mot de passe</h2>
    <!-- Formulaire de changement de mot de passe -->
    <form action="change_password.php" method="POST">
        <!-- Champ caché pour le token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        
        <label for="current_password">Mot de passe actuel:</label>
        <input type="password" id="current_password" name="current_password" required><br><br>

        <label for="new_password">Nouveau mot de passe:</label>
        <input type="password" id="new_password" name="new_password" required><br><br>

        <button type="submit">Changer le mot de passe</button>
    </form>
</body>
</html>
