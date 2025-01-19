<?php
require_once '../../configdb.php';
require_once "csrfPROTECAO.php";
require "verifica_sessao.php";
require "GerenciadorDeSessoes.php";

if (isset($_SESSION['id']) && strtolower(trim($_SESSION['tipo_usuario'])) == 'paciente') {
    $nome = $_SESSION['nome'];
    $nascimento = date("d-m-Y", strtotime($_SESSION['data_nascimento']));

} else {
    GerenciadorSessao::setMensagem("login Necessario");
    GerenciadorSessao::redirecionar("index.php");
    GerenciadorSessao::limparSessao();
    exit();
}
// Verificar se o usuário está logado e é paciente
if (isset($_SESSION['id']) && strtolower(trim($_SESSION['tipo_usuario'])) == 'paciente') {
    $nome = $_SESSION['nome'];
    $nascimento = date("d-m-Y", strtotime($_SESSION['data_nascimento']));
} else {
    GerenciadorSessao::setMensagem("login Necessário");
    GerenciadorSessao::redirecionar("index.php");
    GerenciadorSessao::limparSessao();
    exit();
}

// Função para buscar médicos pela especialização
function getMedicosByEspecializacao($especializacaoId)
{
    global $pdo;
    $query = "SELECT m.id_usuario, u.nome
            FROM medico m
            JOIN usuarios u ON m.id_usuario = u.id
            WHERE m.id_especializacao = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$especializacaoId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Verificar se é uma requisição AJAX para retornar médicos
if (isset($_GET['consulta_id'])) {
    $consultaId = $_GET['consulta_id'];
    $medicos = getMedicosByEspecializacao($consultaId);
    echo json_encode($medicos);
    exit();
}

$especializacaoId = isset($_POST['consulta']) ? $_POST['consulta'] : null;
$medicos = $especializacaoId ? getMedicosByEspecializacao($especializacaoId) : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site</title>
    <link rel="stylesheet" href="siteCSS.css">
    <script>
        //criando funcionalidade do botão para redirecionar para as outras abas
        function home() {
            location.href = "site.php";
        }

        function perfil() {
            location.href = "perfil.php";
        } function agendamento() {
            location.href = 'agendamento.php';
        }

        function atendimento() {
            location.href = 'atendimento.php';
        }
        document.addEventListener("DOMContentLoaded", function () {
            const consultaSelect = document.getElementById("consulta");
            const medicoSelect = document.getElementById("medico");

            consultaSelect.addEventListener("change", function () {
                const consultaId = consultaSelect.value;

                // Limpar as opções do médico
                medicoSelect.innerHTML = `<option value="" disabled selected>Selecione um médico</option>`;

                // Se uma consulta for selecionada, buscar os médicos via AJAX
                if (consultaId) {
                    fetch(`site.php?consulta_id=${consultaId}`)
                        .then(response => response.json())
                        .then(medicos => {
                            // Verificar se médicos foram encontrados
                            if (medicos.length > 0) {
                                medicos.forEach(medico => {
                                    const option = document.createElement("option");
                                    option.value = medico.id_usuario; // ID do médico
                                    option.textContent = medico.nome; // Nome do médico
                                    medicoSelect.appendChild(option);
                                });
                            } else {
                                medicoSelect.innerHTML = `<option value="" disabled>Sem médicos disponíveis para essa especialização</option>`;
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao buscar médicos:', error);
                        });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const dataHoraInput = document.getElementById('dataHora');
            const mensagemErro = document.getElementById('mensagem_erro');


            const dataAtual = new Date();
            const dataISO = dataAtual.toISOString().slice(0, 16);
            dataHoraInput.min = dataISO;

            dataHoraInput.addEventListener('change', function () {
                const dataSelecionada = new Date(dataHoraInput.value);
                if (dataSelecionada < dataAtual) {
                    mensagemErro.textContent = "A data e o horário selecionados não podem ser no passado.";
                    dataHoraInput.setCustomValidity('');
                } else {
                    mensagemErro.textContent = '';
                    dataHoraInput.setCustomValidity('');
                }
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
        <section>
            <center>
                <h2>Faça seu agendamento aqui!</h2>

                <form method="POST" action="sitephp.php" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?php echo Csrf::gerarToken(); ?>">

                    <label for="paciente">Nome do Paciente:</label>
                    <input type="text" placeholder="Nome Do Paciente" id="paciente" name="paciente"
                        value="<?= htmlspecialchars($nome) ?>" required> <br>

                    <label for="dataHora">Data e Horario</label>
                    <input type="datetime-local" name="dataHora" id="dataHora" require>
                    <br>

                    <label for="consulta">Escolha a consulta:</label>
                    <select id="consulta" name="consulta" required>
                        <option value="" disabled selected>Selecione uma consulta</option>
                        <option value="1" <?php echo $especializacaoId == 1 ? 'selected' : ''; ?>>Medico geral</option>
                        <option value="2" <?php echo $especializacaoId == 2 ? 'selected' : ''; ?>>Pediatria</option>
                        <option value="3" <?php echo $especializacaoId == 3 ? 'selected' : ''; ?>>Cardiologista</option>
                        <option value="4" <?php echo $especializacaoId == 4 ? 'selected' : ''; ?>>Ortopedista</option>
                        <option value="5" <?php echo $especializacaoId == 5 ? 'selected' : ''; ?>>Dermatologia</option>
                    </select>
                    <br>

                    <label for="medico">Escolha o seu Doutor/a:</label>
                    <select id="medico" name="medico" required>
                        <option value="" disabled selected>Selecione um médico</option>
                        <?php
                        if ($medicos) {
                            foreach ($medicos as $medico) {
                                echo "<option value='{$medico['id_usuario']}'>{$medico['nome']}</option>";
                            }
                        }
                        ?>
                    </select>
                    <br>
                    <input type="submit" value="Marcar" name="enviar" id="enviar">
                    <?php
                    $mensagem = GerenciadorSessao::getMensagem();
                    if ($mensagem) {
                        echo "<p>$mensagem</p>";
                    }
                    ?>

                </form>
            </center>
        </section>
    </main>

</body>

</html