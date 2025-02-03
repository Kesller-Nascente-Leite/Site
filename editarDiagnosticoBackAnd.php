<?php
require_once '../../configdb.php';
require_once 'verificaAutenticacao.php';
require_once 'GerenciadorDeSessoes.php';
require 'verifica_sessao.php';

class DiagnosticoPaciente
{
    private $conn;
    private $id_paciente;
    private $id_consulta;
    private $linha;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function verificarIds()
    {
        if (
            !isset($_GET['id_paciente'], $_GET['id_consulta']) ||
            !is_numeric($_GET['id_paciente']) ||
            !is_numeric($_GET['id_consulta'])
        ) {
            GerenciadorSessao::setMensagem("ID do paciente ou consulta inválido.");
            GerenciadorSessao::redirecionar('atendimentoEmEspera.php');
            exit();
        }

        $this->id_paciente = (int) $_GET['id_paciente'];
        $this->id_consulta = (int) $_GET['id_consulta'];
    }

    public function consultarDiagnostico()
    {
        $query = "SELECT p.nome, c.data_horario, c.observacoes_diagnostico
                  FROM consulta c
                  INNER JOIN usuarios p ON c.id_paciente = p.id
                  WHERE c.id_paciente = :id_paciente AND c.id = :id_consulta";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_paciente', $this->id_paciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_consulta', $this->id_consulta, PDO::PARAM_INT);
        $stmt->execute();

        $this->linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$this->linha) {
            GerenciadorSessao::setMensagem("Paciente ou consulta não encontrada.");
            GerenciadorSessao::redirecionar('atendimentoEmEspera.php');
            exit();
        }
    }

    public function atualizarDiagnostico()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['diagnostico'])) {
            $novoDiagnostico = $_POST['diagnostico'];

            $updateQuery = "UPDATE consulta 
                            SET observacoes_diagnostico = :diagnostico 
                            WHERE id_paciente = :id_paciente 
                                AND id = :id_consulta 
                                AND id_status = 1";

            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->execute([
                ':diagnostico' => $novoDiagnostico,
                ':id_paciente' => $this->id_paciente,
                ':id_consulta' => $this->id_consulta,
            ]);

            GerenciadorSessao::setMensagem('Diagnóstico atualizado com sucesso.');
            GerenciadorSessao::redirecionar('atendimentoEmEspera.php');
            exit();
        }
    }

    public function getDiagnostico()
    {
        return $this->linha['observacoes_diagnostico'] ?? '';
    }
}