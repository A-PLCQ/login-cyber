<?php
// Fonction pour limiter les tentatives de connexion
function record_failed_login($pdo, $email) {
    $stmt = $pdo->prepare('INSERT INTO login_attempts (email, timestamp) VALUES (?, NOW())');
    $stmt->execute([$email]);
}

function count_recent_failed_logins($pdo, $email) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE email = ? AND timestamp > (NOW() - INTERVAL 15 MINUTE)');
    $stmt->execute([$email]);
    return $stmt->fetchColumn();
}

function clear_old_login_attempts($pdo) {
    $stmt = $pdo->prepare('DELETE FROM login_attempts WHERE timestamp < (NOW() - INTERVAL 1 DAY)');
    $stmt->execute();
}

// Fonction pour générer un token CSRF
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fonction pour vérifier un token CSRF
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fonction pour rediriger l'utilisateur avec un message de statut
function redirect_with_message($location, $message) {
    $_SESSION['status'] = $message;
    header("Location: $location");
    exit;
}
?>
