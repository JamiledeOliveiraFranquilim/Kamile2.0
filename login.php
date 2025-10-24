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
        $_SESSION['id_usuario'] = $usuario['id_usuarios'];
        $_SESSION['nickname'] = $usuario['nickname'];
        $_SESSION['avatar_url'] = $usuario['avatar_url'];
        header('Location: feed.php');
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
    <title>Kamile - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="left-side">
            <h1>🌸 Kamile</h1>
            <p>Entre no mundo artístico de Kamile.<br>Compartilhe momentos, inspire e conecte-se!</p>
            <img src="https://i.ibb.co/2FsfXqM/art-illustration.png" alt="Arte" class="art-image">
        </div>

        <div class="right-side">
            <div class="card">
                <h2>Entrar</h2>
                <form method="POST">
                    <div class="input-group">
                        <i class="fa fa-user"></i>
                        <input type="text" name="nickname" placeholder="Nickname" required>
                    </div>
                    <div class="input-group">
                        <i class="fa fa-lock"></i>
                        <input type="password" name="senha" placeholder="Senha" required>
                    </div>
                    <button type="submit">Entrar</button>
                </form>
                <a href="cadastrar.php">Criar conta</a>

                <?php if (!empty($erro)): ?>
                    <div class="erro"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<script>
        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            const nickname = document.getElementById('nome').value.trim();
            const bio = document.getElementById('bio').value.trim();
            const avatarUrl = document.getElementById('avatar_url').value.trim();
            const senha = document.getElementById('senha').value.trim();

            if (!nickname) {
                alert('O nickname é obrigatório.');
                e.preventDefault();
                return;
            }
            if (senha.length < 4) {
                alert('A senha deve ter pelo menos 4 caracteres.');
                e.preventDefault();
                return;
            }
            if (bio.length > 100) {
                alert('A biografia deve ter no máximo 100 caracteres.');
                e.preventDefault();
                return;
            }
            if (avatarUrl && !avatarUrl.startsWith('http')) {
                alert('O link do avatar deve começar com "http" ou "https".');
                e.preventDefault();
                return;
            }
        });
    </script>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f8f9fb;
    min-height: 100vh;
}

/* Container principal */
.container {
    display: flex;
    min-height: 100vh;
}

/* Lado esquerdo artístico */
.left-side {
    flex: 1;
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
}

.left-side h1 {
    font-size: 3rem;
    margin-bottom: 20px;
}

.left-side p {
    font-size: 1.2rem;
    margin-bottom: 30px;
}

.left-side .art-image {
    max-width: 100%;
    border-radius: 12px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

/* Lado direito formulário */
.right-side {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fb;
}

/* Card de login */
.card {
    background: white;
    padding: 40px 30px;
    border-radius: 12px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
}

.card h2 {
    margin-bottom: 25px;
    color: #333;
}

/* Inputs com ícones */
.input-group {
    position: relative;
    margin-bottom: 20px;
}

.input-group i {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    color: #e56b70;
}

.input-group input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: #e56b70;
    outline: none;
}

/* Botão de login */
.card button {
    width: 100%;
    padding: 12px;
    background: #e56b70;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
}

.card button:hover {
    background: #d4585d;
}

/* Link de cadastro */
.card a {
    display: block;
    margin-top: 15px;
    color: #e56b70;
    text-decoration: none;
    transition: 0.3s;
}

.card a:hover {
    text-decoration: underline;
}

/* Mensagem de erro */
.erro {
    margin-top: 10px;
    color: red;
    font-weight: 500;
}

/* Responsivo */
@media screen and (max-width: 900px) {
    .container {
        flex-direction: column;
    }
    .left-side, .right-side {
        flex: unset;
        width: 100%;
    }
    .left-side {
        padding: 30px 20px;
    }
    .right-side {
        padding: 30px 20px;
    }
}
</style>