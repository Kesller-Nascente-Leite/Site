<?php
require 'prescricaoBackAnd.php';
require_once 'verificaAutenticacao.php';
Autenticacao::AutenticacaoPaciente();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescrição</title>
    <link rel="stylesheet" href="prescricao.css">
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

            <button type="button" onclick="home()">Home</button>
            <button type="buttom" onclick="prescricao()">Prescrição</button>
            <button type="button" onclick="Protuario()">Protuario</button>
            <button type="button" onclick="agendamento()">Agendamento</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <article>
            <?php
            $mostrandoprescricao = new MostrandoPrescricao($conn);
            echo $mostrandoprescricao->mostrarPrescricao();
            ?>
        </article>
    </main>

</body>

</html>