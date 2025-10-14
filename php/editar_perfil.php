<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['atualizar_dados'])) {
    $nome = $_POST['nome'];

    $sql = "UPDATE users SET nome = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $nome, $id);
    $stmt->execute();

    $_SESSION['nome'] = $nome;
}


    if (isset($_POST['atualizar_senha'])) {
        $nova = $_POST['nova_senha'];
        $confirmar = $_POST['confirmar_senha'];

        if ($nova === $confirmar && strlen($nova) >= 6) {
            $senha_hash = password_hash($nova, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET senha = ? WHERE id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $senha_hash, $id);
            $stmt->execute();
        }
    }
}

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $imagem_binaria = file_get_contents($_FILES['imagem']['tmp_name']);

    $sql = "UPDATE users SET imagem_perfil = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("bi", $null, $id); // 'b' para blob
    $stmt->send_long_data(0, $imagem_binaria);
    $stmt->execute();

    // Atualiza a sessão com base64
    $_SESSION['imagem_perfil'] = base64_encode($imagem_binaria);
}

if (isset($_POST['atualizar_imagem']) && isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $imagem_binaria = file_get_contents($_FILES['imagem']['tmp_name']);

    $sql = "UPDATE users SET imagem_perfil = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("bi", $null, $id);
    $stmt->send_long_data(0, $imagem_binaria);
    $stmt->execute();

    $_SESSION['imagem_perfil'] = base64_encode($imagem_binaria);

    // Recarrega os dados do usuário
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
}


?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - LumiStock</title>
    <link rel="icon" type="image/jpg" href="../img/icon-logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container-editar-perfil">
        <?php include 'sidebar.php'; ?>

        <main class="main-content-editar-perfil">
<header class="perfil-header-editar-perfil">
    <div class="perfil-avatar-editar-perfil">
        <?php if (!empty($usuario['imagem_perfil'])): ?>
    <?php $base64 = base64_encode($usuario['imagem_perfil']); ?>
    <img src="data:image/jpeg;base64,<?= $base64 ?>" alt="Foto de perfil" class="avatar-img">
<?php else: ?>
    <span><?= strtoupper(substr($usuario['nome'], 0, 2)); ?></span>
<?php endif; ?>

        <span class="status-editar-perfil"></span>
    </div>

    <div class="perfil-info-editar-perfil">
        <h1><?= htmlspecialchars($usuario['nome']); ?></h1>
        <p><?= htmlspecialchars($usuario['email']); ?></p>
        <span class="role-editar-perfil">Administrador</span>
        <span class="member-since-editar-perfil">Membro desde Oct 2025</span>
    </div>
</header>


        <div class="perfil-body-editar-perfil">
            <div class="coluna-esquerda-editar-perfil">

                <div class="card-editar-perfil info-pessoal-editar-perfil">
    <h2>Informações Pessoais</h2>
    <form method="POST" action="editar_perfil.php" enctype="multipart/form-data">
    <label>Foto de Perfil</label>
    <div class="perfil-foto-editar-perfil">
        <div class="perfil-avatar-editar-perfil">
            <?php if (!empty($usuario['imagem_perfil'])): ?>
                <?php $base64 = base64_encode($usuario['imagem_perfil']); ?>
                <img src="data:image/jpeg;base64,<?= $base64 ?>" alt="Foto de perfil" class="avatar-img">
            <?php else: ?>
                <span><?= strtoupper(substr($usuario['nome'], 0, 2)); ?></span>
            <?php endif; ?>
            <span class="status-editar-perfil"></span>
        </div>

<div class="upload-wrapper">
    <label for="imagem" class="upload-label">Escolher Foto</label>
    <input type="file" name="imagem" id="imagem" class="input-foto-editar-perfil" accept="image/*">
</div>
        <button type="submit" name="atualizar_imagem" class="btn-secondary-editar-perfil">Trocar Foto</button>
    </div>

    <label>Nome Completo *</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']); ?>" required>

    <label>E-mail</label>
    <input type="email" value="<?= htmlspecialchars($usuario['email']); ?>" disabled>
    <small class="info-email-editar-perfil">O e-mail não pode ser alterado</small>

    <div class="botoes-editar-perfil">
        <a href="perfil.php" class="btn-cancelar-editar-perfil">Cancelar</a>
        <button type="submit" name="atualizar_dados" class="btn-primary-editar-perfil">Salvar Alterações</button>
    </div>
</form>

</div>

            </div>

            <div class="coluna-direita-editar-perfil">

                <div class="card nivel-acesso">
                    <h2>Nível de Acesso</h2>
                    <div class="acesso-total-permi" >
                        <h3>Acesso Total:</h3>
                        <p>Você tem permissão completa para gerenciar todos os aspectos do sistema, incluindo produtos, movimentações e usuários</p>
                    </div>
                    <a href="perfil.php" class="btn-voltar-perfil">Voltar ao Perfil</a>
                <h2>Permissões</h2>
                    <ul class="permissoes">
                        <li><img src="../img/icone-correto-removebg-preview.png" class="icone-perm">Visualizar produtos</li>
                        <li><img src="../img/icone-correto-removebg-preview.png" class="icone-perm" >Criar e editar produtos</li>
                        <li><img src="../img/icone-correto-removebg-preview.png" class="icone-perm" >Excluir produtos</li>
                        <li><img src="../img/icone-correto-removebg-preview.png" class="icone-perm" >Registrar movimentações</li>
                        <li><img src="../img/icone-correto-removebg-preview.png" class="icone-perm" >Excluir movimentações</li>
                        <li><img src="../img/icone-correto-removebg-preview.png" class="icone-perm" >Gerar relatórios</li>
                    </ul>
                </div>
                <div class="card-editar-perfil sair-conta-editar-perfil">
                    <h2>Encerrar Sessão</h2>
                    <p>Saia da sua conta de forma segura. Você precisará fazer login novamente para acessar o sistema.</p>
                    <form method="POST" action="logout.php">
                        <button type="submit" class="btn-vermelho-editar-perfil">Sair da Conta</button>
                    </form>

            </div>
        </div>

    </main>
</div>
<script src="../js/script.js"></script>
</body>
</html>
