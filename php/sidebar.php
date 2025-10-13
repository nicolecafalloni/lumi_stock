<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<aside class="sidebar">
    <div class="logo">
        <span class="logo-text">LumiStock</span>
    </div>

    <div class="user-profile">
        <img src="placeholder-avatar.png" alt="Avatar de <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>" class="avatar">
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?></span>
            <span class="user-email"><?= htmlspecialchars($_SESSION['email'] ?? 'email@exemplo.com'); ?></span>
        </div>
    </div>

    <nav class="nav-menu">
        <ul>
            <li class="active"><a href="dashboard.php"><i class="fas fa-home"></i> Início</a></li>
            <li><a href="produtos.php"><i class="fas fa-box"></i> Produtos</a></li>
            <li><a href="cadastrar_produto.php"><i class="fas fa-plus-circle"></i> Cadastrar Produto</a></li>
            <li><a href="movimentacoes.php"><i class="fas fa-exchange-alt"></i> Movimentações</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
            <li><a href="clientes.php"><i class="fas fa-users"></i> Clientes</a></li>
            <li><a href="fornecedores.php"><i class="fas fa-truck"></i> Fornecedores</a></li>
            <li><a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a></li>
        </ul>
    </nav>

    <div class="logout-btn">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>
</aside>


    <!-- Carregador resiliente para js/script.js: tenta caminhos relativos comuns para funcionar quando a sidebar é incluída de subpastas -->
    <script>
        (function(){
            var candidates = [
                'js/script.js',        // quando a página está na raiz do projeto
                '../js/script.js',     // quando a página está dentro de php/ (ex: php/dashboard.php)
                '/lumi_stock/js/script.js', // caminho absoluto esperado no ambiente local (ajuste se necessário)
            ];

            function tryLoad(list, idx){
                if(idx >= list.length) return; // nenhum deu certo
                var s = document.createElement('script');
                s.src = list[idx];
                s.onload = function(){ console.log('script carregado:', list[idx]); };
                s.onerror = function(){
                    // remove o script com src inválido e tenta o próximo
                    s.parentNode && s.parentNode.removeChild(s);
                    tryLoad(list, idx+1);
                };
                document.head.appendChild(s);
            }

            tryLoad(candidates, 0);
        })();
    </script>