<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nome']);
    $bio = trim($_POST['bio']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $avatar_url = trim($_POST['avatar_url']);

    $check = $conn->prepare("SELECT id_usuarios FROM usuarios WHERE nickname = ?");
    $check->bind_param('s', $nickname);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Este usuário já existe!'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (nickname, bio, avatar_url, senha_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $nickname, $bio, $avatar_url, $senha);

    if ($stmt->execute()) {
        $_SESSION['usuario_id'] = $conn->insert_id;
        $_SESSION['nickname'] = $nickname;
        header('Location: feed.php');
        exit;
    } else {
        $erro = 'Erro ao cadastrar: ' . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Kamile - Cadastro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Kamile</h1>
    <div class="card">
        <h2>Registrar</h2>
        <form id="formCadastro" method="POST" novalidate>
            <input type="text" name="nome" id="nome" placeholder="Nickname" required>
            <textarea name="bio" id="bio" placeholder="Biografia (máx. 100 caracteres)" maxlength="100"></textarea>
            <input type="url" name="avatar_url" id="avatar_url" placeholder="Link do Avatar (http...)" required>
            <input type="password" name="senha" id="senha" placeholder="Senha" required>
            <button type="submit">Cadastrar</button>
        </form>
        <a href="index.php">Logar</a>
        <?php if (!empty($erro)) echo '<p class="erro">'.$erro.'</p>'; ?>
    </div>

    <script>
        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            const nickname = document.getElementById('nome').value.trim();
            const bio = document.getElementById('bio').value.trim();
            const avatarUrl = document.getElementById('avatar_url').value.trim();
            const senha = document.getElementById('senha').value.trim();

            // Validação: nickname obrigatório
            if (!nickname) {
                alert('O nickname é obrigatório.');
                e.preventDefault();
                return;
            }

            // Validação: senha obrigatória e mínimo 4 caracteres
            if (senha.length < 4) {
                alert('A senha deve ter pelo menos 4 caracteres.');
                e.preventDefault();
                return;
            }

            // Validação: bio até 100 caracteres
            if (bio.length > 100) {
                alert('A biografia deve ter no máximo 100 caracteres.');
                e.preventDefault();
                return;
            }

            // Validação: avatar_url deve começar com http
            if (avatarUrl && !avatarUrl.startsWith('http')) {
                alert('O link do avatar deve começar com "http" ou "https".');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>