<?php
require 'atendimentoEmEsperaBackAnd.php';
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoMedico();
$consulta = new Consulta($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">

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
            <button type="button" onclick="AdicionarPrescricao()">Adicionar Prescrição</button>
            <button type="button" onclick="adicionarProntuario()">Adicionar Prontuário</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>

    <main>
        <article>
            <h1>Lista de Atendimento em Espera</h1>

            <form method="GET" action="#">
                <input type="text" name="pesquisaEspera" placeholder="Pesquisar..."
                    value="<?php echo htmlspecialchars($filtro = $_GET['pesquisaEspera'] ?? ""); ?>">
                <button type="submit">Pesquisar</button>
            </form>

            <?php
            $paginaEspera = $_GET['pagina_espera'] ?? 1;
            $paginaConcluido = $_GET['pagina_concluido'] ?? 1;
            $limite = 50;
            $offsetEspera = ($paginaEspera - 1) * $limite;
            $offsetConcluido = ($paginaConcluido - 1) * $limite;

            echo $consulta->listarConsultasEmEspera($filtro, $offsetEspera, $limite);
            ?>


            <div id="paginacao">
                <?php if ($paginaEspera > 1): ?>
                    <a href="?pagina_espera=<?php echo $paginaEspera - 1; ?>&pesquisa=<?php echo urlencode($filtro); ?>">⬅ Anterior</a>
                <?php endif; ?>

                <span>Página <?php echo $paginaEspera; ?></span>

                <a href="?pagina_espera=<?php echo $paginaEspera + 1; ?>&pesquisa=<?php echo urlencode($filtro); ?>">Próxima ➡</a>
            </div>


            <?php $mensagem = GerenciadorSessao::getMensagem();
            if ($mensagem): ?>
                <center>
                    <p class="mensagem"><?php echo $mensagem; ?></p>
                </center>
            <?php endif; ?>

        </article>

        <article>
            <h1>Lista de Atendimento Concluido</h1>

            <form method="GET" action="#">
                <input type="text" name="pesquisa_Concluida" placeholder="Pesquisar..."
                    value="<?php echo htmlspecialchars($filtro = $_GET['pesquisa_Concluida'] ?? ""); ?>">
                <button type="submit">Pesquisar</button>
            </form>
            <?php

            echo $consulta->listarConsultasConcluidas($filtro, $offsetConcluido, $limite);
            ?>


            <div id="paginacao">
                <?php if ($paginaConcluido > 1): ?>
                    <a href="?pagina_concluido=<?php echo $paginaConcluido - 1; ?>&pesquisa=<?php echo urlencode($filtro); ?>">⬅
                        Anterior</a>
                <?php endif; ?>

                <span>Página <?php echo $paginaConcluido; ?></span>

                <a href="?pagina_concluido=<?php echo $paginaConcluido + 1; ?>&pesquisa=<?php echo urlencode($filtro); ?>">Próxima
                    ➡</a>
            </div>


            <?php $mensagem = GerenciadorSessao::getMensagem();
            if ($mensagem): ?>
                <center>
                    <p class="mensagem"><?php echo $mensagem; ?></p>
                </center>
            <?php endif; ?>
        </article>
    </main>

</body>

</html>