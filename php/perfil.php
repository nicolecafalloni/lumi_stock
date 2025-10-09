<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - LumiStock</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">

        <!-- Sidebar -->
         <?php include 'sidebar.php'; ?>
        <!-- Main Content -->
        <!-- Main Content -->
        <main class="main-content">

            <!-- Cabeçalho -->
            <header class="perfil-header">
                <div class="perfil-foto">
                    <span>KL</span>
                    <span class="status"></span>
                </div>
                <div class="perfil-info">
                    <h2>Kevin&nbsp;Lucca</h2>
                    <p>kevin.03@gmail.com</p>
                    <div class="badges">
                        <span class="badge admin">Administrador</span>
                        <span class="badge member">Membro desde Oct 2025</span>
                    </div>
                </div>
            </header>

            <!-- Corpo do Perfil -->
            <div class="perfil-body">
                <!-- Coluna Esquerda -->
                <div class="coluna-esquerda">

                    <!-- Informações Pessoais -->
                    <div class="card info-pessoal">
                        <h3><i class="fa-regular fa-user"></i> Informações Pessoais</h3>
                        <div class="conteudo-card">
                            <p>Nome Completo: Teste</p>
                            <p>E-mail: Teste@gmail.com</p>
                            <small>O e-mail não pode ser alterado</small>
                            <button class="btn-azul">Editar Perfil</button>
                        </div>
                    </div>

                    <!-- Segurança da Conta -->
                    <div class="card seguranca-conta">
                        <h3><i class="fa-solid fa-lock"></i> Segurança da Conta</h3>
                        <div class="conteudo-card">
                            <label>Senha Atual *</label>
                            <div class="input-group">
                                <input type="password" placeholder="Digite sua senha atual">
                                <i class="fa-regular fa-eye"></i>
                            </div>

                            <label>Nova Senha *</label>
                            <div class="input-group">
                                <input type="password" placeholder="Mínimo 6 caracteres">
                                <i class="fa-regular fa-eye"></i>
                            </div>

                            <label>Confirmar Nova Senha *</label>
                            <div class="input-group">
                                <input type="password" placeholder="Confirme sua nova senha">
                                <i class="fa-regular fa-eye"></i>
                            </div>

                            <button class="btn-azul">Atualizar Senha</button>

                            <div class="dicas">
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

                </div>

                <!-- Coluna Direita -->
                <div class="coluna-direita">
                    <div class="card acesso">
                        <h3><i class="fa-solid fa-user-shield"></i> Nível de Acesso</h3>
                        <div class="conteudo-card">
                            <div class="acesso-info">
                               
                            <div class="permissoes">
                                <p>Permissões:</p>
                                
                            </div>
                        </div>
                    </div>

                    <div class="card sair-conta">
                        <h3><i class="fa-solid fa-arrow-right-from-bracket"></i> Encerrar Sessão</h3>
                        <p>Saia da sua conta de forma segura. Você precisará fazer login novamente para acessar o sistema.</p>
                        <button class="btn-vermelho"><i class="fa-solid fa-right-from-bracket"></i> Sair da Conta</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>