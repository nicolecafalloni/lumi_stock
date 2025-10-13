<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: ../index.php");
    exit();
}

require_once 'conexao.php';

$usuario_logado = $_SESSION['nome'];
$email_logado = $_SESSION['email'];
$error_message = null; 
$status_success = false; 

$filtro_tipo = isset($_GET['tipoMov']) ? $conexao->real_escape_string($_GET['tipoMov']) : '';
$filtro_produto_id = isset($_GET['produto']) && is_numeric($_GET['produto']) ? (int)$_GET['produto'] : '';
$filtro_data_inicial = isset($_GET['dataInicial']) ? $conexao->real_escape_string($_GET['dataInicial']) : '';
$filtro_data_final = isset($_GET['dataFinal']) ? $conexao->real_escape_string($_GET['dataFinal']) : '';
$filtro_usuario = isset($_GET['usuario']) ? $conexao->real_escape_string($_GET['usuario']) : '';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'new_movement') {
    $movType = $conexao->real_escape_string($_POST['movType']);
    $movDate = $conexao->real_escape_string($_POST['movDate']);
    $product_id = (int)$_POST['productSelect'];
    $quantity = (int)$_POST['quantity'];
    $observations = $conexao->real_escape_string($_POST['observations']);

    $sql_insert = "INSERT INTO movimentacoes (data_movimentacao, tipo, produto_id, quantidade, usuario_email, observacoes) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conexao->prepare($sql_insert)) {
        $stmt->bind_param("ssiiss", $movDate, $movType, $product_id, $quantity, $email_logado, $observations);

        if ($stmt->execute()) {
            $status_success = true;
        } else {
            $error_message = "ERRO ao registrar movimentação: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "ERRO de preparação da query de inserção: " . $conexao->error;
    }
}

if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql_delete = "DELETE FROM movimentacoes WHERE id = ?";
    if ($stmt_del = $conexao->prepare($sql_delete)) {
        $stmt_del->bind_param("i", $delete_id);
        if ($stmt_del->execute()) {
            header("Location: movimentacoes.php?status=deleted");
            exit();
        } else {
             $error_message = "ERRO ao excluir movimentação: " . $stmt_del->error;
        }
        $stmt_del->close();
    } else {
         $error_message = "ERRO de preparação da query de exclusão: " . $conexao->error;
    }
}


$total_movimentacoes = 0;
$total_entradas_qnt = 0;
$total_saidas_qnt = 0;
$movimentacoes_hoje = 0;

$sql_summary = "SELECT 
                    COUNT(id) AS total_movs,
                    SUM(CASE WHEN tipo = 'entrada' THEN quantidade ELSE 0 END) AS total_entradas_qnt,
                    SUM(CASE WHEN tipo = 'saida' THEN quantidade ELSE 0 END) AS total_saidas_qnt,
                    SUM(CASE WHEN DATE(data_movimentacao) = CURDATE() THEN 1 ELSE 0 END) AS movs_hoje
                FROM movimentacoes";

if ($result_summary = $conexao->query($sql_summary)) {
    $summary_data = $result_summary->fetch_assoc();
    $total_movimentacoes = (int)$summary_data['total_movs'];
    $total_entradas_qnt = (int)$summary_data['total_entradas_qnt'];
    $total_saidas_qnt = (int)$summary_data['total_saidas_qnt'];
    $movimentacoes_hoje = (int)$summary_data['movs_hoje'];
    $result_summary->free();
}

$produtos = [];
$sql_produtos = "SELECT id AS id_produto, nome FROM produtos ORDER BY nome"; 
if ($result_produtos = $conexao->query($sql_produtos)) {
    while ($row = $result_produtos->fetch_assoc()) {
        $produtos[] = $row;
    }
    $result_produtos->free();
}

$movimentacoes = [];
$sql_historico = "SELECT 
                        m.data_movimentacao, 
                        m.tipo, 
                        p.nome AS nome_produto, 
                        m.quantidade, 
                        m.usuario_email,
                        m.observacoes,
                        m.id AS id_movimentacao
                    FROM movimentacoes m
                    JOIN produtos p ON m.produto_id = p.id";

