<?php
$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "lumistock";

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

echo "Conectado com sucesso!";
?>