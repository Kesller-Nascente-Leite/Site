<?php
require "verifica_sessao.php";
require "agendamentophp.php";
require_once "Session_start.php";
require_once "GerenciadorDeSessoes.php";
if (isset($_SESSION['id']) && strtolower(trim($_SESSION['tipo_usuario'])) == 'paciente') {

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
    <link rel="stylesheet" href="agendamento.css">
    <title>Agendamento</title>
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
            <button type="button" name="home" onclick="home()">Home</button>
            <button type="button" onclick="atendimento()">Atendimentos</button>
            <button type="button" onclick="agendamento()">Agendamento</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <article>
            <center>
                <h2>Agendamentos</h2>
                <br>
                <?php

                $agendamento->mostrandoAgendamento();
                echo "<br>";
                $mensagem = GerenciadorSessao::getMensagem();
                if ($mensagem) {
                    echo "<p>$mensagem</p>";
                }
                ?>
            </center>
        </article>
    </main>
</body>

</html>