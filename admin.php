<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoAdmin();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>

    <link rel="stylesheet" href="admin.css">
    <script>
        function usuarios() {
            location.href = "usuarios.php";
        }

        function adicionarMedico() {
            location.href = 'adicionarMedico.php';
        }
        function removedor() {
            location.href = 'removedor.php';
        }

        function perfil() {
            location.href = "perfilAdm.php";
        }
    </script>
</head>

<body>
    <header>
        <nav>
            <button type="button" name="usuarios" onclick="usuarios()">Tabela de Usuarios</button>
            <button type="button" onclick="adicionarMedico()">Adicionar medico</button>
            <button type="button" onclick="removedor()">Remover Medicos/Pacientes</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <article>
            
        </article>
    </main>
</body>

</html>