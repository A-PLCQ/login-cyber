<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../src/init_session.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier le token CSRF
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $_SESSION['status'] = 'Échec de la vérification CSRF.';
        header('Location: login.php');
        exit;
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $_SESSION['status'] = 'Veuillez fournir un email et un mot de passe valides.';
        header('Location: login.php');
        exit;
    }

    // Vérifier les tentatives de connexion
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE email = ? AND timestamp > (NOW() - INTERVAL 15 MINUTE)');
    $stmt->execute([$email]);
    $attempts = $stmt->fetchColumn();

    if ($attempts >= 5) {
        $_SESSION['status'] = 'Trop de tentatives de connexion échouées. Veuillez réessayer plus tard.';
        header('Location: login.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['status'] = 'Connexion réussie !';
        
        // Réinitialiser les tentatives après un succès
        $stmt = $pdo->prepare('DELETE FROM login_attempts WHERE email = ?');
        $stmt->execute([$email]);

        header('Location: profile.php');
        exit;
    } else {
        // Enregistrer la tentative échouée
        $stmt = $pdo->prepare('INSERT INTO login_attempts (email) VALUES (?)');
        $stmt->execute([$email]);

        $_SESSION['status'] = 'Email ou mot de passe incorrect.';
        header('Location: login.php');
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
    <title>Connexion</title>
</head>
<body>
    <!-- Inclure la barre de navigation -->
    <?php include '../templates/navbar.php'; ?>

    <!-- Afficher le message de statut s'il existe -->
    <?php if ($status): ?>
        <p><?= htmlspecialchars($status) ?></p>
    <?php endif; ?>

    <h2>Connexion</h2>
    <!-- Formulaire de connexion -->
    <form action="login.php" method="POST">
        <!-- Champ caché pour le token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Connexion</button>
    </form>
</body>
</html>