$where_clauses = [];

if (!empty($filtro_tipo)) {
    $where_clauses[] = "m.tipo = '{$filtro_tipo}'";
}
if (!empty($filtro_produto_id)) {
    $where_clauses[] = "m.produto_id = {$filtro_produto_id}";
}
if (!empty($filtro_data_inicial)) {
    $where_clauses[] = "DATE(m.data_movimentacao) >= '{$filtro_data_inicial}'";
}
if (!empty($filtro_data_final)) {
    $where_clauses[] = "DATE(m.data_movimentacao) <= '{$filtro_data_final}'";
}
if (!empty($filtro_usuario)) {
    $where_clauses[] = "m.usuario_email LIKE '%{$filtro_usuario}%'";
}

if (!empty($where_clauses)) {
    $sql_historico .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql_historico .= " ORDER BY m.data_movimentacao DESC";


if ($result_historico = $conexao->query($sql_historico)) {
    while ($row = $result_historico->fetch_assoc()) {
        $movimentacoes[] = $row;
    }
    $result_historico->free();
}

$conexao->close(); 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="../img/icon-logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/movimentacao.css">
    <title>LumiStock - Movimentações</title>
    <style>
        .badge-entrada { background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; }
        .badge-saida { background-color: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; }
        .qnt-entrada { color: green; font-weight: bold; }
        .qnt-saida { color: red; font-weight: bold; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close-btn:hover, .close-btn:focus { color: #000; text-decoration: none; cursor: pointer; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-danger { background-color: #dc3545; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    
    <?php if ($error_message): ?>
        <div class="alert-error" role="alert">
            <i class="fas fa-exclamation-triangle"></i> Erro: <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php elseif ($status_success): ?>
        <div class="alert-success" role="alert">
            <i class="fas fa-check-circle"></i> Movimentação registrada com sucesso!
        </div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
        <div class="alert-success" role="alert">
            <i class="fas fa-check-circle"></i> Movimentação excluída com sucesso!
        </div>
    <?php endif; ?>

    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-boxes logo-icon"></i>
            <span class="logo-text">LumiStock</span>
        </div>
        <div class="user-info">
            <img src="https://via.placeholder.com/40" alt="Avatar" class="user-avatar">
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($usuario_logado); ?></span>
                <span class="user-email"><?php echo htmlspecialchars($email_logado); ?></span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#"><i class="fas fa-home"></i> Início</a></li>
                <li class="active"><a href="movimentacoes.php"><i class="fas fa-exchange-alt"></i> Movimentações</a></li>
                <li><a href="#"><i class="fas fa-box"></i> Produtos</a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
                <hr>
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div class="header-content">
                <div class="page-title-group">
                    <i class="fas fa-chart-line title-icon"></i>
                    <h2>Movimentações de Estoque</h2>
                </div>
                <p class="page-subtitle">Registre e acompanhe entradas e saídas de produtos</p>
            </div>
            <button class="btn-primary" id="openModalBtn">
                <i class="fas fa-plus"></i> Nova Movimentação
            </button>
        </header>

        <section class="summary-cards">
            <div class="card card-total">
                <span class="card-title">Total de Movimentações</span>
                <div class="card-value-container">
                    <span class="card-value"><?php echo number_format($total_movimentacoes, 0, ',', '.'); ?></span>
                    <i class="fas fa-boxes card-icon"></i>
                </div>
            </div>
            <div class="card card-entrada">
                <span class="card-title">Entradas (Unidades)</span>
                <div class="card-value-container">
                    <span class="card-value">
                        <?php echo number_format($total_entradas_qnt, 0, ',', '.'); ?>
                    </span>
                    <span class="card-subtitle">unidades</span>
                    <i class="fas fa-plus card-icon"></i>
                </div>
            </div>
            <div class="card card-saida">
                <span class="card-title">Saídas (Unidades)</span>
                <div class="card-value-container">
                    <span class="card-value">
                        <?php echo number_format($total_saidas_qnt, 0, ',', '.'); ?>
                    </span>
                    <span class="card-subtitle">unidades</span>
                    <i class="fas fa-minus card-icon"></i>
                </div>
            </div>
            <div class="card card-hoje">
                <span class="card-title">Hoje</span>
                <div class="card-value-container">
                    <span class="card-value"><?php echo number_format($movimentacoes_hoje, 0, ',', '.'); ?></span>
                    <span class="card-subtitle">movimentações</span>
                    <i class="fas fa-calendar-day card-icon"></i>
                </div>
            </div>
        </section>
        
        <section class="dashboard-section">
            <form method="GET" action="movimentacoes.php" class="filter-area">
                <h3><i class="fas fa-filter"></i> Filtros de Pesquisa</h3>
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="tipoMov">Tipo de Movimentação</label>
                        <select id="tipoMov" name="tipoMov"> 
                            <option value="">Todos</option>
                            <option value="entrada" <?php echo ($filtro_tipo == 'entrada') ? 'selected' : ''; ?>>Entrada</option>
                            <option value="saida" <?php echo ($filtro_tipo == 'saida') ? 'selected' : ''; ?>>Saída</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="produto">Produto</label>
                        <select id="produto" name="produto"> 
                            <option value="">Todos os produtos</option>
                            <?php foreach ($produtos as $produto): ?>
                                <option value="<?php echo $produto['id_produto']; ?>" 
                                    <?php echo ($filtro_produto_id == $produto['id_produto']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($produto['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="dataInicial">Data Inicial</label>
                        <input type="date" id="dataInicial" name="dataInicial" value="<?php echo htmlspecialchars($filtro_data_inicial); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="dataFinal">Data Final</label>
                        <input type="date" id="dataFinal" name="dataFinal" value="<?php echo htmlspecialchars($filtro_data_final); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="usuario">Usuário</label>
                        <input type="text" id="usuario" name="usuario" placeholder="Nome ou Email" value="<?php echo htmlspecialchars($filtro_usuario); ?>">
                    </div>
                </div>
                <button type="submit" class="btn-primary filter-btn"><i class="fas fa-search"></i> Aplicar Filtros</button>
            </form>
            
            <div class="history-table">
                <h3><i class="fas fa-history"></i> Histórico de Movimentações</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Usuário</th>
                            <th>Observações</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimentacoes)): ?>
                            <?php foreach ($movimentacoes as $mov): 
                                $tipo = strtolower($mov['tipo']);
                                $data_formatada = date('d/m/Y', strtotime($mov['data_movimentacao'])); 
                                $badge_class = ($tipo == 'entrada') ? 'badge-entrada' : 'badge-saida';
                                $qnt_class = ($tipo == 'entrada') ? 'qnt-entrada' : 'qnt-saida';
                                $icon = ($tipo == 'entrada') ? 'fas fa-arrow-right' : 'fas fa-arrow-left';
                            ?>
                                <tr>
                                    <td data-label="Data"><?php echo $data_formatada; ?></td>
                                    <td data-label="Tipo" class="type-<?php echo $tipo; ?>">
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <i class="<?php echo $icon; ?>"></i> 
                                            <?php echo ucfirst($tipo); ?>
                                        </span>
                                    </td>
                                    <td data-label="Produto"><?php echo htmlspecialchars($mov['nome_produto']); ?></td>
                                    <td data-label="Quantidade" class="<?php echo $qnt_class; ?>">
                                        <?php echo number_format($mov['quantidade'], 0, ',', '.'); ?>
                                    </td>
                                    <td data-label="Usuário"><?php echo htmlspecialchars($mov['usuario_email']); ?></td>
                                    <td data-label="Observações"><?php echo htmlspecialchars($mov['observacoes']); ?></td>
                                    <td data-label="Ações">
                                        <button class="btn-icon delete-btn" 
                                                data-id="<?php echo $mov['id_movimentacao']; ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 15px;">Nenhuma movimentação encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div id="newMovementModal" class="modal">
            <div class="modal-content">
                <header class="modal-header">
                    <h2>Nova Movimentação</h2>
                    <span class="close-btn new-close-btn">&times;</span>
                </header>
                <form class="new-movement-form" method="POST" action="movimentacoes.php">
                    <input type="hidden" name="action" value="new_movement">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="movType">Tipo de Movimentação</label>
                            <select id="movType" name="movType" required>
                                <option value="entrada" selected>Entrada</option>
                                <option value="saida">Saída</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="movDate">Data</label>
                            <input type="date" id="movDate" name="movDate" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group full-width">
                            <label for="productSelect">Produto</label>
                            <select id="productSelect" name="productSelect" required>
                                <option value="" disabled selected>Selecione um produto</option>
                                <?php foreach ($produtos as $produto): ?>
                                    <option value="<?php echo $produto['id_produto']; ?>">
                                        <?php echo htmlspecialchars($produto['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($produtos)): ?>
                                <small style="color: red;">Nenhum produto encontrado. Cadastre produtos primeiro!</small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group full-width">
                            <label for="quantity">Quantidade</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="form-group full-width">
                            <label for="observations">Observações</label>
                            <textarea id="observations" name="observations" placeholder="Ex: Chegada de novo lote, produto vendido..."></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelModalBtn">Cancelar</button>
                        <button type="submit" class="btn-primary confirm-btn">Confirmar Movimentação</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="deleteConfirmModal" class="modal">
            <div class="modal-content">
                <header class="modal-header">
                    <h2>Confirmação de Exclusão</h2>
                    <span class="close-btn delete-close-btn">&times;</span>
                </header>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a movimentação de código <strong id="delete_id_display">#ID</strong>?</p>
                    <p style="color: red; font-weight: bold;"><i class="fas fa-exclamation-triangle"></i> Esta ação é irreversível.</p>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary delete-cancel-btn">Cancelar</button>
                    <button type="button" class="btn-danger confirm-delete-btn"><i class="fas fa-trash-alt"></i> Excluir Definitivamente</button>
                </div>
            </div>
        </div>

    </main>
    <script>
        var modalNovo = document.getElementById("newMovementModal");
        var btnNovo = document.getElementById("openModalBtn");
        var spanNovo = document.querySelector(".new-close-btn"); 
        var cancelBtnNovo = document.getElementById("cancelModalBtn");

        if(btnNovo) {
            btnNovo.onclick = function() { modalNovo.style.display = "block"; }
        }
        if(spanNovo) {
            spanNovo.onclick = function() { modalNovo.style.display = "none"; }
        }
        if(cancelBtnNovo) {
            cancelBtnNovo.onclick = function() { modalNovo.style.display = "none"; }
        }

        var modalExcluir = document.getElementById("deleteConfirmModal");
        var confirmDeleteBtn = document.querySelector(".confirm-delete-btn");
        var deleteCloseBtn = document.querySelector(".delete-close-btn");
        var deleteCancelBtn = document.querySelector(".delete-cancel-btn");
        var currentDeleteId = null;

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.onclick = function(e) {
                e.preventDefault(); 
                
                currentDeleteId = this.getAttribute('data-id'); 
                
                if (currentDeleteId) {
                    document.getElementById('delete_id_display').textContent = `#${currentDeleteId}`;
                    modalExcluir.style.display = "block";
                }
            };
        });

        if (confirmDeleteBtn) {
            confirmDeleteBtn.onclick = function() {
                if (currentDeleteId) {
                    window.location.href = `movimentacoes.php?delete_id=${currentDeleteId}`;
                }
            };
        }

        if(deleteCloseBtn) { deleteCloseBtn.onclick = function() { modalExcluir.style.display = "none"; } }
        if(deleteCancelBtn) { deleteCancelBtn.onclick = function() { modalExcluir.style.display = "none"; } }

        window.onclick = function(event) {
            if (event.target == modalNovo) {
                modalNovo.style.display = "none";
            }
            if (event.target == modalExcluir) {
                modalExcluir.style.display = "none";
            }
        }
    </script>
</body>
</html>