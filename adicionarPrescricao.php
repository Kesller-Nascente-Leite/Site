<?php
require_once '../../configdb.php';
require_once 'verificaAutenticacao.php';
require_once 'GerenciadorDeSessoes.php';
Autenticacao::AutenticacaoMedico();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Prescrição</title>
    <link rel="stylesheet" href="adicionarPrescricao.css">
    <script>
        function AtendimentoEmEspera() {
            location.href = "atendimentoEmEspera.php";
        }
        function Home() {
            location.href = 'medico.php';
        }
        function AdicionarProntuario() {
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
            <button type="button" onclick="AtendimentoEmEspera()">Atendimento Em Espera</button>
            <button type="button" onclick="Home()">Home</button>
            <button type="button" onclick="AdicionarProntuario()">Adicionar Prontuário</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Adicionar Prescrição</h2>

            <form method="post" action="<?= htmlspecialchars('adicionarPrescricaoBackAnd.php'); ?>" autocomplete="on">
            
                <label for="idConsulta">ID da Consulta:</label>
                <input type="number" id="id_consulta" name="idConsulta" required>

                <label for="id_medicamento">Medicamento:</label>
                <select id="id_medicamento" name="id_medicamento" required>
                    <option value="">Selecione um medicamento</option>
                    <?php

                    try {
                        $query = "SELECT id, medicamento FROM medicamento ORDER BY medicamento ASC";
                        $stmt = $conn->query($query);

                        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . htmlspecialchars($linha['id']) . "'>" . htmlspecialchars($linha['medicamento']) . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo '<option value="">Nenhum medicamento disponível</option>';
                    }
                    ?>
                </select>

                <label for="dosagem">Dosagem:</label>
                <input type="text" id="dosagem" name="dosagem" required>

                <label for="validade_prescricao">Validade da Prescrição:</label>
                <input type="date" id="validade_prescricao" name="validade_prescricao" required>

                <input type="submit" value="Marcar" name="enviar" id="enviar">
                <?php $mensagem = GerenciadorSessao::getMensagem();
                if ($mensagem) {
                    echo $mensagem;
                } ?>
            </form>
        </div>
    </main>
</body>

</html>