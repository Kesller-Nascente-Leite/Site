<?php
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once '../../configdb.php';
require_once 'verificaAutenticacao.php';
require 'verifica_sessao.php';
Autenticacao::AutenticacaoMedico();

$nascimento = date("d-m-Y", strtotime($_SESSION['data_nascimento']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="perfil.css">
    <script>

        function AtendimentoEmEspera() {
            location.href = "atendimentoEmEspera.php";
        }

        function AdicionarPrescricao() {
            location.href = 'adicionarPrescricao.php';
        }
        function adicionarProntuario() {
            location.href = 'adicionarProntuario.php';
        }

        function home() {
            location.href = "medico.php";
        }
        document.addEventListener('DOMContentLoaded', function () {
            const buttonCliqueAqui = document.getElementById('Clique_aqui');
            const formulario = document.getElementById('formulario');

            buttonCliqueAqui.addEventListener('click', function () {
                formulario.style.display = 'block';
                buttonCliqueAqui.remove();
            });
        });
    </script>
</head>

<body>

    <header>
        <nav>
            <button type="button" name="usuarios" onclick="AtendimentoEmEspera()">Atendimento Em Espera</button>
            <button type="button" onclick="AdicionarPrescricao()">Adicionar prescricao</button>
            <button type="button" onclick="adicionarProntuario()">AdicionarProntuario</button>
            <button type="button" name="usuarios" onclick="home()">Home</button>
        </nav>
    </header>
    <main>
        <article>

            <div id="container">
                <form action="#" method="post">
                    <button type="submit" name="sair" id="sair">Sair</button>
                    <?php
                    if (isset($_POST['sair'])) {
                        GerenciadorSessao::limparSessao();
                        GerenciadorSessao::redirecionar("index.php");
                        exit();
                    }
                    ?>
                </form>

                <h1>Informações Do Medico</h1>
                <?php
                
                    echo "<h1>Bem-vindo {$_SESSION['nome']}!</h1>";
                    echo "<p>Email: {$_SESSION['email']}</p>";
                    echo "<p>Telefone: {$_SESSION['telefone']}</p>";
                    echo "<p>Data de Nascimento: " . $nascimento . "</p>";
                    $sexo = $_SESSION['sexo'] == '1' ? 'Masculino' : 'Feminino';
                    echo "<p>Sexo: $sexo</p>";
                

                $mensagem = GerenciadorSessao::getMensagem();
                if ($mensagem) {
                    echo "<p>$mensagem</p>";
                }
                ?>
            </div>
        </article>
    </main>

</body>

</html>