<?php 
session_start();
require 'conexao.php';

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
        <p><?php echo "Olá " . $nome?></p>
        <ul>
            <li a href="perfil.php">Post</a></li>
            <li a href="feed.php">Feed</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="logout.php">Sair</a></li>
    </ul>
</nav>
</header>
<body>
    <h1>Bem vindo ao Kamile!</h1>
</body>
</html>