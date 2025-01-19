<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";

if (isset($_SESSION['id']) && strtolower(trim($_SESSION['tipo_usuario'])) == 'admin') {

} else {
    GerenciadorSessao::setMensagem("login Necessario");
    GerenciadorSessao::redirecionar("index.php");
    GerenciadorSessao::limparSessao();
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>

    <link rel="stylesheet" href="admin.css">
    <script>
        function home() {
            location.href = "site.php";
        }

        function atendimento() {
            location.href = 'atendimento.php';
        }
        function agendamento() {
            location.href = 'agendamento.php';
        }

        function perfil() {
            location.href = "perfil.php";
        }
    </script>
</head>

<body>
    <header>
        <nav>
            <button type="button" name="home" onclick="home()">Tabela de Usuarios</button>
            <button type="button" onclick="atendimento()">Adicionar medico</button>
            <button type="button" onclick="agendamento()">Remover Medicos/Pacientes</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
</body>

</html>