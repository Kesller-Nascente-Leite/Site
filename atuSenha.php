<?php
require_once "csrfPROTECAO.php";
require_once "GerenciadorDeSessoes.php";
require_once "Session_start.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="atuSenha.css">
</head>

<body>
    <script>

function home() {
            location.href = "site.php";
        }
        function prescricao(){
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
    <header>
        <nav>

            <button type="button" name="home" onclick="home()">Home</button>
            <button type="buttom" onclick="prescricao()">Prescrição</button>
            <button type="button" onclick="Protuario()">Protuario</button>
            <button type="button" onclick="agendamento()">Agendamento</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>

    <main>
        <article>
            <center>
                <h2>Digite seu email</h2>
                <div id="container">
                    <form action="<?php echo htmlspecialchars("atuSenhaBackAnd.php") ?>" method="POST">
                        
                    <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">
                        <label for="pemail">Email:
                            <input type="email" name="email" id="pemail" placeholder="Email" required>
                        </label><br>

                        <label for="psenha">Senha:
                            <input type="password" placeholder="No mínimo 8 caracteres com letras e números"
                                name="senha" id="psenha" minlength="8" maxlength="50" required>
                        </label><br>


                        <input type="submit" name="enviar" id="penviar" value="Verificar"><br>
                        <?php
                        $mensagem = GerenciadorSessao::getMensagem();
                        if ($mensagem) {
                            echo "<p>$mensagem</p>";
                        }
                        ?>
                    </form>
                </div>
            </center>
        </article>
    </main>

</body>

</html>