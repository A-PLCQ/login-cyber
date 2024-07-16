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
        header('Location: register.php');
        exit;
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    // Validation du mot de passe
    if (!$email) {
        $_SESSION['status'] = 'Email non valide.';
        header('Location: register.php');
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['status'] = 'Le mot de passe doit comporter au moins 8 caractères.';
        header('Location: register.php');
        exit;
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $_SESSION['status'] = 'Le mot de passe doit comporter au moins une majuscule.';
        header('Location: register.php');
        exit;
    }

    if (!preg_match('/[\W_]/', $password)) {
        $_SESSION['status'] = 'Le mot de passe doit comporter au moins un caractère spécial.';
        header('Location: register.php');
        exit;
    }

    $options = ['cost' => 12];
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->execute([$email, $hashedPassword]);
        $_SESSION['status'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['status'] = "Erreur : L'email est déjà utilisé.";
        } else {
            $_SESSION['status'] = "Erreur lors de l'inscription: " . $e->getMessage();
        }
        header('Location: register.php');
        exit;
    }
}
?>

<?php
require_once '../src/init_session.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
    <title>Inscription</title>
</head>
<body>
    <!-- Inclure la barre de navigation -->
    <?php include '../templates/navbar.php'; ?>

    <!-- Afficher le message de statut s'il existe -->
    <?php if ($status): ?>
        <p><?= htmlspecialchars($status) ?></p>
    <?php endif; ?>

    <h2>Inscription</h2>
    <!-- Formulaire d'inscription -->
    <form action="register.php" method="POST">
        <!-- Champ caché pour le token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="password">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
