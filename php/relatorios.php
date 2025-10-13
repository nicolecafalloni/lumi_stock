<?php
// relatorios.php
// Ajuste o caminho do include conforme sua estrutura
include 'conexao.php';
include 'verifica_sessao.php';

// Inicializações
$relatorioGerado = false;
$produtos = [];
$totais = [
    'totalProdutos' => 0,
    'baixoEstoque' => 0,
    'valorTotal' => 0.0,
    'unidadesTotais' => 0
];
$categoriaSums = []; // soma por categoria
$topProdutos = []; // para top10

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe filtros (sanitize mínimo)
    $tipo = $_POST['tipoRelatorio'] ?? 'produtos';
    $categoria = $_POST['categoria'] ?? 'todas';
    $dataInicial = $_POST['dataInicial'] ?? null;
    $dataFinal = $_POST['dataFinal'] ?? null;

    // Construir query com parâmetros
    $params = [];
    $conditions = [];

    // Se categoria for 'todas', não adiciona filtro, traz todos os produtos
    if (!empty($categoria) && $categoria !== 'todas') {
        $conditions[] = "categoria = ?";
        $params[] = $categoria;
    }


    if (!empty($dataInicial) && !empty($dataFinal)) {
        $conditions[] = "data_cadastro BETWEEN ? AND ?";
        $params[] = $dataInicial;
        $params[] = $dataFinal;
    }

    $sql = "SELECT id, sku, nome, categoria, quantidade, preco AS preco_unit, data_cadastro FROM produtos";
    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $sql .= " ORDER BY nome ASC";

    // Preparar e executar
    $stmt = $conexao->prepare($sql);
    if ($stmt) {
        if (count($params) > 0) {
            // Bind dinâmico
            $types = str_repeat('s', count($params)); // todos como string por simplicidade
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            // calcular preco_total por produto
            $row['preco_total'] = $row['preco_unit'] * $row['quantidade'];
            $produtos[] = $row;

            // Totais
            $totais['totalProdutos']++;
            $totais['unidadesTotais'] += (int)$row['quantidade'];
            $totais['valorTotal'] += (float)$row['preco_total'];
            if ((int)$row['quantidade'] < 10) $totais['baixoEstoque']++;

            // Categoria sums
            $cat = $row['categoria'] ?: 'Outros';
            if (!isset($categoriaSums[$cat])) $categoriaSums[$cat] = 0.0;
            $categoriaSums[$cat] += (float)$row['preco_total'];
        }

        $relatorioGerado = count($produtos) > 0;

        // Top 10 por valor
        $produtosSorted = $produtos;
        usort($produtosSorted, function($a, $b) {
            return $b['preco_total'] <=> $a['preco_total'];
        });
        $topProdutos = array_slice($produtosSorted, 0, 10);

        // Salvar um registro do relatório na tabela relatorios
        // (crie a tabela relatorios conforme modelo: id, tipo, categoria, data_inicial, data_final, total_produtos, baixo_estoque, valor_total, unidades_totais, data_geracao)
        $insert = $conexao->prepare("INSERT INTO relatorios (tipo, categoria, data_inicial, data_final, total_produtos, baixo_estoque, valor_total, unidades_totais, data_geracao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        if ($insert) {
            $insert->bind_param(
                "ssssiidd",
                $tipo,
                $categoria,
                $dataInicial,
                $dataFinal,
                $totais['totalProdutos'],
                $totais['baixoEstoque'],
                $totais['valorTotal'],
                $totais['unidadesTotais']
            );
            $insert->execute();
            $insert->close();
        }

        $stmt->close();
    }

    $relatorioGerado = true;
}

