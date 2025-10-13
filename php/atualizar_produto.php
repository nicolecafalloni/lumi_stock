<?php
include 'conexao.php';
include 'verifica_sessao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $quantidade = $_POST['quantidade'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    if (!$id) {
        http_response_code(400);
        echo "ID do produto não fornecido.";
        exit;
    }

    // Se foi enviado arquivo de imagem
    $imagemBlob = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagemBlob = file_get_contents($_FILES['imagem']['tmp_name']);
    }

    if ($imagemBlob) {
        $sql = "UPDATE produtos SET nome=?, preco=?, quantidade=?, descricao=?, imagem=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sdisbi", $nome, $preco, $quantidade, $descricao, $imagemBlob, $id);
        $stmt->send_long_data(4, $imagemBlob);
    } else {
        $sql = "UPDATE produtos SET nome=?, preco=?, quantidade=?, descricao=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sdisi", $nome, $preco, $quantidade, $descricao, $id);
    }

    if ($stmt->execute()) {
        echo "Produto atualizado com sucesso!";
    } else {
        http_response_code(500);
        echo "Erro ao atualizar o produto: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
} else {
    http_response_code(405);
    echo "Método não permitido.";
}
