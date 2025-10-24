<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $avatar_url = trim($_POST['avatar_url']);

    // Verifica se o nickname já existe
    $checkNick = $conn->prepare("SELECT id_usuarios FROM usuarios WHERE nickname = ?");
    $checkNick->bind_param('s', $nickname);
    $checkNick->execute();
    $resNick = $checkNick->get_result();

    if ($resNick->num_rows > 0) {
        $erro = 'Este nickname já está cadastrado!';
    } else {
        // Verifica se o e-mail já existe
        $checkEmail = $conn->prepare("SELECT id_usuarios FROM usuarios WHERE email = ?");
        $checkEmail->bind_param('s', $email);
        $checkEmail->execute();
        $resEmail = $checkEmail->get_result();

        if ($resEmail->num_rows > 0) {
            $erro = 'Este e-mail já está cadastrado!';
        } else {
            // Inserção no banco
            $stmt = $conn->prepare("INSERT INTO usuarios (nickname, email, bio, avatar_url, senha_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $nickname, $email, $bio, $avatar_url, $senha);

            if ($stmt->execute()) {
                $_SESSION['id_usuario'] = $conn->insert_id;
                $_SESSION['nickname'] = $nickname;
                $_SESSION['avatar_url'] = $avatar_url;
                header('Location: feed.php');
                exit;
            } else {
                $erro = 'Erro ao cadastrar! ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Kamile - Cadastro</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <!-- Lado artístico -->
    <div class="left-side">
        <h1>🌸 Kamile</h1>
        <p>Crie sua conta e faça parte da comunidade artística.<br>Compartilhe momentos e inspire outras pessoas!</p>
        <img src="https://i.ibb.co/2FsfXqM/art-illustration.png" alt="Arte" class="art-image">
    </div>

    <!-- Lado formulário -->
    <div class="right-side">
        <div class="card">
            <h2>Registrar</h2>
            <form id="formCadastro" method="POST" novalidate>
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="nome" id="nome" placeholder="Nickname" required>
                </div>

                <div class="input-group">
                    <i class="fa fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>

                <div class="input-group bio-group">
                    <i class="fa fa-info-circle"></i>
                    <textarea name="bio" id="bio" placeholder="Biografia (máx. 100 caracteres)" maxlength="100"></textarea>
                    <div class="char-count"><span id="bioCount">0</span>/100</div>
                </div>

                <div class="input-group">
                    <i class="fa fa-image"></i>
                    <input type="url" name="avatar_url" id="avatar_url" placeholder="Link do Avatar (http...)" required>
                </div>

                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="senha" id="senha" placeholder="Senha" required>
                </div>

                <button type="submit">Cadastrar</button>
            </form>
            <a href="login.php">Já tem uma conta? Logar</a>

            <?php if (!empty($erro)): ?>
                <div class="erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Contador de caracteres da biografia
    const bioInput = document.getElementById('bio');
    const bioCount = document.getElementById('bioCount');
    bioInput.addEventListener('input', () => {
        bioCount.textContent = bioInput.value.length;
    });

    // Validação do formulário
    document.getElementById('formCadastro').addEventListener('submit', function(e) {
        const nickname = document.getElementById('nome').value.trim();
        const email = document.getElementById('email').value.trim();
        const bio = document.getElementById('bio').value.trim();
        const avatarUrl = document.getElementById('avatar_url').value.trim();
        const senha = document.getElementById('senha').value.trim();

        if (!nickname) { alert('O nickname é obrigatório.'); e.preventDefault(); return; }
        if (!email) { alert('O e-mail é obrigatório.'); e.preventDefault(); return; }
        if (senha.length < 4) { alert('A senha deve ter pelo menos 4 caracteres.'); e.preventDefault(); return; }
        if (bio.length > 100) { alert('A biografia deve ter no máximo 100 caracteres.'); e.preventDefault(); return; }
        if (avatarUrl && !avatarUrl.startsWith('http')) { alert('O link do avatar deve começar com "http" ou "https".'); e.preventDefault(); return; }
    });
</script>
</body>
</html>
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
body { background:#f8f9fb; min-height:100vh; }

/* Container principal */
.container { display:flex; min-height:100vh; }

/* Lado artístico */
.left-side {
    flex:1;
    background: linear-gradient(135deg,#f093fb,#f5576c);
    color:white;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:40px;
    text-align:center;
}
.left-side h1 { font-size:3rem; margin-bottom:20px; }
.left-side p { font-size:1.2rem; margin-bottom:30px; }
.left-side .art-image { max-width:100%; border-radius:12px; box-shadow:0 10px 20px rgba(0,0,0,0.2); }

/* Lado formulário */
.right-side { flex:1; display:flex; align-items:center; justify-content:center; background:#f8f9fb; }

/* Card */
.card { background:white; padding:40px 30px; border-radius:12px; width:100%; max-width:400px; box-shadow:0 10px 25px rgba(0,0,0,0.1); text-align:center; }
.card h2 { margin-bottom:25px; color:#333; }

/* Inputs com ícones */
.input-group { position:relative; margin-bottom:20px; }
.input-group i { position:absolute; top:50%; left:15px; transform:translateY(-50%); color:#e56b70; }
.input-group input, .input-group textarea { width:100%; padding:12px 15px 12px 45px; border:1px solid #ccc; border-radius:8px; font-size:1rem; transition:0.3s; }
.input-group input:focus, .input-group textarea:focus { border-color:#e56b70; outline:none; }

/* Textarea biografia */
.bio-group textarea { min-height:80px; resize:none; }
.bio-group .char-count { text-align:right; font-size:0.85rem; color:#555; margin-top:4px; }

/* Botão */
.card button { width:100%; padding:12px; background:#e56b70; border:none; border-radius:8px; color:white; font-size:1rem; cursor:pointer; transition:0.3s; }
.card button:hover { background:#d4585d; }

/* Link */
.card a { display:block; margin-top:15px; color:#e56b70; text-decoration:none; transition:0.3s; }
.card a:hover { text-decoration:underline; }

/* Erro */
.erro { margin-top:10px; color:red; font-weight:500; }

/* Responsivo */
@media screen and (max-width:900px) {
    .container { flex-direction:column; }
    .left-side, .right-side { flex:unset; width:100%; }
    .left-side { padding:30px 20px; }
    .right-side { padding:30px 20px; }
}

</style>