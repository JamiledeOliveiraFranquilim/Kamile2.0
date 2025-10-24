<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nickname = ?");
    $stmt->bind_param('s', $nome);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id_usuarios'] = $usuario['id'];
        $_SESSION['nickname'] = $usuario['nome'];
        header('Location: home.php');
        exit;
    } else {
        $erro = 'Login inválido!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Kamile - Login</title>
    <style>
        body{
            padding: 200px 200px;
        }
        h1{
            color: #ff6b6b;
            text-align: center;
        }
    </style>
</head>
    <body>
    <h1>Kamile</h1>
        <div class="card">
            <h2>Entrar</h2>
            <form method="POST">
                <input type="nickname" name="nickname" placeholder="Nome" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>
            <a href="cadastro.php">Criar conta</a>
            <?php if (!empty($erro)) echo '<p class="erro">'.$erro.'</p>'; ?>
        </div>
    </body>
</html>