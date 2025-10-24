<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$data = 'kamile';

$conn = new mysqli($host, $user, $pass, $data);

if ($conn->connect_error) {
    die('Erro na conexão: ' . $conn->connect_error);
}
?>
