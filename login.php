<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname']);
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nickname = ?");
    $stmt->bind_param('s', $nickname);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        $_SESSION['usuario_id'] = $usuario['id_usuarios'];
        $_SESSION['nickname'] = $usuario['nickname'];
        $_SESSION['avatar_url'] = $usuario['avatar_url'];

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
</head>
<body>
    <h1>Kamile</h1>
    <div class="card">
        <h2>Entrar</h2>
        <form method="POST">
            <input type="text" name="nickname" placeholder="Nickname" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <a href="cadastro.php">Criar conta</a>

        <?php if (!empty($erro)): ?>
            <div class="erro" style="color: red; margin-top: 10px;">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>