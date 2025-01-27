<?php
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once 'csrfPROTECAO.php';
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoAdmin();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Usuário</title>
    <link rel="stylesheet" href="removedor.css">
    <script>
        function usuarios() {
            location.href = "usuarios.php";
        }

        function adicionarMedico() {
            location.href = 'adicionarMedico.php';
        }

        function home() {
            location.href = "admin.php";
        }

        function perfil() {
            location.href = 'perfilAdm.php';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formulario');
            const modal = document.getElementById('modalConfirmacao');
            const btnConfirmar = document.getElementById('btnConfirmar');
            const btnCancelar = document.getElementById('btnCancelar');

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                modal.style.display = 'block';
            });

            btnConfirmar.addEventListener('click', function () {
                modal.style.display = 'none';
                form.submit();
            });

            btnCancelar.addEventListener('click', function () {
                modal.style.display = 'none';
            });
        });
        const senhaInput = document.getElementById('senha');
        const mostrarSenhaBtn = document.getElementById('mostrarSenha');


        mostrarSenhaBtn.addEventListener('click', function () {

            if (senhaInput.type === 'password') {

                senhaInput.type = 'text';
                mostrarSenhaBtn.classList.remove('fa-eye');
                mostrarSenhaBtn.classList.add('fa-eye-slash')
            } else {

                senhaInput.type = 'password';
                mostrarSenhaBtn.classList.remove('fa-eye-slash');
                mostrarSenhaBtn.classList.add('fa-eye')
            }
        });
    </script>
</head>

<body>
    <header>
        <nav>
            <button type="button" onclick="usuarios()">Tabela de usuários</button>
            <button type="button" onclick="adicionarMedico()">Adicionar médico</button>
            <button type="button" onclick="home()">Home</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <article>
            <form id="formulario" action="<?php echo htmlspecialchars('removedorBackAnd.php'); ?>" method="POST">

                <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">

                <label for="id">Digite o ID do usuário:</label>
                <input type="number" name="id" id="id" placeholder="Digite o ID para deletar" required><br>
                <label for="senha">Confirme sua senha:</label>
                <input type="password" name="senha" id="senha" placeholder="Digite sua senha" required><br>
                <input type="submit" value="Excluir" name="enviar"><br>

                <?php $mensagem = GerenciadorSessao::getMensagem();
                if ($mensagem) {
                    echo $mensagem;
                } ?>

            </form>


            <div id="modalConfirmacao"
                style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
                <div
                    style="background: #fff; padding: 20px; max-width: 400px; margin: 100px auto; text-align: center; border-radius: 8px;">
                    <p>Tem certeza de que deseja excluir este usuário? Essa ação não pode ser desfeita.</p>
                    <button id="btnConfirmar" style="margin-right: 10px;">Confirmar</button>
                    <button id="btnCancelar">Cancelar</button>
                </div>
            </div>
        </article>
    </main>
</body>

</html>