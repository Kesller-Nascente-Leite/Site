<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once "../../configdb.php";


if (isset($_SESSION['id']) && strtolower(trim($_SESSION['tipo_usuario'])) == 'admin') {

} else {
    GerenciadorSessao::setMensagem("login Necessario");
    GerenciadorSessao::redirecionar("index.php");
    GerenciadorSessao::limparSessao();
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="adicionarMedico.css">
    <title>Add Medicos</title>
    <script>
        function usuarios() {
            location.href = "usuarios.php";
        }

        function admin() {
            location.href = 'admin.php';
        }
        function removedor() {
            location.href = 'removedor.php';
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
            <button type="button" name="usuarios" onclick="usuarios()">Tabela de Usuarios</button>
            <button type="button" onclick="admin()">home</button>
            <button type="button" onclick="removedor()">Remover Medicos/Pacientes</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <article>
            <center>
                <h1>Adicionar Medico</h1><br>
                <div id="container">

                    <form method="POST" autocomplete="off"
                        action="<?php echo htmlspecialchars('adicionarMedicophp.php'); ?>">

                        <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">
                        <label for="nome">Nome completo do Medico:</label>

                        <input type="text" placeholder="Nome" name="nome" id="nome" minlength="2" maxlength="50"
                            aria-label="Nome do paciente" required>

                        <br>
                        <label for="email">Email do Medico:</label>
                        <input type="email" placeholder="Email" name="email" id="email" maxlength="100" required>
                        <br>

                        <label for="psenha">Senha:</label>
                        <input type="password" placeholder="No mínimo 8 caracteres com letras e números" name="senha"
                            id="senha" minlength="8" maxlength="50" required>
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

                        <label for="consulta">Escolha a especialização:</label>
                        <Select name="especializacao" id="especializacao" required>
                            <?php
                            try {
                                $cont = 0;
                                $query = "SELECT id, especializacao FROM especializacao";
                                $stmt = $conn->query($query);
                                while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$linha['id']}'>{$linha['especializacao']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option value=''>Erro ao carregar especializações</option>";
                            }

                            ?>
                        </Select>
                        <input type="submit" name="enviar" id="enviar" value="Cadastrar">



                        </Select>
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