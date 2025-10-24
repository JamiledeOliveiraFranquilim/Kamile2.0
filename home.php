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
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f8f9fb;
    margin: 0;
    color: #333;
}

/* Header */
header {
    background: #fff;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

header h1 {
    color: #e56b70;
    font-weight: 600;
    margin: 0;
}

header nav {
    display: flex;
    align-items: center;
    gap: 20px;
}

header nav span {
    font-weight: 500;
    color: #555;
}

header nav ul {
    list-style: none;
    display: flex;
    gap: 15px;
    margin: 0;
    padding: 0;
}

header nav ul li a {
    text-decoration: none;
    color: #e56b70;
    font-weight: 500;
    transition: 0.3s;
}

header nav ul li a:hover {
    color: #d4585d;
}

/* Main Card */
main {
    max-width: 700px;
    margin: 50px auto;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

main h2 {
    color: #e56b70;
    margin-top: 0;
}

main p {
    font-size: 1rem;
    line-height: 1.6;
}
</style>
</head>
<body>

<header>
    <h1>Kamile</h1>
    <nav>
        <span>Olá, <?= htmlspecialchars($_SESSION['nickname'] ?? '') ?></span>
        <ul>
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
