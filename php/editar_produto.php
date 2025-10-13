<?php
include 'conexao.php';
include 'verifica_sessao.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: produtos.php');
    exit;
}

// Atualização do produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];
    $sku = $_POST['sku'];

    // Verifica se foi enviada uma nova imagem
    if (!empty($_FILES['imagem']['tmp_name'])) {
        $imagem = addslashes(file_get_contents($_FILES['imagem']['tmp_name']));
        $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco', quantidade='$quantidade', sku='$sku', imagem='$imagem' WHERE id=$id";
    } else {
        $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco='$preco', quantidade='$quantidade', sku='$sku' WHERE id=$id";
    }

    if ($conexao->query($sql)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Produto atualizado!',
            text: 'As alterações foram salvas com sucesso.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'produtos.php';
        });
        </script>";
    } else {
        echo "Erro: " . $conexao->error;
    }
    exit;
}

// Busca os dados do produto atual
$sql = "SELECT * FROM produtos WHERE id = $id";
$result = $conexao->query($sql);
$produto = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto - LumiStock</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container-editar">
    <h1>Editar Produto</h1>
    <form action="" method="POST" enctype="multipart/form-data" class="form-editar">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']); ?>" required>

        <label>Descrição:</label>
        <textarea name="descricao" required><?= htmlspecialchars($produto['descricao']); ?></textarea>

        <label>Preço:</label>
        <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($produto['preco']); ?>" required>

        <label>Quantidade:</label>
        <input type="number" name="quantidade" value="<?= htmlspecialchars($produto['quantidade']); ?>" required>

        <label>SKU:</label>
        <input type="text" name="sku" value="<?= htmlspecialchars($produto['sku']); ?>" required>

        <label>Imagem atual:</label><br>
        <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']); ?>" width="150" style="margin-bottom:10px;"><br>

        <label>Alterar imagem (opcional):</label>
        <input type="file" name="imagem" accept="image/*">

        <button type="submit" class="btn-salvar">Salvar Alterações</button>
    </form>
</div>
</body>
</html>
