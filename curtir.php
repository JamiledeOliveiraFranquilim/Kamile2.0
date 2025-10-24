<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuarios'])) {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['id_usuarios'];
$post_id = intval($_POST['post_id'] ?? 0);

if ($post_id > 0) {
    $verifica = $conn->prepare("SELECT id_curtida FROM curtidas WHERE fk_id_post = ? AND fk_id_usuario = ?");
    $verifica->bind_param('ii', $post_id, $uid);
    $verifica->execute();
    $result = $verifica->get_result();

    if ($result->num_rows > 0) {
        // Remove curtida
        $del = $conn->prepare("DELETE FROM curtidas WHERE fk_id_post = ? AND fk_id_usuario = ?");
        $del->bind_param('ii', $post_id, $uid);
        $del->execute();
    } else {
        // Adiciona curtida
        $add = $conn->prepare("INSERT INTO curtidas (fk_id_post, fk_id_usuario) VALUES (?, ?)");
        $add->bind_param('ii', $post_id, $uid);
        $add->execute();
    }
}

header('Location: feed.php');
exit;
?>