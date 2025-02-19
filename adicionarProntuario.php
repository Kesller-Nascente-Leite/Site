<?php
require_once 'GerenciadorDeSessoes.php';
require_once 'csrfPROTECAO.php';
require_once 'verificaAutenticacao.php';
require 'verifica_sessao.php';
Autenticacao::AutenticacaoMedico()
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Prontuário</title>
    <link rel="stylesheet" href="adicionarProntuario.css">
    <script>
        function AtendimentoEmEspera() {
            location.href = "atendimentoEmEspera.php";
        }

        function AdicionarPrescricao() {
            location.href = 'adicionarPrescricao.php';
        }
        function Home() {
            location.href = 'medico.php';
        }

        function perfil() {
            location.href = "perfilMedico.php";
        }
    </script>
</head>

<body>
    <header>
        <nav>
            <button type="button" name="usuarios" onclick="AtendimentoEmEspera()">Atendimento Em Espera</button>
            <button type="button" onclick="AdicionarPrescricao()">Adicionar Prescricao</button>
            <button type="button" onclick="Home()">Home</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>

    <main class="form-container">
        <h2>Adicionar Prontuário</h2>
        <form method="POST" action="<?php echo htmlspecialchars(' adicionarProntuarioBackAnd.php') ?>">

            <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">


            <label for="paciente_id">ID do Paciente:</label>
            <input type="number" id="paciente_id" name="paciente_id" required>

            <label for="descricao">Descrição do Prontuário:</label>
            <textarea id="descricao" name="descricao" rows="4" cols="50" required></textarea>

            <button type="submit">Adicionar Prontuário</button>
            <?php $mensagem = GerenciadorSessao::getMensagem();
            if ($mensagem) {
                echo $mensagem;
            } ?>

        </form>
    </main>
</body>

</html>