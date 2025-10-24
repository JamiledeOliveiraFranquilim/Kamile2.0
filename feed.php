<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuarios'])) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['id_usuarios'];

// Publicar novo post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conteudo'])) {
    $conteudo = trim($_POST['conteudo']);
    $imagem = null;

    if (!empty($_FILES['imagem']['name'])) {
        $pasta = 'uploads/';
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);
        $imagem = $pasta . basename($_FILES['imagem']['name']);
        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem);
    }

    $stmt = $conn->prepare("INSERT INTO posts (fk_id_usuario, conteudo, imagem, data_post) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('iss', $uid, $conteudo, $imagem);
    $stmt->execute();
    header('Location: feed.php');
    exit;
}

// Buscar posts
$postagens = $conn->query("
    SELECT p.id_post, p.conteudo, p.imagem, p.data_post, u.nickname, u.avatar_url
    FROM posts p
    JOIN usuarios u ON p.fk_id_usuario = u.id_usuarios
    ORDER BY p.data_post DESC
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feed - Kamile</title>
<style>
    body { font-family: 'Poppins', sans-serif; background: #f8f9fb; margin:0; color:#333; }
    header { background:#fff; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    header h1 { color:#e56b70; font-weight:600; margin:0; }
    .auth-buttons button { margin-left:10px; background:#e56b70; color:white; border:none; border-radius:6px; padding:8px 14px; cursor:pointer; transition:0.3s; }
    .auth-buttons button:hover { background:#d4585d; }

    main.feed { max-width:700px; margin:40px auto; }

    .nova { background:white; padding:20px; border-radius:10px; margin-bottom:20px; }
    .nova textarea { width:100%; height:80px; margin-bottom:10px; padding:8px; border-radius:6px; border:1px solid #ccc; }
    .nova input[type="file"] { margin-bottom:10px; }
    .nova button { padding:10px 20px; background:#e56b70; color:white; border:none; border-radius:6px; cursor:pointer; }

    .post { background:white; padding:15px; border-radius:12px; margin-bottom:20px; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
    .post-header { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
    .avatar { width:45px; height:45px; border-radius:50%; object-fit:cover; }
    .imgpost { max-width:100%; border-radius:10px; margin-top:10px; }

    .acoes { margin-top:10px; }
    .acoes button { background:none; border:none; color:#e56b70; cursor:pointer; font-weight:bold; }

    .comentarios { margin-top:15px; background:#f9f9f9; padding:10px; border-radius:8px; }
    .comentario { display:flex; gap:8px; margin-bottom:8px; }
    .avatar-mini { width:30px; height:30px; border-radius:50%; object-fit:cover; }
    .comentario div { font-size:0.9rem; }
    .comentario div strong { font-size:0.95rem; }
    .comentarios form textarea { width:100%; height:50px; margin-top:5px; margin-bottom:5px; padding:6px; border-radius:6px; border:1px solid #ccc; }
    .comentarios form button { padding:6px 10px; background:#e56b70; color:white; border:none; border-radius:6px; cursor:pointer; }

</style>
</head>
<body>
<header>
    <h1>Kamile</h1>
    <div class="auth-buttons">
        <span>Olá, <?= htmlspecialchars($_SESSION['nickname'] ?? '') ?></span>
        <li><a href="perfil.php">Perfil</a></li>
        <a href="logout.php"><button>Sair</button></a>
    </div>
</header>

<main class="feed">
    <!-- Nova Publicação -->
    <section class="nova">
        <h2>Nova Publicação</h2>
        <form method="POST" enctype="multipart/form-data">
            <textarea name="conteudo" placeholder="O que você está pensando?" required></textarea>
            <input type="file" name="imagem">
            <button type="submit">Publicar</button>
        </form>
    </section>

    <!-- Feed -->
    <?php while($p = $postagens->fetch_assoc()): 
        $post_id = $p['id_post'];
        $qLikes = $conn->query("SELECT COUNT(*) AS total FROM curtidas WHERE fk_id_post = $post_id");
        $likes = $qLikes->fetch_assoc()['total'];
        $qUserLike = $conn->query("SELECT id_curtida FROM curtidas WHERE fk_id_post = $post_id AND fk_id_usuario = $uid");
        $curtiu = $qUserLike->num_rows > 0;
    ?>
    <div class="post">
        <div class="post-header">
            <img src="<?= htmlspecialchars($p['avatar_url'] ?: 'default.jpg') ?>" class="avatar">
            <div>
                <strong><?= htmlspecialchars($p['nickname']) ?></strong><br>
                <small><?= htmlspecialchars($p['data_post']) ?></small>
            </div>
        </div>

        <p><?= nl2br(htmlspecialchars($p['conteudo'])) ?></p>
        <?php if($p['imagem']): ?>
            <img src="<?= htmlspecialchars($p['imagem']) ?>" class="imgpost">
        <?php endif; ?>

        <div class="acoes">
            <form method="POST" action="curtir.php">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <button type="submit" style="color:<?= $curtiu ? '#ff4d4d' : '#777' ?>">❤️ <?= $likes ?> Curtidas</button>
            </form>
        </div>

        <div class="comentarios">
            <h4>Comentários</h4>
            <?php
                $qComentarios = $conn->prepare("
                    SELECT c.*, u.nickname, u.avatar_url 
                    FROM comentarios c
                    JOIN usuarios u ON c.fk_id_usuario = u.id_usuarios
                    WHERE c.fk_id_post = ?
                    ORDER BY c.data_comentario ASC
                ");
                $qComentarios->bind_param('i', $post_id);
                $qComentarios->execute();
                $comentarios = $qComentarios->get_result();
            ?>
            <?php if ($comentarios->num_rows > 0): ?>
                <?php while($c = $comentarios->fetch_assoc()): ?>
                    <div class="comentario">
                        <img src="<?= htmlspecialchars($c['avatar_url'] ?: 'default.jpg') ?>" class="avatar-mini">
                        <div>
                            <strong><?= htmlspecialchars($c['nickname']) ?></strong><br>
                            <?= nl2br(htmlspecialchars($c['conteudo'])) ?><br>
                            <small><?= htmlspecialchars($c['data_comentario']) ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nenhum comentário ainda.</p>
            <?php endif; ?>

            <!-- Formulário para novo comentário -->
            <form method="POST" action="comentar.php">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <textarea name="comentario" placeholder="Escreva um comentário..." required></textarea>
                <button type="submit">Comentar</button>
            </form>
        </div>
    </div>
    <?php endwhile; ?>
</main>
</body>
</html>