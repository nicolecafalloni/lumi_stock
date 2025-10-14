<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<button class="hamburger" id="hamburger-btn">
    <i class="fas fa-bars"></i>
</button>
<aside class="sidebar">
    <div class="logo">
        <span class="logo-text">LumiStock</span>
    </div>

    <div class="user-profile">
        <?php if (!empty($_SESSION['imagem_perfil'])): ?>
            <img src="data:image/jpeg;base64,<?= $_SESSION['imagem_perfil'] ?>" alt="Avatar" class="avatar">
        <?php else: ?>
            <div class="avatar-placeholder"><?= strtoupper(substr($_SESSION['nome'], 0, 2)); ?></div>
        <?php endif; ?>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($_SESSION['nome']); ?></span>
            <span class="user-email"><?= htmlspecialchars($_SESSION['email']); ?></span>
        </div>
    </div>

    <nav class="nav-menu">
        <ul>
            <li class="active"><a href="dashboard.php"><i class="fas fa-home"></i> Início</a></li>
            <li><a href="produtos.php"><i class="fas fa-box"></i> Produtos</a></li>
            <li><a href="cadastrar_produto.php"><i class="fas fa-plus-circle"></i> Cadastrar Produto</a></li>
            <li><a href="movimentacoes.php"><i class="fas fa-exchange-alt"></i> Movimentações</a></li>
            <li><a href="relatorios.php"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
            <li><a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a></li>
        </ul>
    </nav>

    <div class="logout-btn">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>
    
</aside>

<script>
(function(){
    var candidates = [
        'js/script.js',
        '../js/script.js',
        '/lumi_stock/js/script.js'
    ];


    function tryLoad(list, idx){
        if(idx >= list.length) return;
        var s = document.createElement('script');
        s.src = list[idx];
        s.onload = function(){ console.log('script carregado:', list[idx]); };
        s.onerror = function(){
            s.parentNode && s.parentNode.removeChild(s);
            tryLoad(list, idx+1);
        };
        document.head.appendChild(s);
    }
    tryLoad(candidates, 0);
})();

// Função do botão hamburguer
const btn = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');

btn.addEventListener('click', () => {
    sidebar.classList.toggle('active');
    document.body.classList.toggle('menu-open');
});
</script>

