<?php
require 'usuariosphp.php';
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
        function home() {
            location.href = "admin.php";
        }
        function adicionarMedico() {
            location.href = 'adicionarMedico.php';
        }
        function removedor() {
            location.href = 'removedor.php';
        }
        function perfil() {
            location.href = "perfil.php";
        }
    </script>
</head>

<body>
    <header>
        <nav>
            <button type="button" name="usuarios" onclick="home()">Home</button>
            <button type="button" onclick="adicionarMedico()">Adicionar medico</button>
            <button type="button" onclick="removedor()">Remover Medicos/Pacientes</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <h1>Lista de Usuários</h1>
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