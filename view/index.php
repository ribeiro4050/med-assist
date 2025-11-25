<?php
session_start();
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        include 'home.php';
        break;
    case 'login':
        include 'login.php';
        break;
    case 'historico':
        include 'historico-paciente-view.php';
        break;
    default:
        include '404.php';
        break;
}
?>
