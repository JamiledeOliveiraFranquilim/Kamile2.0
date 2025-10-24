<?php
require 'db/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        $erro = "Erro ao cadastrar.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <title>Cadastrar - Kamile</title>
</head>
<body>
<div class="login-container">
    <h2>Criar conta</h2>
    <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
    <form method="POST">
        <input type="text" name="nome" placeholder="Nome completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Cadastrar</button>
    </form>
    <p>Já tem conta? <a href="login.php">Entrar</a></p>
</div>
</body>
</html>
