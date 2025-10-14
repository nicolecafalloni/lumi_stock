<?php

include 'conexao.php';
include 'verifica_sessao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verifica se foi enviado um arquivo
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    } else {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Selecione uma imagem antes de salvar.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
        };
        </script>
        ";
        exit();
    }
    
    $nome = $_POST['nome'];
    $sku = $_POST['sku'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    $verifica = $conexao->prepare("SELECT id FROM produtos WHERE nome = ?");
    $verifica->bind_param("s", $nome);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Produto já cadastrado.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            })
        };
        </script>
        ";
        exit();
    } else {

        $sql = "INSERT INTO produtos (imagem, nome, sku, descricao, quantidade, preco, categoria) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conexao->error);
        }
    
        $stmt->bind_param("bsssids", $imagem, $nome, $sku, $descricao, $quantidade, $preco, $categoria);
        $stmt->send_long_data(0, $imagem);

        if ($stmt->execute()) {
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            window.onload = function() {
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'O produto foi cadastrado.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = 'produtos.php';
                });
            };
            </script>
            ";
        } else {
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Não foi possível cadastrar o produto.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            };
            </script>
            ";
        }
        $stmt->close();
    }

    $verifica->close();
    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar produto - LumiStock</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/cadastrar_produtos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" type="image/jpg" href="../img/icon-logo.jpg">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

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
            <li><a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a></li>
        </ul>
    </nav>

    <div class="logout-btn">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>
</aside>

    <?php include 'sidebar.php'; ?>

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
    <main class="main-content">
    <header class="page-header">
        <button class="back-btn"><i class="fas fa-arrow-left"></i></button>
        <div class="titulo-produto">
            <h1>Novo Produto</h1>
            <p>Cadastre um novo produto no estoque</p>
        </div>
    </header>

    <section class="form-section">
        <h2>Informações do Produto</h2>

        <form class="product-form" id="formProduto" method="POST" enctype="multipart/form-data">
            
            <!-- Upload da imagem -->
        <div class="form-group image-upload">
            <label class="labels-form-group" for="imagem">Imagem do Produto</label>
            <div class="image-upload-icons">
                <div class="upload-box" id="uploadBox">
                    <!-- Ícones antes do upload -->
                    <i id="icon-upload" class="fas fa-upload"></i>
                    <p id="text-upload">Clique para fazer upload</p>
    
                    <!-- Input do upload -->
                    <input type="file" id="imagem" name="imagem" accept="image/*" style="display: none;">
    
                    <!-- Ícones depois do upload -->
                    <div id="overlay-after">
                        <i id="icon-upload-after" class="fas fa-upload"></i>
                        <p id="text-upload-after">Trocar imagem</p>
                    </div>
                </div>
            </div>
            </div>

            <!-- Campos de texto -->
            <div class="form-group">
                <label class="labels-form-group" for="nome">Nome do Produto</label>
                <input type="text" id="nome" name="nome" placeholder="Ex: Mouse sem fio Logitech" required>
            </div>

            <div class="form-group">
                <label class="labels-form-group" for="sku">Código SKU</label>
                <input type="text" id="sku" name="sku" placeholder="Ex: SKU-001" required>
            </div>

            <div class="form-group">
                <label class="labels-form-group" for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Ex: Mouse ergonômico USB, cor preta..." rows="3" required></textarea>
            </div>

            <!-- Quantidade e preço -->
            <div class="form-row">
                <div class="form-group">
                    <label class="labels-form-group" for="quantidade">Quantidade *</label>
                    <input type="number" id="quantidade" name="quantidade" min="0" value="" required>
                </div>
                <div class="form-group">
                    <label class="labels-form-group" for="preco">Preço (R$) *</label>
                    <input type="number" id="preco" name="preco" min="0" step="0.01" value="" required>
                </div>
            </div>

            <!-- Categoria -->
            <div class="form-group">
                <label class="labels-form-group" for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="" disabled selected>Selecione uma categoria</option>
                    <option>Eletrônicos</option>
                    <option>Escritório</option>
                </select>
            </div>

            <!-- Botões -->
            <div class="form-actions">
                <button type="submit" class="btn-salvar"><i class="fas fa-save"></i> Salvar Produto</button>
            </div>
        </form>
    </section>
</main>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formProduto");
    const uploadBox = document.getElementById('uploadBox');
    const inputImagem = document.getElementById('imagem');
    const iconUpload = document.getElementById('icon-upload');
    const textUpload = document.getElementById('text-upload');
    const iconUploadAfter = document.getElementById('icon-upload-after');
    const textUploadAfter = document.getElementById('text-upload-after');

    form.addEventListener("submit", (e) => {
        if (!inputImagem.files || inputImagem.files.length === 0) {
            e.preventDefault();
            Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Selecione uma imagem antes de salvar.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            return false;
        }
    });

    // Ao clicar na área, abre o seletor de arquivo
    uploadBox.addEventListener('click', () => {
        inputImagem.click();
    });

    inputImagem.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                // Esconde os elementos iniciais
                iconUpload.style.display = "none";
                textUpload.style.display = "none";

                // Mostra os elementos "depois do upload"
                iconUploadAfter.style.display = "flex";
                textUploadAfter.style.display = "flex";

                // Remove qualquer imagem antiga antes de adicionar a nova
                const oldPreview = uploadBox.querySelector('.preview-img');
                if (oldPreview) oldPreview.remove();

                // Cria e adiciona a nova imagem
                const preview = document.createElement('img');
                preview.src = reader.result;
                preview.classList.add('preview-img');
                uploadBox.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
<script src="../js/script.js"></script>
</body>
</html>