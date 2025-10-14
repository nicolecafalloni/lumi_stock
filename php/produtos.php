<?php
include 'conexao.php';
include 'verifica_sessao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LumiStock - Produtos</title>

    <link rel="icon" type="image/jpg" href="../img/icon-logo.jpg">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
      <button class="hamburger">
    <i class="fas fa-bars"></i>
  </button>
<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="main-header">
            <div class="header-title">
                <i class="fas fa-box-open"></i>
                <h1>Produtos em Estoque</h1>
                <p>Gerencie seus produtos de forma fácil e moderna</p>
            </div>
            <button class="btn-novo-produto" onclick="window.location.href='cadastrar_produto.php'">
                <i class="fas fa-plus"></i> Novo Produto
            </button>
        </header>

        <section class="inventory-controls">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Pesquisar produto...">
            </div>
            <div class="filter-dropdown">
                <i class="fas fa-filter"></i>
                <select id="filterSelect">
                    <option value="todos">Todos</option>
                    <option value="in-stock">Em Estoque</option>
                    <option value="low-stock">Estoque Baixo</option>
                    <option value="out-stock">Sem Estoque</option>
                </select>
            </div>
        </section>

        <section class="product-grid" id="productGrid">
        <?php
        $sql = "SELECT p.*, (SELECT GROUP_CONCAT(c.comentario SEPARATOR '||') FROM comentarios c WHERE c.produto_id = p.id) as comentarios
                FROM produtos p ORDER BY p.data_cadastro DESC";
        $result = $conexao->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row['quantidade'] == 0) {
                    $status = "Sem estoque";
                    $class = "out-stock";
                } elseif ($row['quantidade'] <= 10) {
                    $status = "Estoque baixo";
                    $class = "low-stock";
                } else {
                    $status = "Em estoque";
                    $class = "in-stock";
                }

                $imagemBase64 = base64_encode($row['imagem']);
                $comentarios = $row['comentarios'] ? explode("||", $row['comentarios']) : [];
                ?>

                <div class="product-card" 
                     data-id="<?= $row['id'] ?>" 
                     data-nome="<?= htmlspecialchars($row['nome']) ?>"
                     data-preco="<?= htmlspecialchars($row['preco']) ?>"
                     data-quantidade="<?= htmlspecialchars($row['quantidade']) ?>"
                     data-descricao="<?= htmlspecialchars($row['descricao']) ?>"
                     data-status="<?= $class ?>"
                     data-nomefiltro="<?= strtolower($row['nome']) ?>">

                    <div class="stock-badge <?= $class ?>"><?= $status ?></div>
                    <img src="data:image/jpeg;base64,<?= $imagemBase64 ?>" alt="<?= htmlspecialchars($row['nome']) ?>">
                    <div class="card-content">
                        <h2><?= htmlspecialchars($row['nome']) ?></h2>
                        <p class="product-desc"><?= htmlspecialchars($row['descricao']) ?></p>
                        <div class="product-details">
                            <p>SKU: <?= htmlspecialchars($row['sku']) ?></p>
                            <div class="price-qty">
                                <span class="quantity <?= ($row['quantidade']==0?'zero-stock':'') ?>"><?= $row['quantidade'] ?> un</span>
                                <span class="price">R$ <?= number_format($row['preco'], 2, ',', '.') ?></span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-action btn-edit" onclick="editarProduto(this)">Editar</button>
                            <button class="btn-action btn-exclude" onclick="excluirProduto(<?= $row['id'] ?>)">Excluir</button>
                            <button class="btn-action btn-comment" onclick="adicionarComentario(<?= $row['id'] ?>)">Comentar</button>
                        </div>

                        <?php if (!empty($comentarios)): ?>
                        <div class="comentarios-list">
                            <?php foreach ($comentarios as $com): ?>
                                <p class="comentario"><i class="fas fa-comment"></i> <?= htmlspecialchars($com) ?></p>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php }
        } else {
            echo "<p>Nenhum produto cadastrado.</p>";
        }

        $conexao->close();
        ?>
        </section>
    </main>
</div>

<script>
// FILTRO E PESQUISA
const searchInput = document.getElementById('searchInput');
const filterSelect = document.getElementById('filterSelect');

function filtrarProdutos() {
    const search = searchInput.value.toLowerCase();
    const filter = filterSelect.value;

    document.querySelectorAll('.product-card').forEach(card => {
        const nome = card.dataset.nomefiltro;
        const status = card.dataset.status;
        const matchesSearch = nome.includes(search);
        const matchesFilter = filter === 'todos' || status === filter;
        card.style.display = (matchesSearch && matchesFilter) ? 'block' : 'none';
    });
}

searchInput.addEventListener('input', filtrarProdutos);
filterSelect.addEventListener('change', filtrarProdutos);

// EXCLUIR PRODUTO
function excluirProduto(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Essa ação não pode ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = `excluir_produto.php?id=${id}`;
        }
    });
}

// EDITAR PRODUTO COM SWEETALERT
function editarProduto(btn) {
    const card = btn.closest('.product-card');
    const id = card.dataset.id;
    const nome = card.dataset.nome;
    const preco = card.dataset.preco;
    const qtd = card.dataset.quantidade;
    const desc = card.dataset.descricao;

    Swal.fire({
        title: 'Editar Produto',
        html: `
            <input id="edit-nome" class="swal2-input" placeholder="Nome" value="${nome}">
            <input id="edit-preco" class="swal2-input" placeholder="Preço" type="number" value="${preco}">
            <input id="edit-quantidade" class="swal2-input" placeholder="Quantidade" type="number" value="${qtd}">
            <textarea id="edit-descricao" class="swal2-textarea" placeholder="Descrição">${desc}</textarea>
            <input id="edit-imagem" type="file" class="swal2-file">
        `,
        confirmButtonText: 'Salvar',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('nome', document.getElementById('edit-nome').value);
            formData.append('preco', document.getElementById('edit-preco').value);
            formData.append('quantidade', document.getElementById('edit-quantidade').value);
            formData.append('descricao', document.getElementById('edit-descricao').value);
            const file = document.getElementById('edit-imagem').files[0];
            if (file) formData.append('imagem', file);

            return fetch('atualizar_produto.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                Swal.fire('Salvo!', 'Produto atualizado com sucesso.', 'success')
                    .then(() => location.reload());
            });
        }
    });
}

// COMENTAR COM SWEETALERT
function adicionarComentario(id) {
    Swal.fire({
        title: 'Adicionar Comentário',
        input: 'text',
        inputPlaceholder: 'Digite seu comentário...',
        showCancelButton: true,
        confirmButtonText: 'Salvar',
        cancelButtonText: 'Cancelar',
        preConfirm: (comentario) => {
            if (!comentario) {
                Swal.showValidationMessage('Digite algo!');
                return false;
            }

            return fetch('salvar_comentario.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `produto_id=${id}&comentario=${encodeURIComponent(comentario)}`
            }).then(() => {
                Swal.fire('Comentado!', 'Seu comentário foi adicionado.', 'success')
                    .then(() => location.reload());
            });
        }
    });
}
</script>
<script src="../js/script.js"></script>
</body>
</html>
