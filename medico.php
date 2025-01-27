<?php
require 'medicoBackAnd.php';

Autenticacao::AutenticacaoMedico();
$usuario = new MostrandoUsuarios($conn);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de usuarios</title>
    <link rel="stylesheet" href="usuarios.css">
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

        function perfil() {
            location.href = "perfilMedico.php";
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
            <button type="button" name="usuarios" onclick="perfil()">perfil</button>
        </nav>
    </header>
    
    <main>
        <h1>Lista de Pacientes</h1>
        <form method="GET" action="#">
            <input type="text" name="pesquisa" placeholder="Pesquisar..."
                value="<?php echo htmlspecialchars($filtro = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : ""); ?>">
            <button type="submit">Pesquisar</button>
        </form>

        </form>

        <?php

        $pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
        $limite = 50;
        $offset = ($pagina - 1) * $limite;
        $usuario->usuarios($filtro, $offset, $limite);
        echo '<div id="paginacao">';
        if ($pagina > 1) {
            echo '<a href="?pagina=' . ($pagina - 1) . '&pesquisa=' . urlencode($filtro) . '">⬅ Anterior</a>';
        }
        echo '<span>Página ' . $pagina . '</span>';
        echo '<a href="?pagina=' . ($pagina + 1) . '&pesquisa=' . urlencode($filtro) . '">Próxima ➡</a>';
        echo '</div>';
        ?>

    </main>
</body>

</html>