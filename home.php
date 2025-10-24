<?php 
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuarios'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Home</title>
</head>
<header>
    <nav>
        <h1>Kamile</h1>
        <?php if ($_SESSION['id_usuarios']): ?>
            <span>Olá, <?= htmlspecialchars($_SESSION['nickname'] ?? '') ?></span>
        <?php else: ?>
        <ul>
            <li a href="perfil.php">Post</a></li>
            <li a href="feed.php">Feed</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="logout.php">Sair</a></li>
    </ul>
</nav>
</header>
<body>
</body>
</html>