<?php
session_start();
require 'db/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$post_id = intval($_POST['post_id']);
$user_id = $_SESSION['usuario_id'];

// verifica se já curtiu
$check = $conn->query("SELECT * FROM curtidas WHERE fk_id_post = $post_id AND fk_id_usuario = $user_id");

if ($check->num_rows > 0) {
    // se já curtiu, remove curtida
    $conn->query("DELETE FROM curtidas WHERE fk_id_post = $post_id AND fk_id_usuario = $user_id");
} else {
    // se não curtiu, adiciona curtida
    $conn->query("INSERT INTO curtidas (fk_id_post, fk_id_usuario) VALUES ($post_id, $user_id)");
}

header("Location: index.php");
exit;
?>
