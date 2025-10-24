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
<body>
<header>
    <nav>
        <h1>Kamile</h1>
        <span>Olá, <?= htmlspecialchars($_SESSION['nickname'] ?? '') ?></span>
        <ul>
            <li><a href="perfil.php">Post</a></li>
            <li><a href="feed.php">Feed</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2>Bem-vindo ao Kamile!</h2>
    <p>Use o menu acima para navegar entre seu perfil e o feed.</p>
</main>
</body>
</html>