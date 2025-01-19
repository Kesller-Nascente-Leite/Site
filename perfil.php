<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";

if (isset($_SESSION['id']) && strtolower(trim($_SESSION['tipo_usuario'])) == 'paciente') {
    $nome = $_SESSION['nome'];
    $nascimento = date("d-m-Y", strtotime($_SESSION['data_nascimento']));

} else {
    $_SESSION['msg'] = 'login Necessario';
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="perfil.css">
    <script>
        //criando funcionalidade do botão para redirecionar para as outras abas
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

            <button type="button" name="home" onclick="home()">Home</button>
            <button type="button" onclick="atendimento()">Atendimentos</button>
            <button type="button" onclick="agendamento()">Agendamento</button>
            <button type="button" onclick="perfil()">Perfil</button>
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

                <h1>Informações Do Paciente</h1>
                <?php
                if (isset($_SESSION['id'])) {
                    echo "<h1>Bem-vindo {$_SESSION['nome']}!</h1>";
                    echo "<p>Email: {$_SESSION['email']}</p>";
                    echo "<p>Telefone: {$_SESSION['telefone']}</p>";
                    echo "<p>Data de Nascimento: " . $nascimento . "</p>";
                    $sexo = $_SESSION['sexo'] == 'M' ? 'Masculino' : 'Feminino';
                    echo "<p>Sexo: $sexo</p>";
                } else {
                    echo "Usuário não autenticado.";
                    header("Location: index.php");
                    exit();
                }
                ?>


                <label for="Deletar a conta">Deletar a conta</label>
                <input type="button" id="Clique_aqui" value="Clique aqui"><br>
                <form id="formulario" action="<?php echo htmlspecialchars('perfilphp.php'); ?>" method="post">
                    <br>
                    <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">
                    <input type="password" placeholder="Confirme a Sua senha" name="senha" id="psenha" required><br>
                    <input type="submit" name="delete" id="delete" value="Confirmar">
                </form>
                <?php
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