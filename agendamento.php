<?php
require "verifica_sessao.php";
require "agendamentoBackAnd.php";
require_once "Session_start.php";
require_once "GerenciadorDeSessoes.php";
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoPaciente();

$agendamento = new Agendamento($conn, $_SESSION['id']);
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
        function prescricao() {
            location.href = 'prescricao.php';
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
            <button type="buttom" onclick="prescricao()">Prescrição</button>
            <button type="button" onclick="Protuario()">Protuario</button>
            <button type="button" onclick="agendamento()">Agendamento</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <article>
            <center>
                <h2>Agendamentos</h2>
            </center>
            <br>
            <form method="GET" action="#">
                <select id='botaoAgendamento' name='botaoAgendamento'>
                    <option value='' disabled selected>Selecione a ordem</option>
                    <option value='1'>Última Consulta</option>
                    <option value='2'>Concluídos</option>
                    <option value='3'>Pendente</option>
                    <option value='4'>Cancelado</option>
                    <option value='5'>Em espera</option>
                </select>
                <button type="submit">Ordenar</button>
            </form>
            <center>
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