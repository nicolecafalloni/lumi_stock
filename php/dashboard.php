<?php
include 'conexao.php';
include 'verifica_sessao.php';

// Arquivo: dashboard.php

// 1. INCLUSÃO DE CONFIGURAÇÕES E CONEXÃO COM O BANCO DE DADOS
// include 'config/db_connection.php';
// include 'models/Estoque.php';

// 2. FUNÇÃO SIMULADA PARA BUSCAR DADOS DO ESTOQUE
// Em um sistema real, essa função faria uma consulta SQL
function getDadosEstoque() {
    // Simulação de dados (substituir pela consulta real)
    $data = [
        'produtos_estoque_baixo' => 7, // Valor atualizado dinamicamente
        'quantidade_total_produtos' => 150,
        'custo_total_produtos' => 215430.50 
    ];
    return $data;
}

$dadosEstoque = getDadosEstoque();

// Formatação do custo para Real brasileiro
$custoFormatado = number_format($dadosEstoque['custo_total_produtos'], 2, ',', '.');

// O PHP renderiza o HTML, injetando os dados dinâmicos
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LumiStock - Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="body-dashboard">
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <h1>Início</h1>

            <section class="stat-cards">
                <div class="card stat-card card-low-stock">
                    <h3 class="card-title">Produtos com estoque baixo</h3>
                    <div class="card-content">
                        <span class="stat-value" id="lowStockCount">0</span>
                        <i class="fas fa-exclamation-triangle stat-icon"></i>
                    </div>
                </div>

                <div class="card stat-card card-product-qty">
                    <h3 class="card-title">Quantidade de produtos no estoque</h3>
                    <div class="card-content">
                        <span class="stat-value" id="productQuantity">45</span>
                        <i class="fas fa-boxes stat-icon"></i>
                    </div>
                </div>

                <div class="card stat-card card-total-cost">
                    <h3 class="card-title">Custo total dos produtos</h3>
                    <div class="card-content">
                        <span class="currency">R$</span>
                        <span class="stat-value" id="totalCost">183.525</span>
                    </div>
                </div>
            </section>

            <h2>Atalhos</h2>
            <section class="shortcuts-grid">
                <div class="card shortcut-card card-products">
                    <i class="fas fa-boxes shortcut-icon"></i>
                    <a class="a-dashboard" class="shortcut-title" href="produtos.php">Produtos</a>
                    <p class="shortcut-description">Cadastre de produtos</p>
                </div>

                <div class="card shortcut-card card-entries">
                    <i class="fas fa-archive shortcut-icon"></i>
                    <a class="a-dashboard" class="shortcut-title" href="movimentacoes.php">Entradas</a>
                    <p class="shortcut-description">Gerenciar entradas de produtos no estoque</p>
                </div>

                <div class="card shortcut-card card-exits">
                    <i class="fas fa-box-open shortcut-icon"></i>
                    <a class="a-dashboard" class="shortcut-title" href="movimentacoes.php">Saídas</a>
                    <p class="shortcut-description">Gerenciar saldos de produtos no estoque</p>
                </div>

                <div class="card shortcut-card card-reports">
                    <i class="fas fa-clipboard-list shortcut-icon"></i>
                    <a class="a-dashboard" class="shortcut-title" href="relatorios.php">Relatórios</a>
                    <p class="shortcut-description">Relatórios</p>
                </div>

                <div class="card shortcut-card card-clients">
                    <i class="fas fa-users shortcut-icon"></i>
                    <a class="a-dashboard" class="shortcut-title" href="clientes.php">Clientes</a>
                    <p class="shortcut-description">Cadastro de clientes</p>
                </div>

                <div class="card shortcut-card card-suppliers">
                    <i class="fas fa-user-tie shortcut-icon"></i>
                    <a class="a-dashboard" class="shortcut-title" href="fornecedores.php">Fornecedores</a>
                    <p class="shortcut-description">Cadastre de fornecedores</p>
                </div>
            </section>
        </main>
    </div>

    <script src="js/script.js"></script>
</body>

</html>