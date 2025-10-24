<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuarios'])) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['id_usuarios'];
$erro = '';
$sucesso = '';

// Atualizar dados do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $nickname = trim($_POST['nickname']);
    $avatar_url = trim($_POST['avatar_url']);

    // Upload de avatar
    if (!empty($_FILES['avatar']['name'])) {
        $pasta = 'uploads/';
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);
        $avatar_url = $pasta . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_url);
    }

    $stmt = $conn->prepare("UPDATE usuarios SET nickname=?, avatar_url=? WHERE id_usuarios=?");
    $stmt->bind_param('ssi', $nickname, $avatar_url, $uid);
    if ($stmt->execute()) {
        $_SESSION['nickname'] = $nickname;
        $_SESSION['avatar_url'] = $avatar_url;
        $sucesso = "Perfil atualizado com sucesso!";
    } else {
        $erro = "Erro ao atualizar: " . $conn->error;
    }
}

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT nickname, avatar_url FROM usuarios WHERE id_usuarios=?");
$stmt->bind_param('i', $uid);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Buscar posts do usuário
$postagens = $conn->prepare("
    SELECT id_post, conteudo, data_post 
    FROM posts 
    WHERE fk_id_usuario=? 
    ORDER BY data_post DESC
");
$postagens->bind_param('i', $uid);
$postagens->execute();
$posts = $postagens->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil - Kamile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fb;
            margin: 0;
            color: #333;
        }

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

        header nav a {
            text-decoration: none;
            color: #e56b70;
            font-weight: 500;
            transition: 0.3s;
        }

        header nav a:hover {
            color: #d4585d;
        }

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

        .avatar-perfil {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="url"], input[type="file"] {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            background: #e56b70;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background: #d4585d;
        }

        .post {
            border-top: 1px solid #eee;
            padding: 15px 0;
        }

        .post:first-child {
            border-top: none;
        }

        .post small {
            color: #888;
        }

        .mensagem {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .erro { background: #ffe5e5; color: #d4585d; }
        .sucesso { background: #e5ffe5; color: #4CAF50; }
    </style>
</head>
<body>
<header>
    <h1>Kamile</h1>
    <nav>
        <a href="feed.php">Home</a>
        <a href="perfil.php">Perfil</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<main>
    <h2>Meu Perfil</h2>

    <?php if($erro): ?>
        <div class="mensagem erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if($sucesso): ?>
        <div class="mensagem sucesso"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <img src="<?= htmlspecialchars($usuario['avatar_url'] ?: 'default.jpg') ?>" class="avatar-perfil"><br>
        <input type="file" name="avatar" accept="image/*">
        <input type="text" name="nickname" value="<?= htmlspecialchars($usuario['nickname']) ?>" placeholder="Nome de usuário" required>
        <button type="submit" name="atualizar">Atualizar Perfil</button>
    </form>
    <h2>Meus Posts</h2>
    <?php if($posts->num_rows > 0): ?>
        <?php while($p = $posts->fetch_assoc()): ?>
            <div class="post">
                <p><?= nl2br(htmlspecialchars($p['conteudo'])) ?></p>
                <small><?= htmlspecialchars($p['data_post']) ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Você ainda não fez nenhuma publicação.</p>
    <?php endif; ?>
</main>
</body>
</html>
