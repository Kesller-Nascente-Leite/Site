<?php
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once '../../configdb.php';
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoAdmin();
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
        
        function home() {
            location.href = "admin.php";
        }
        function adicionarMedico() {
            location.href = 'adicionarMedico.php';
        }
        function removedor() {
            location.href = 'removedor.php';
        }
        function usuarios() {
            location.href = "usuarios.php";
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
            <button type="button" onclick="usuarios()">Tabela de usuarios</button>
            <button type="button" onclick="adicionarMedico()">Adicionar medico</button>
            <button type="button" onclick="removedor()">Remover Medicos/Pacientes</button>
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

                <h1>Informações Do Admin</h1>
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