// Helper para formatar reais
function fmtMoney($v) {
    return 'R$ ' . number_format((float)$v, 2, ',', '.');
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatórios de Estoque</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../css/style_relatorios.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body id="rel-body">
    <div class="container-relatorio">

<?php include 'sidebar.php'; ?>
<header id="rel-header">
    <div class="rel-header-container">
        <div>
            <h1>Relatórios de Estoque</h1>
            <p class="rel-subtitle">Visualize e exporte relatórios detalhados do seu estoque</p>
        </div>
        <div class="rel-realtime">📈 Análises em Tempo Real</div>
    </div>
</header>

<main id="rel-main" class="rel-container">
    <!-- FILTROS -->
    <section id="rel-filtros" class="rel-card">
        <h2>Filtros do Relatório</h2>
        <form id="rel-form" method="post" class="rel-filtros-form">
            <div class="rel-row">
                <div class="rel-field">
                    <label for="tipoRelatorio">Tipo de Relatório *</label>
                    <select id="tipoRelatorio" name="tipoRelatorio" required>
                        <option value="produtos">Produtos<option>
                        <option value="movimentacoes">Movimentações</option>
                    </select>
                </div>

                <div class="rel-field">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria">
                        <option value="todas">Todas</option>
                        <option value="eletronicos">Eletrônicos</option>
                        <option value="escritorio">Escritório</option>
                    </select>
                </div>

                <div class="rel-field">
                    <label for="dataInicial">Data Inicial</label>
                    <input type="date" id="dataInicial" name="dataInicial">
                </div>

                <div class="rel-field">
                    <label for="dataFinal">Data Final</label>
                    <input type="date" id="dataFinal" name="dataFinal">
                </div>

                <div class="rel-field rel-field-button">
                    <button type="submit" id="rel-btn-gerar" class="rel-btn">Filtrar Relatório</button>
                </div>
            </div>
        </form>
    </section>

    <?php if (!$relatorioGerado): ?>
        <!-- MENSAGEM INICIAL: aparece quando NÃO foi gerado relatório -->
        <section id="rel-mensagem-inicial" class="rel-card rel-info-box">
            <div class="rel-info-icon">📄</div>
            <div class="rel-info-text">
                <strong>Selecione os filtros desejados e clique em "Gerar Relatório" para visualizar os dados.</strong>
            </div>
        </section>
    <?php else: ?>
        <!-- RELATÓRIO GERADO: cards resumo, gráficos, exportação e tabela -->
        <section id="rel-resumo" class="rel-grid-4">
            <div class="rel-card rel-card-summary">
                <div class="rel-card-title">Total de Produtos</div>
                <div class="rel-card-value"><?= $totais['totalProdutos'] ?></div>
            </div>
            <div class="rel-card rel-card-summary">
                <div class="rel-card-title">Baixo Estoque</div>
                <div class="rel-card-value color-baixo-estoque"><?= $totais['baixoEstoque'] ?></div>
            </div>
            <div class="rel-card rel-card-summary">
                <div class="rel-card-title">Valor Total</div>
                <div class="rel-card-value color-valor"><?= fmtMoney($totais['valorTotal']) ?></div>
            </div>
            <div class="rel-card rel-card-summary">
                <div class="rel-card-title">Unidades Totais</div>
                <div class="rel-card-value color-unit"><?= $totais['unidadesTotais'] ?></div>
            </div>
        </section>

        <section id="rel-graficos" class="rel-grid-2">
            <div class="rel-card">
                <div class="rel-card-content">
                    <h3>Valor por Categoria</h3>
                    <canvas id="rel-chart-categoria" width="400" height="250"></canvas>
                </div>
            </div>
            <div class="rel-card">
                <h3>Top 10 Produtos por Valor</h3>
                <canvas id="rel-chart-top10" width="400" height="250"></canvas>
            </div>
        </section>

        <section id="rel-export" class="rel-card rel-export">
            <div class="rel-export-title">Exportar Relatório</div>
            <div class="rel-export-buttons">
                <button id="rel-export-pdf" class="rel-btn rel-btn-outline">Exportar PDF</button>
                <button id="rel-export-xlsx" class="rel-btn rel-btn-success">Exportar Excel</button>
                <button id="rel-print" class="rel-btn rel-btn-ghost">Imprimir</button>
            </div>
        </section>

        <section id="rel-tabela" class="rel-card">
            <h3>Todos os Produtos</h3>
            <div class="rel-table-wrap">
                <table class="rel-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nome do Produto</th>
                            <th>Categoria</th>
                            <th>Quantidade</th>
                            <th>Preço Unit.</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th>Data Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['sku']) ?></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($p['categoria'])) ?></td>
                            <td><?= (int)$p['quantidade'] ?> unid</td>
                            <td><?= fmtMoney($p['preco_unit']) ?></td>
                            <td><?= fmtMoney($p['preco_total']) ?></td>
                            <td>
                                <?php
                                if ((int)$p['quantidade'] === 0) echo "<span class='rel-status rel-sem'>Sem estoque</span>";
                                elseif ((int)$p['quantidade'] < 10) echo "<span class='rel-status rel-baixo'>Baixo</span>";
                                else echo "<span class='rel-status rel-normal'>Normal</span>";
                                ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($p['data_cadastro'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</main>

<script>
/* Dados para JS (só estarão preenchidos se relatórioGerado==true) */
const REL_REPORT_GENERATED = <?= $relatorioGerado ? 'true' : 'false' ?>;
const REL_PRODUTOS = <?= json_encode($produtos, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
const REL_CATEGORIA_SUMS = <?= json_encode($categoriaSums, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
const REL_TOP_PRODUTOS = <?= json_encode($topProdutos, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;
</script>

<script src="../js/script_relatorios.js"></script>
</div>
</body>
</html>
