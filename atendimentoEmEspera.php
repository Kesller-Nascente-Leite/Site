<?php
require 'atendimentoEmEsperaBackAnd.php';
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoMedico();
$usuario = new MostrandoUsuarios($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Atendimento</title>
    <link rel="stylesheet" href="atendimentosEmEspera.css">
    <script>
        function home() {
            location.href = "medico.php";
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
    </script>
</head>

<body>
    <header>
        <nav>
            <button type="button" onclick="home()">Home</button>
            <button type="button" onclick="AdicionarPrescricao()">Adicionar Prescricao</button>
            <button type="button" onclick="adicionarProntuario()">Adicionar Prontuário</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>

    <main>
        <h1>Lista de Atendimento em Espera</h1>
        <form method="GET" action="#">
            <input type="text" name="pesquisa" placeholder="Pesquisar..."
                value="<?php echo htmlspecialchars($filtro = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : ""); ?>">
            <button type="submit">Pesquisar</button>
        </form>

        <?php
        $pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
        $limite = 50;
        $offset = ($pagina - 1) * $limite;

        $usuario->usuarios($filtro, $offset, $limite);
        ?>

        <div id="paginacao">
            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?>&pesquisa=<?php echo urlencode($filtro); ?>">⬅ Anterior</a>
            <?php endif; ?>
            <span>Página <?php echo $pagina; ?></span>
            <a href="?pagina=<?php echo $pagina + 1; ?>&pesquisa=<?php echo urlencode($filtro); ?>">Próxima ➡</a>

            <?php $mensagem = GerenciadorSessao::getMensagem();
            if ($mensagem) {
                echo $mensagem;
            } ?>

        </div>
    </main>
</body>

</html>