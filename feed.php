<?php
session_start();
require 'conexao.php';

// Verifica se o usuário está logado
$logado = isset($_SESSION['id_usuario']);

// Busca posts e informações dos usuários
$sql = "SELECT posts.*, usuarios.nickname, usuarios.avatar_url 
        FROM posts
        JOIN usuarios ON posts.fk_id_usuario = usuarios.id_usuarios
        ORDER BY posts.data_post DESC";
$resultado = $conn->query($sql);

if (!$resultado) {
    die("Erro na consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamile - Feed</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Kamile</h1>
    <div class="auth-buttons">
        <?php if ($logado): ?>
            <span>Olá, <?php echo htmlspecialchars($_SESSION['usuario_nick']); ?> 👋</span>
            <a href="logout.php"><button>Sair</button></a>
        <?php else: ?>
            <a href="login.php"><button>Entrar</button></a>
            <a href="register.php"><button>Cadastrar</button></a>
        <?php endif; ?>
    </div>
</header>

<main class="feed">
    <?php while ($post = $resultado->fetch_assoc()): ?>
        <?php
        // Contar curtidas
        $likesQuery = $conn->query("SELECT COUNT(*) AS total FROM curtidas WHERE fk_id_post = " . $post['id_post']);
        $likes = $likesQuery->fetch_assoc()['total'];

        // Verificar se o usuário logado já curtiu
        $curtido = false;
        if ($logado) {
            $check = $conn->query("SELECT * FROM curtidas WHERE fk_id_post = {$post['id_post']} AND fk_id_usuario = {$_SESSION['id_usuario']}");
            $curtido = $check->num_rows > 0;
        }
        ?>
        <div class="post">
            <div class="user-info">
                <img src="<?= $post['avatar_url'] ?: 'https://i.pravatar.cc/50?u='.$post['nickname'] ?>" alt="user">
                <span>@<?= htmlspecialchars($post['nickname']) ?></span>
            </div>

            <p class="content"><?= htmlspecialchars($post['conteudo']) ?></p>
            <small><?= date('d/m/Y H:i', strtotime($post['data_post'])) ?></small>

            <div class="actions">
                <?php if ($logado): ?>
                    <form action="like.php" method="POST" class="inline-form">
                        <input type="hidden" name="post_id" value="<?= $post['id_post'] ?>">
                        <button type="submit" class="<?= $curtido ? 'liked' : '' ?>">❤️ <?= $likes ?> Curtidas</button>
                    </form>
                <?php else: ?>
                    <button onclick="openModal()">❤️ <?= $likes ?> Curtidas</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</main>

<!-- Modal para login/cadastro -->
<div id="authModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Entre ou cadastre-se</h2>
        <p>Para curtir ou comentar, você precisa estar logado.</p>
        <div class="modal-buttons">
            <a href="login.php"><button>Entrar</button></a>
            <a href="register.php"><button class="register">Cadastrar</button></a>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
