<?php
require_once 'editarDiagnosticoBackAnd.php';
Autenticacao::AutenticacaoMedico();
// pura gambiara com o $_GET
$diagnosticoPaciente = new DiagnosticoPaciente($conn);
$diagnosticoPaciente->verificarIds();
$diagnosticoPaciente->consultarDiagnostico();
$diagnosticoPaciente->atualizarDiagnostico();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Diagnóstico</title>
    <link rel="stylesheet" href="editarDiagnostico.css">
    <script>
        function AtendimentoEmEspera() {
            location.href = "AtendimentoEmEspera.php";
        }

        function AdicionarPrescricao() {
            location.href = 'AdicionarPrescricao.php';
        }
        function adicionarProntuario() {
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
            <button type="button" name="usuarios" onclick="AtendimentoEmEspera()">Atendimento Em Espera</button>
            <button type="button" onclick="AdicionarPrescricao()">Adicionar Prescricao</button>
            <button type="button" onclick="adicionarProntuario()">AdicionarProntuario</button>
            <button type="button" onclick="perfil()">Perfil</button>
        </nav>
    </header>
    <main>
        <h1>Editar Diagnóstico do Paciente</h1>
        <form method="POST" action="<?=  htmlspecialchars('#'); ?>">
            <label for="diagnostico">Diagnóstico:</label>
            <textarea name="diagnostico" id="diagnostico" rows="4"
                cols="50"></textarea>
            
            <button type="submit">Atualizar Diagnóstico</button>
            
        </form>
    </main>
</body>

</html>