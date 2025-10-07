<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>LumiStock - Produtos</title>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="main-header">
                <div class="header-title">
                    <i class="fas fa-box-open"></i>
                    <h1>Produtos em Estoque</h1>
                    <p>Gerencie todos os produtos do seu estoque</p>
                </div>
                <button class="btn-novo-produto">
                    <i class="fas fa-plus"></i> Novo Produto
                </button>
            </header>

            <section class="inventory-controls">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Pesquisar produto...">
                </div>
                <div class="filter-dropdown">
                    <i class="fas fa-filter"></i>
                    <select>
                        <option value="todos">Todos</option>
                        <option value="em-estoque">Em Estoque</option>
                        <option value="sem-estoque">Sem Estoque</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </section>

            <section class="product-grid">
                <div class="product-card">
                    <div class="stock-badge in-stock">Em estoque</div>
                    <img src="../img/pouse_semfio.png" alt="Mouse sem fio Logitech">
                    <div class="card-content">
                        <h2>Mouse sem fio Logitech</h2>
                        <p class="product-desc">Mouse ergonômico USB, cor prata, 1000 DPI</p>
                        <div class="product-details">
                            <p>SKU:ELE-001</p>
                            <div class="price-qty">
                                <span class="quantity">25 un</span>
                                <span class="price">R$ 89,90</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-action btn-edit"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn-action btn-exclude"><i class="fas fa-trash-alt"></i> Excluir</button>
                            <button class="btn-action btn-comment"><i class="fas fa-comment-alt"></i> Comentários</button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="stock-badge low-stock">Estoque baixo</div>
                    <img src="../img/fone_ouvido.png" alt="Fone de Ouvido Bluetooth">
                    <div class="card-content">
                        <h2>Fone de Ouvido Bluetooth</h2>
                        <p class="product-desc">Fone over-ear, cancelamento de ruído, bateria 20h</p>
                        <div class="product-details">
                            <p>SKU:ELE-002</p>
                            <div class="price-qty">
                                <span class="quantity">25 un</span>
                                <span class="price">R$ 249,90</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-action btn-edit"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn-action btn-exclude"><i class="fas fa-trash-alt"></i> Excluir</button>
                            <button class="btn-action btn-comment"><i class="fas fa-comment-alt"></i> Comentários</button>
                        </div>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="stock-badge out-stock">Sem estoque</div>
                    <img src="../img/webcam_full.png" alt="Webcam Full HD">
                    <div class="card-content">
                        <h2>Webcam Full HD</h2>
                        <p class="product-desc">Câmera 1080p com microfone embutido</p>
                        <div class="product-details">
                            <p>SKU:ELE-008</p>
                            <div class="price-qty">
                                <span class="quantity zero-stock">0 un</span>
                                <span class="price">R$ 179,90</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-action btn-edit"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn-action btn-exclude"><i class="fas fa-trash-alt"></i> Excluir</button>
                            <button class="btn-action btn-comment"><i class="fas fa-comment-alt"></i> Comentários</button>
                        </div>
                    </div>
                </div>

                <div class="product-card">
                    <div class="stock-badge in-stock">Em estoque</div>
                    <img src="../img/furadeira_eletrica.png" alt="Furadeira Elétrica">
                    <div class="card-content">
                        <h2>Furadeira Elétrica</h2>
                        <p class="product-desc">Furadeira de impacto 55MM</p>
                        <div class="product-details">
                            <p>SKU: FER-001</p>
                            <div class="price-qty">
                                <span class="quantity">25un</span>
                                <span class="price">R$ 89,90</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="btn-action btn-edit"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn-action btn-exclude"><i class="fas fa-trash-alt"></i> Excluir</button>
                            <button class="btn-action btn-comment"><i class="fas fa-comment-alt"></i> Comentários</button>
                        </div>
                    </div>
                </div>

                </section>
        </main>
    </div>

    <script src=" js/script.js"></script>
</body>
</html>