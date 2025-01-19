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
    <title>Hospital</title>
    <link rel="stylesheet" href="cadastro.css">

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

        document.addEventListener("DOMContentLoaded", function () {
            const dataInput = document.getElementById("nascimento");
            const hoje = new Date();
            const limiteMinimo = new Date();
            limiteMinimo.setFullYear(hoje.getFullYear() - 100);
            dataInput.min = limiteMinimo.toISOString().split("T")[0];
        });
    </script>
</head>

<body>

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
                <h1>Cadastro</h1><br>
                <div id="container">

                    <form method="POST" autocomplete="off" action="<?php echo htmlspecialchars('cadastrophp.php'); ?>">

                        <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">
                        <label for="nome">Nome completo do Paciente:</label>
                        <input type="text" placeholder="Nome" name="nome" id="nome" minlength="2" maxlength="50"
                            aria-label="Nome do paciente" required>

                        <br>
                        <label for="email">Email:</label>
                        <input type="email" placeholder="Email" name="email" id="email" maxlength="100" required>
                        <br>

                        <label for="psenha">Senha:</label>
                        <input type="password" placeholder="No mínimo 8 caracteres com letras e números" name="senha"
                            id="senha" minlength="8" maxlength="50"required>
                        <br>

                        <label for="Data de nascimento">Data de Nascimento</label>
                        <input type="date" name="nascimento" min="1900-01-01" id="nascimento" required>
                        <br>

                        <label for="Telefone">Telefone</label>
                        <input type="tel" name="telefone" id="tel" minlength="8" maxlength="12"
                            placeholder="XX XXXX-XXXX" required><br>
                            
                        <label for="sexo">Sexo:</label><br>
                        <select name="sexo" id="sexo">
                            <option value="1">Masculino</option>
                            <option value="2">Feminino</option>
                        </select><br>

                        <input type="submit" name="enviar" id="enviar" value="Cadastrar">

                        <!-- Exibe a mensagem de erro aqui -->
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