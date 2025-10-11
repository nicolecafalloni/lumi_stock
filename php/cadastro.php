<?php
include_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $verifica = $conexao->prepare("SELECT id FROM users WHERE email = ?");
    $verifica->bind_param("s", $email);
    $verifica->execute();
    $verifica->store_result();

    if ($verifica->num_rows > 0) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'error',
                title: 'E-mail já cadastrado!',
                text: 'Por favor, use outro endereço de e-mail.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            }).then(() => {
                window.location.href = '../index.php';
            });
        };
        </script>
        ";
        exit();
    } else {

        $sql = "INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conexao->error);
        }
    
        $stmt->bind_param("sss", $nome, $email, $senhaHash);
    
    
        if ($stmt->execute()) {
            echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            window.onload = function() {
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'O registro foi salvo com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = '../index.php';
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
                    text: 'Ocorreu um erro ao salvar o registro.',
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
    <title>Cadastro - LumiStock</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>

<body class="body-cadastro">
    <div class="container">
        <div class="left-panel-cadastro">
            <h2>Criar Conta</h2>
            <div class="social-icons">
                <div class="icon">
                    <img class="img-icon-facebook" src="../img/icon-facebook.png" alt="">
                </div>
                <div class="icon">
                    <img class="img-icon-google" src="../img/icon-google.png" alt="">
                </div>
                <div class="icon">
                    <img class="img-icon-linkedin" src="../img/icon-linkedin.png" alt="">
                </div>
            </div>
            
            <form action="" method="post">
                <div class="input-group">
                    <div class="icon-input">
                        <img class="img-input-user" src="../img/icon-usuario.png" alt="">
                    </div>
                    <input type="text" id="nome" name="nome" placeholder="Nome" required>
                </div>
                
                <div class="input-group">
                    <div class="icon-input">
                        <img class="img-input-email" src="../img/icon-email.png" alt="">
                    </div>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <div class="icon-input">
                        <img class="img-input-senha" src="../img/icon-senha.png" alt="">
                    </div>
                    <input type="password" id="senha" name="senha" placeholder="Senha" required>
                </div>
                
                <input type="submit" class="btn-primary" value= "Cadastre-se">
            </form>
        </div>
        <div class="right-panel-cadastro">
            <img class="logo-login" src="../img/logo-lumistock.png" alt="">
            <h1>Acessar sua conta</h1>
            <center><p>Para se manter conectado conosco, forneça suas informações pessoais</p></center>
            <a href="../index.php"><button class="btn-outline">Entrar</button></a>
        </div>
    </div>
</body>

</html>