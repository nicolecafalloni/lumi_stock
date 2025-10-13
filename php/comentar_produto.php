<?php
include 'conexao.php';
$id = $_POST['id'];
$comentario = $_POST['comentario'];

$conexao->query("ALTER TABLE produtos ADD COLUMN IF NOT EXISTS comentarios TEXT");
$conexao->query("UPDATE produtos SET comentarios = CONCAT(IFNULL(comentarios, ''), '\n', '$comentario') WHERE id=$id");
?>
