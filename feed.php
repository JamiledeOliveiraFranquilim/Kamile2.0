<?php
session_start();
require 'conexao.php';

$logado = isset($_SESSION['usuario_id']);
$posts = $conn->query("SELECT p.*, u.nome FROM posts p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.data_post DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamile - Feed</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <h1>Kamile</h1>
    <div class="auth-buttons">
        <?php if ($logado): ?>
            <span>Olá, <?php echo $_SESSION['usuario_nome']; ?> 👋</span>
            <a href="logout.php"><button>Sair</button></a>
        <?php else: ?>
            <a href="login.php"><button>Entrar</button></a>
            <a href="register.php"><button>Cadastrar</button></a>
        <?php endif; ?>
    </div>
</header>

<main class="feed">
    <?php while($post = $posts->fetch_assoc()): ?>
        <div class="post">
            <div class="user-info">
                <img src="https://i.pravatar.cc/50?u=<?php echo $post['usuario_id']; ?>" alt="user">
                <span>@<?php echo $post['nome']; ?></span>
            </div>
            <p class="content"><?php echo htmlspecialchars($post['conteudo']); ?></p>
            <?php if($post['imagem']): ?>
                <img src="<?php echo htmlspecialchars($post['imagem']); ?>" class="post-img">
            <?php endif; ?>

            <div class="actions">
                <?php if ($logado): ?>
                    <form action="like_post.php" method="POST" class="inline-form">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit">❤️ Curtir</button>
                    </form>
                <?php else: ?>
                    <button onclick="openModal()">❤️ Curtir</button>
                <?php endif; ?>

                <?php if ($logado): ?>
                    <form action="post_comment.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <input type="text" name="comentario" placeholder="Escreva um comentário...">
                        <button type="submit">Enviar</button>
                    </form>
                <?php else: ?>
                    <button onclick="openModal()">💬 Comentar</button>
                <?php endif; ?>
            </div>

            <div class="comments">
                <?php
                $comentarios = $conn->query("SELECT c.*, u.nome FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.post_id = ".$post['id']." ORDER BY c.data_comentario DESC");
                while($c = $comentarios->fetch_assoc()):
                ?>
                    <p><strong>@<?php echo $c['nome']; ?>:</strong> <?php echo htmlspecialchars($c['comentario']); ?></p>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endwhile; ?>
</main>

<div id="authModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Entre ou cadastre-se</h2>
        <p>Para curtir e comentar, você precisa estar logado.</p>
        <div class="modal-buttons">
            <a href="login.php"><button>Entrar</button></a>
            <a href="register.php"><button class="register">Cadastrar</button></a>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
