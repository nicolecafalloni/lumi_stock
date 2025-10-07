<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LumiStock</title>
    <link rel="stylesheet" href="./css/style.css">
    <script src="./js/main.js"></script>
</head>

<body class="body-login">
    <div class="container">
        <div class="left-panel">
            <img class="logo-login" src="./img/logo-lumistock.png" alt="">
            <h1>Bem-vindo de volta!</h1>
            <p>Para se manter conectado conosco, forneça suas informações pessoais</p>
            <a href="./php/login.php"><button class="btn-outline">Entrar</button></a>
        </div>

        <div class="right-panel">
            <h2>Criar Conta</h2>
            <div class="social-icons">
                <div class="icon">
                    <img class="img-icon-facebook" src="./img/icon-facebook.png" alt="">
                </div>
                <div class="icon">
                    <img class="img-icon-google" src="./img/icon-google.png" alt="">
                </div>
                <div class="icon">
                    <img class="img-icon-linkedin" src="./img/icon-linkedin.png" alt="">
                </div>
            </div>

            <form>
                <div class="input-group">
                    <div class="icon-input">
                        <img class="img-input-user" src="./img/icon-usuario.png" alt="">
                    </div>
                    <input type="text" name="nome" placeholder="Nome" required>
                </div>

                <div class="input-group">
                    <div class="icon-input">
                        <img class="img-input-email" src="./img/icon-email.png" alt="">
                    </div>
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <div class="icon-input">
                        <img class="img-input-senha" src="./img/icon-senha.png" alt="">
                    </div>
                    <input type="password" name="senha" placeholder="Senha" required>
                </div>

                <button type="submit" class="btn-primary">Cadastre-se</button>
            </form>
        </div>
    </div>
</body>

</html>