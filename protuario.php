<?php
require_once "Session_start.php";
require_once 'protuarioBackAnd.php';
require "verifica_sessao.php";
require_once "GerenciadorDeSessoes.php";
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoPaciente();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="protuario.css">
    <title>Meus atendimentos</title>
    <script>
        function home() {
            location.href = "site.php";
        }
        function Protuario() {
            location.href = 'protuario.php';
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
            <button type="button" onclick="Protuario()">Prontuario</button>
            <button type="button" onclick="agendamento()">Agendamento</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <article>
            <center>
                <h2>Protuario Adicionadas</h2>
                <br>
                <?php

                $protuario->historico();
                echo "<br><p>Total de Prontuarios: " . $protuario->totalDeAtendimentos() . "</p>";

                ?>
            </center>
        </article>
    </main>

</body>

</html>