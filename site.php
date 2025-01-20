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


// Função para buscar médicos pela especialização

function getMedicosEspecializacao($especializacaoId)
{
    global $conn;
    $query = "SELECT m.id_usuario, u.nome
              FROM medico m
              JOIN usuarios u ON m.id_usuario = u.id
              WHERE m.id_especializacao = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$especializacaoId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// AJAX: Retorna médicos em JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consulta_id'])) {
    $especializacaoId = intval($_POST['consulta_id']);
    $medicos = getMedicosEspecializacao($especializacaoId);

    if (empty($medicos)) {
        echo json_encode(['erro' => 'Sem médicos disponíveis para essa especialização.']);
    } else {
        echo json_encode($medicos);
    }
    exit();
}

$especializacaoId = null;
$medicos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consulta'])) {
    $especializacaoId = intval($_POST['consulta']);
    $medicos = getMedicosEspecializacao($especializacaoId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site</title>
    <link rel="stylesheet" href="siteCSS.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

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
        } $(document).ready(function () {
            $('#consulta').change(function () {
                var consultaId = $(this).val();

                // Limpa o select de médicos
                $('#medico').html('<option value="" disabled selected>Selecione um médico</option>');

                if (consultaId) {
                    $.post('site.php', { consulta_id: consultaId }, function (data) {
                        console.log(data); 
                        if (data.erro) {
                            $('#medico').html('<option value="" disabled>' + data.erro + '</option>');
                        } else {
                            var options = '';
                            $.each(data, function (index, medico) {
                                options += '<option value="' + medico.id_usuario + '">' + medico.nome + '</option>';
                            });
                            $('#medico').html(options);
                        }
                    }, 'json').fail(function () {
                        alert('Erro ao carregar médicos.');
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

                <form method="POST" action="<?php echo htmlspecialchars('sitephp.php'); ?>" autocomplete="off">
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
                        <?php if ($medicos): ?>
                            <?php foreach ($medicos as $medico): ?>
                                <option value="<?= $medico['id_usuario'] ?>"><?= htmlspecialchars($medico['nome']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Sem médicos disponíveis para essa especialização</option>
                        <?php endif; ?>
                    </select><br>

                    <?php
                    $mensagem = GerenciadorSessao::getMensagem();
                    if ($mensagem) {
                        echo "<p>$mensagem</p>";
                    }
                    ?>

                    <input type="submit" value="Marcar" name="enviar" id="enviar">
                </form>
            </center>
        </section>
    </main>

</body>

</html>