<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['senha_atual'], $_POST['nova_senha'], $_POST['confirmar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if ($nova_senha !== $confirmar_senha) {
            echo "<script>alert('A nova senha e a confirmação não coincidem.');</script>";
        } elseif (strlen($nova_senha) < 6) {
            echo "<script>alert('A nova senha deve ter pelo menos 6 caracteres.');</script>";
        } elseif (!password_verify($senha_atual, $usuario['senha'])) {
            echo "<script>alert('Senha atual incorreta.');</script>";
        } else {
            $nova_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET senha = ? WHERE id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $nova_hash, $id);
            $stmt->execute();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil - LumiStock</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="perfil-header">
            <div class="perfil-avatar">
                <span><?= strtoupper(substr($usuario['nome'], 0, 2)); ?></span>
                <span class="status"></span>
            </div>
            <div class="perfil-info">
                <h1><?= htmlspecialchars($usuario['nome']); ?></h1>
                <p><?= htmlspecialchars($usuario['email']); ?></p>
                <span class="role">Administrador</span>
                <span class="member-since">Membro desde Oct 2025</span>
            </div>
        </header>

        <div class="perfil-body">
            <div class="coluna-esquerda">
                <div class="card info-pessoal">
                    <h2>Informações Pessoais</h2>
                    <p><strong>Nome Completo:</strong> <?= htmlspecialchars($usuario['nome']); ?></p>
                    <p><strong>E-mail:</strong> <?= htmlspecialchars($usuario['email']); ?></p>
                    <small>O e-mail não pode ser alterado</small>
                    <a href="editar_perfil.php?id=<?= $usuario['id']; ?>" class="btn-primary">Editar Perfil</a>
                </div>

                <div class="card seguranca-conta">
                    <h2>Segurança da Conta</h2>
                    <form method="POST">
                        <label>Senha Atual *</label>
                        <input type="password" name="senha_atual" required>

                        <label>Nova Senha *</label>
                        <input type="password" name="nova_senha" minlength="6" required>

                        <label>Confirmar Nova Senha *</label>
                        <input type="password" name="confirmar_senha" minlength="6" required>

                        <button type="submit" class="btn-primary">Atualizar Senha</button>
                    </form>
                    <div class="dicas-senha">
                        <p>Dicas de segurança:</p>
                        <ul>
                            <li>Use pelo menos 6 caracteres</li>
                            <li>Combine letras, números e símbolos</li>
                            <li>Não use informações pessoais óbvias</li>
                            <li>Troque sua senha regularmente</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="coluna-direita">
                <div class="card nivel-acesso">
                    <h2>Nível de Acesso</h2>
                    <p><strong>Acesso Total:</strong> Você tem permissão completa para gerenciar todos os aspectos do sistema.</p>
                    <ul class="permissoes">
                        <li>Visualizar produtos</li>
                        <li>Criar e editar produtos</li>
                        <li>Excluir produtos</li>
                        <li>Registrar movimentações</li>
                        <li>Excluir movimentações</li>
                        <li>Gerar relatórios</li>
                        <li>Gerenciar usuários</li>
                    </ul>
                </div>

                <div class="card sair-conta">
                    <h2>Encerrar Sessão</h2>
                    <p>Saia da sua conta de forma segura. Você precisará fazer login novamente para acessar o sistema.</p>
                    <form method="POST" action="logout.php">
                        <button type="submit" class="btn-vermelho">Sair da Conta</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
