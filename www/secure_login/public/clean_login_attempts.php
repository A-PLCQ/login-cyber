<?php
require_once '../src/init_session.php';
require_once '../src/config.php';

// Supprimer les tentatives de connexion échouées datant de plus d'un jour
$stmt = $pdo->prepare('DELETE FROM login_attempts WHERE timestamp < (NOW() - INTERVAL 1 DAY)');
$stmt->execute();

echo "Nettoyage des tentatives de connexion échouées terminé.";
?>
