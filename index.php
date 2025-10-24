<?php
session_start();
require 'conexao.php';

$logado = isset($_SESSION['id_usuario']);

// Busca posts e informações dos usuários
$sql = "SELECT posts.*, usuarios.nickname, usuarios.avatar_url 
        FROM posts 
        JOIN usuarios ON posts.fk_id_usuario = usuarios.id_usuarios
        ORDER BY posts.data_post DESC";
$result = $conn->query($sql);

if (!$result) die("Erro na consulta: " . $conn->error);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamile - Feed com usuário</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Kamile</h1>
    <div class="auth-buttons">
        <?php if ($logado): ?>
            <span>Olá, <?= htmlspecialchars($_SESSION['nickname'] ?? '') ?></span>
            <a href="logout.php"><button>Sair</button></a>
        <?php else: ?>
            <a href="login.php"><button>Entrar</button></a>
            <a href="cadastrar.php"><button class="register">Cadastrar</button></a>
        <?php endif; ?>
    </div>
</header>

<main class="feed">
    <?php while ($post = $result->fetch_assoc()): ?>
        <?php
        // Contar curtidas
        $likes = $conn->query("SELECT COUNT(*) AS total FROM curtidas WHERE fk_id_post = {$post['id_post']}")->fetch_assoc()['total'];
        ?>
        <div class="post">
            <div class="user-info">
                <img src="<?= $post['avatar_url'] ?: 'https://i.pravatar.cc/50?u='.$post['nickname'] ?>" alt="user">
                <span>@<?= htmlspecialchars($post['nickname']) ?></span>
            </div>

            <p class="content"><?= htmlspecialchars($post['conteudo']) ?></p>
            <?php if ($post['imagem']): ?>
                <img src="<?= htmlspecialchars($post['imagem']) ?>" class="post-img">
            <?php endif; ?>
            <small><?= date('d/m/Y H:i', strtotime($post['data_post'])) ?></small>

            <div class="actions">
                <?php if ($logado): ?>
                    <form action="like.php" method="POST" class="inline-form">
                        <input type="hidden" name="post_id" value="<?= $post['id_post'] ?>">
                        <button type="submit">❤️ <?= $likes ?> Curtidas</button>
                    </form>
                    <form action="comment.php" method="POST" class="comment-form">
                        <input type="hidden" name="post_id" value="<?= $post['id_post'] ?>">
                        <input type="text" name="comentario" placeholder="Deixe um comentário..." required>
                        <button type="submit">Enviar</button>
                    </form>
                <?php else: ?>
                    <button onclick="openCard()">❤️ <?= $likes ?> Curtidas</button>
                    <input type="text" placeholder="Deixe um comentário..." onclick="openCard()" readonly>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</main>

<!-- Card para usuário não logado -->
<div id="authCard" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCard()">&times;</span>
        <h2>Cadastre-se ou faça login</h2>
        <p>Para curtir ou comentar, você precisa estar logado.</p>
        <div class="modal-buttons">
            <a href="login.php"><button>Entrar</button></a>
            <a href="cadastrar.php"><button class="register">Cadastrar</button></a>
        </div>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
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
}

header h1 {
    color: #e56b70;
    font-weight: 600;
}

.auth-buttons button {
    margin-left: 10px;
    background: #e56b70;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 14px;
    cursor: pointer;
    transition: 0.3s;
}
.auth-buttons button:hover { background: #d4585d; }

.feed {
    max-width: 700px;
    margin: 40px auto;
}

.post {
    background: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-info img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.post-img {
    width: 100%;
    margin-top: 10px;
    border-radius: 8px;
}

.actions {
    margin-top: 10px;
}

.actions button {
    border: none;
    background: none;
    color: #e56b70;
    cursor: pointer;
    font-weight: bold;
}

.comment-form {
    display: flex;
    margin-top: 8px;
    gap: 5px;
}

.comment-form input {
    flex: 1;
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.comment-form button {
    background: #e56b70;
    border: none;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
}

/* Modal/Card */
.modal {
    display: none;
    position: fixed;
    z-index: 10;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    width: 300px;
    text-align: center;
    margin: 15% auto;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
}

.modal-buttons button {
    margin-top: 10px;
    width: 100%;
    padding: 8px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    color: white;
    background: #e56b70;
}

.close {
    float: right;
    cursor: pointer;
    color: #888;
}

</style>
<script>
    function openCard() {
    document.getElementById('authCard').style.display = 'block';
}
function closeCard() {
    document.getElementById('authCard').style.display = 'none';
}

window.onclick = function(e) {
    const modal = document.getElementById('authCard');
    if (e.target === modal) modal.style.display = 'none';
}

</script>