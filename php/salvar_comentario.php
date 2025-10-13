<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = $_POST['produto_id'];
    $comentario = trim($_POST['comentario']);

    if ($comentario !== '') {
        $sql = "INSERT INTO comentarios (produto_id, comentario, data) VALUES ('$produto_id', '$comentario', NOW())";
        if ($conexao->query($sql)) {
            header("Location: produtos.php");
            exit;
        } else {
            echo "Erro ao salvar comentário: " . $conexao->error;
        }
    }
}
?>
