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
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - LumiStock</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-editar-perfil">
    <?php include 'sidebar.php'; ?>

    <main class="main-content-editar-perfil">

        <header class="perfil-header-editar-perfil">
            <div class="perfil-avatar-editar-perfil">
                <span><?= strtoupper(substr($usuario['nome'], 0, 2)); ?></span>
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
    <form method="POST" action="editar_perfil.php">
        <!-- Foto de Perfil -->
        <label>Foto de Perfil</label>
        <div class="perfil-foto-editar-perfil">
    <div class="perfil-avatar-editar-perfil">
        <?php if (!empty($usuario['imagem_perfil'])): ?>
            <img src="../uploads/<?= htmlspecialchars($usuario['imagem_perfil']); ?>" alt="Foto de perfil" class="avatar-img">
        <?php else: ?>
            <span><?= strtoupper(substr($usuario['nome'], 0, 2)); ?></span>
        <?php endif; ?>
        <span class="status-editar-perfil"></span>
    </div>

    <input type="file" name="imagem" class="input-foto-editar-perfil" accept="image/*">
    <button type="submit" name="atualizar_imagem" class="btn-secondary-editar-perfil">Trocar Foto</button>
</div>

        <label>Nome Completo *</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']); ?>" required>

        <label>E-mail</label>
        <input type="email" value="<?= htmlspecialchars($usuario['email']); ?>" disabled>
        <small>O e-mail não pode ser alterado</small>

        <div class="botoes-editar-perfil">
        <a href="perfil.php" class="btn-cancelar-editar-perfil">Cancelar</a>
        <button type="submit" name="atualizar_dados" class="btn-primary-editar-perfil">Salvar Alterações</button>
        </div>
    </form>
</div>


                <div class="card-editar-perfil seguranca-conta-editar-perfil">
                    <h2>Alterar Senha</h2>
                    <form method="POST">
                        <label>Nova Senha *</label>
                        <input type="password" name="nova_senha" minlength="6" required>

                        <label>Confirmar Nova Senha *</label>
                        <input type="password" name="confirmar_senha" minlength="6" required>

                        <button type="submit" name="atualizar_senha" class="btn-primary-editar-perfil">Atualizar Senha</button>
                    </form>
                    <div class="dicas-senha-editar-perfil">
                        <p><strong>Dicas de segurança:</strong></p>
                        <ul>
                            <li>Use pelo menos 6 caracteres</li>
                            <li>Combine letras, números e símbolos</li>
                            <li>Não use informações pessoais óbvias</li>
                            <li>Troque sua senha regularmente</li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="coluna-direita-editar-perfil">

                <div class="card-editar-perfil nivel-acesso-editar-perfil">
                    <h2>Nível de Acesso</h2>
                    <p><strong>Acesso Total:</strong> Este usuário tem acesso total a todas as funcionalidades do sistema.</p>
                    <a href="perfil.php" class="btn-primary-editar-perfil">Voltar ao Perfil</a>
                </div>

                <div class="card-editar-perfil sair-conta-editar-perfil">
                    <h2>Encerrar Sessão</h2>
                    <p>Saia da sua conta de forma segura. Você precisará fazer login novamente para acessar o sistema.</p>
                    <form method="POST" action="logout.php">
                        <button type="submit" class="btn-vermelho-editar-perfil">Sair da Conta</button>
                    </form>
                </div>

            </div>
        </div>

    </main>
</div>

</body>
</html>
