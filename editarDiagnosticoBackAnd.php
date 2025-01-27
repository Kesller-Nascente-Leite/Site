<?php
require_once '../../configdb.php';
require_once 'GerenciadorDeSessoes.php';

// muita gambiarra nesse codigo

class DiagnosticoPaciente
{
    private $conn;
    private $id_paciente;
    private $linha;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function verificarIdPaciente()
    {
        if (!isset($_GET['id_paciente']) || !is_numeric($_GET['id_paciente'])) {
            echo "ID do paciente não fornecido.";
            exit();
        }

        $this->id_paciente = $_GET['id_paciente'];
    }

    public function consultarDiagnostico()
    {
        $query = "SELECT p.nome, c.data_horario, c.observacoes_diagnostico
                    FROM consulta c
                    INNER JOIN usuarios p ON c.id_paciente = p.id
                    WHERE c.id_paciente = :id_paciente";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_paciente', $this->id_paciente, PDO::PARAM_INT);
        $stmt->execute();
        $this->linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$this->linha) {
            echo "Paciente não encontrado.";
            exit();
        }
    }

    public function atualizarDiagnostico()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['diagnostico'])) {
            $novoDiagnostico = $_POST['diagnostico'];

            $query = "SELECT id FROM consulta WHERE id_paciente = :id_paciente AND status = 'Em espera'";;
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_paciente', $this->id_paciente, PDO::PARAM_INT);
            $stmt->execute();
            $id_Consulta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$id_Consulta) {
                echo "Consulta não encontrada para o paciente.";
                exit();
            }

            $updateQuery = "UPDATE consulta 
            SET observacoes_diagnostico = :diagnostico 
            WHERE id_paciente = :id_paciente 
                AND id = :id 
                AND status = 'Em espera'";
            $updateStmt = $this->conn->prepare($updateQuery);


            $updateStmt->execute([
                ':diagnostico' => $novoDiagnostico,
                ':id_paciente'=>$this->id_paciente,
                ':id' => $id_Consulta['id']
            ]);
            
            GerenciadorSessao::setMensagem('Diagnostico Atualizado');
            GerenciadorSessao::redirecionar('AtendimentoEmEspera.php');
            exit();
        }
    }


    public function getDiagnostico()
    {
        return $this->linha['observacoes_diagnostico'];
    }
}
$diagnosticoPaciente = new DiagnosticoPaciente($conn);
$diagnosticoPaciente->verificarIdPaciente();
$diagnosticoPaciente->consultarDiagnostico();
$diagnosticoPaciente->atualizarDiagnostico();
?>