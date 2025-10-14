<?php
include 'conexao.php';
include 'verifica_sessao.php';

// Calcular quantidade total de produtos e custo total
$queryTotais = "SELECT SUM(quantidade) as total_estoque, SUM(preco * quantidade) as total_custo FROM produtos";
$resultTotais = mysqli_query($conexao, $queryTotais);
$totais = mysqli_fetch_assoc($resultTotais);

// Produtos com baixo estoque (quantidade <= 5)
$queryBaixoEstoque = "SELECT COUNT(*) as baixo_estoque FROM produtos WHERE quantidade <= 5";
$resultBaixo = mysqli_query($conexao, $queryBaixoEstoque);
$baixoEstoque = mysqli_fetch_assoc($resultBaixo);

// Formatar valores
$totalEstoque = $totais['total_estoque'] ?? 0;
$totalCusto = number_format($totais['total_custo'] ?? 0, 2, ',', '.');
$quantidadeBaixoEstoque = $baixoEstoque['baixo_estoque'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LumiStock - Dashboard Profissional</title>

    <title>LumiStock - Gerenciamento de Estoque</title>
    <link rel="icon" type="image/jpg" href="../img/icon-logo.jpg">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
       
        body.body-dashboard {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        main.main-content {
            padding: 20px;
        }

        /* Banner */
        .dashboard-banner {
            background: linear-gradient(90deg, #4b6cb7, #3e578aff);
            color: white;
            padding: 30px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .dashboard-banner h1 {
            font-size: 28px;
            margin: 0;
              color: aliceblue
        }
        .dashboard-banner p {
            font-size: 16px;
            opacity: 0.85;
        }

        /* Estatísticas */
        .stat-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        .stat-card {
            flex: 1;
            min-width: 220px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .card-low-stock { border-left: 6px solid #e74c3c; }
        .card-product-qty { border-left: 6px solid #3498db; }
        .card-total-cost { border-left: 6px solid #2ecc71; }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: bold;
        }
        .stat-card .stat-icon {
            font-size: 36px;
            opacity: 0.7;
        }

        /* Atalhos */
        .shortcuts-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .shortcut-card {
            background: white;
            flex: 1;
            min-width: 220px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.2s;
        }
        .shortcut-card:hover {
            transform: translateY(-5px);
        }
        .shortcut-card i {
            font-size: 36px;
            margin-bottom: 10px;
            color: #4b6cb7;
        }
        .shortcut-card a {
            display: block;
            font-weight: bold;
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
            text-decoration: none;
        }
        .shortcut-description {
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body class="body-dashboard">
      <button class="hamburger">
    <i class="fas fa-bars"></i>
  </button>
    <div class="container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">

            <!-- Banner -->
            <div class="dashboard-banner">
                <div>
                    <h1>Bem-vindo ao LumiStock</h1>
                    <p>Gerencie seu estoque de forma eficiente e acompanhe os indicadores em tempo real</p>
                </div>
                <i class="fas fa-warehouse fa-3x"></i>
            </div>

            <!-- Cards de estatísticas -->
            <section class="stat-cards">
                <div class="card stat-card card-low-stock">
                    <div>
                        <h3 class="card-title">Produtos com estoque baixo</h3>
                        <span class="stat-value"><?= $quantidadeBaixoEstoque ?></span>
                    </div>
                    <i class="fas fa-exclamation-triangle stat-icon"></i>
                </div>

                <div class="card stat-card card-product-qty">
                    <div>
                        <h3 class="card-title">Quantidade de produtos no estoque</h3>
                        <span class="stat-value"><?= $totalEstoque ?></span>
                    </div>
                    <i class="fas fa-boxes stat-icon"></i>
                </div>

                <div class="card stat-card card-total-cost">
                    <div>
                        <h3 class="card-title">Custo total dos produtos</h3>
                        <span class="stat-value">R$ <?= $totalCusto ?></span>
                    </div>
                    <i class="fas fa-dollar-sign stat-icon"></i>
                </div>
            </section>

            <!-- Atalhos -->
            <h2>Atalhos</h2>
            <section class="shortcuts-grid">
                <div class="card shortcut-card card-products">
                    <i class="fas fa-boxes"></i>
                    <a href="../php/produtos.php">Produtos</a>
                    <p class="shortcut-description">Cadastre e gerencie seus produtos</p>
                </div>

                <div class="card shortcut-card card-exits">
                    <i class="fas fa-box-open"></i>
                    <a href="../php/movimentacoes.php">Movimentações</a>
                    <p class="shortcut-description">Gerenciar entradas e saídas de produtos</p>
                </div>

                <div class="card shortcut-card card-reports">

                    <i class="fas fa-clipboard-list"></i>
                    <a href="../php/relatorios.php">Relatórios</a>
                    <p class="shortcut-description">Visualizar relatórios completos do estoque</p>
                </div>
            </section>

        </main>
    </div>
</body>
</html>
