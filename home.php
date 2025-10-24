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
<title>Home - Kamile</title>
<style>body { font-family: 'Poppins', sans-serif; background: #f8f9fb; margin:0; color:#333; }

header {
    background:#fff; 
    padding:15px 30px; 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

header h1 {
    color:#e56b70; 
    font-weight:600; 
    margin:0;
}

.nav-bar {
    display:flex; 
    align-items:center; 
    gap:20px;
}

.saudacao {
    font-weight:500;
    color:#333;
}

.menu {
    list-style:none;
    display:flex;
    gap:15px;
    margin:0;
    padding:0;
}

.menu li {
    display:inline;
}

.menu a {
    text-decoration:none;
    color:#e56b70;
    font-weight:500;
    padding:6px 12px;
    border-radius:6px;
    transition:0.3s;
}

.menu a:hover {
    background:#f7d0d2;
}

.btn-sair {
    background:#e56b70;
    color:white !important;
}

.btn-sair:hover {
    background:#d4585d;
}
</style>
</head>
<body>

<header>
    <h1>Kamile</h1>
    <nav class="nav-bar">
        <span class="saudacao">Olá, <?= htmlspecialchars($_SESSION['nickname'] ?? '') ?></span>
        <ul class="menu">
            <li><a href="feed.php">Feed</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="logout.php" class="btn-sair">Sair</a></li>
        </ul>
    </nav>
</header>


<main>
    <h2>Bem-vindo ao Kamile!</h2>
    <p>Use o menu acima para navegar entre seu perfil e o feed.</p>
</main>

</body>
</html>
