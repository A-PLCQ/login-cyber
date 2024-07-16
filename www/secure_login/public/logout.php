<?php
require_once '../src/init_session.php';

session_start();
session_destroy();

$_SESSION['status'] = 'Vous avez été déconnecté.';
header('Location: login.php');
exit;
?>
