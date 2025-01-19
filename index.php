<?php
session_start();
require_once "csrfPROTECAO.php";
require_once "GerenciadorDeSessoes.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">


</head>
<script>

    function home() {
        location.href = "site.php";
    }

    function atendimento() {
        location.href = 'atendimento.php';
    }
    function agendamento() {
        location.href = 'agendamento.php';
    }

    function perfil() {
        location.href = "perfil.php";
    }
</script>

<body>
    <!--Falta o javaScrip-->
    <header>
        <nav>

            <button type="button" name="home" onclick="home()">Home</button>
            <button type="button" onclick="atendimento()">Atendimentos</button>
            <button type="button" onclick="agendamento()">Agendamento</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>

    <main>
        <article>
            <center>
                <h1>Bem-Vindo</h1><br>
                <div id="container">
                    <form method="POST" autocomplete="on" action="<?php echo htmlspecialchars('loginphp.php'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">
                        <label for="pemail">Email:
                            <input type="email" name="email" id="pemail" placeholder="Email" required>
                        </label><br>

                        <label for="psenha">Senha:
                            <input type="password" placeholder="Senha" name="senha" id="psenha" minlength="8"
                                maxlength="50" required>
                        </label><br>



                        <input type="submit" name="submit" id="penviar" value="Entrar"><br>
                        <?php
                        $mensagem = GerenciadorSessao::getMensagem();
                        if ($mensagem) {
                            echo "<p id='erro'>$mensagem</p>";

                        }
                        ?>
                        <!--Deixar menor e botar para um canto--><a href="atuSenha.php" id="Atualizar_senha">Esqueci minha senha!</a>
                        <a href="cadastro.php" id="cadastro">Cadastrar-se!</a>

                        <br>

                    </form>

                </div>
            </center>
        </article>
    </main>


</body>

</html>