<?php
include 'conexao.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $sql = "DELETE FROM produtos WHERE id = $id";
    if ($conexao->query($sql)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'success',
            title: 'Produto excluído!',
            text: 'O produto foi removido com sucesso.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'produtos.php';
        });
        </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Não foi possível excluir o produto.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'produtos.php';
        });
        </script>";
    }
} else {
    header('Location: produtos.php');
    exit;
}
?>
