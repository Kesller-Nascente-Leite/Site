<?php
require_once '../../configdb.php';
require_once 'GerenciadorDeSessoes.php';
require_once 'verificaAutenticacao.php';
require_once 'csrfPROTECAO.php';
Autenticacao::AutenticacaoMedico();

class Prescricao
{
    private $conn;
    private $idPaciente;
    private $idConsulta;
    private $idMedicamento;
    private $dosagem;
    private $validadePrescricao;

    public function __construct($conn, $idPaciente, $idConsulta, $idMedicamento, $dosagem, $validadePrescricao)
    {
        $this->conn = $conn;
        $this->idPaciente = (int) $idPaciente;
        $this->idConsulta = (int) $idConsulta;
        $this->idMedicamento = (int) $idMedicamento;
        $this->dosagem = trim($dosagem);
        $this->validadePrescricao = trim($validadePrescricao);
    }

    public function prescricaoInvalido()
    {
        return empty($_POST['idPaciente']) || empty($_POST['idConsulta']) || empty($_POST['id_medicamento']) || empty($_POST['dosagem']) || empty($_POST['validade_prescricao']);

    }
    public function verificandoFormularioVazio()
    {

        $queryVerificaConsulta = "SELECT id FROM consulta WHERE id = :id_consulta";
        $stmtConsulta = $this->conn->prepare($queryVerificaConsulta);
        $stmtConsulta->execute([':id_consulta' => $this->idConsulta]);

        if ($stmtConsulta->rowCount() == 0) {
            throw new Exception("Consulta não encontrada.");
        }

        $queryVerificaMedicamento = "SELECT id FROM medicamento WHERE id = :id_medicamento";
        $stmtMedicamento = $this->conn->prepare($queryVerificaMedicamento);
        $stmtMedicamento->bindParam(':id_medicamento', $this->idMedicamento, PDO::PARAM_INT);
        $stmtMedicamento->execute();

        if ($stmtMedicamento->rowCount() == 0) {
            throw new Exception("Medicamento não encontrado.");
        }
        return false;

    }

    public function adicionarPrescricao()
    {
        try {

            $query = "INSERT INTO prescricoes (id_paciente,id_consulta, id_medicamento, dosagem, validade_prescricao) 
                        VALUES (:id_paciente,:id_consulta, :id_medicamento, :dosagem, :validade_prescricao)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id_paciente' => $this->idPaciente,
                ':id_consulta' => $this->idConsulta,
                ':id_medicamento' => $this->idMedicamento,
                ':dosagem' => $this->dosagem,
                ':validade_prescricao' => $this->validadePrescricao,
            ]);

            GerenciadorSessao::setMensagem("<p style = 'color:green'>Prescrição adicionada com sucesso.</p>");
            GerenciadorSessao::redirecionar("adicionarPrescricao.php");

        } catch (Exception $e) {
            GerenciadorSessao::setMensagem("<p style = 'color:red'>Erro ao adicionar prescrição: " . $e->getMessage() . "</p>");
            GerenciadorSessao::redirecionar("adicionarPrescricao.php");
        } catch (PDOException $e) {
            GerenciadorSessao::setMensagem("<p style = 'color:red'>Erro em PDO ao adicionar prescrição: " . $e->getMessage() . "</p>");
            GerenciadorSessao::redirecionar("adicionarPrescricao.php");
        }
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar'])) {
        $tokenRecebido = $_POST['csrf_token'] ?? '';
        Csrf::verificarToken($tokenRecebido);
        Csrf::limparToken();
        $idPaciente = $_POST['idPaciente'] ?? '';
        $idConsulta = $_POST['idConsulta'] ?? '';
        $idMedicamento = $_POST['id_medicamento'] ?? '';
        $dosagem = $_POST['dosagem'] ?? '';
        $validadePrescricao = $_POST['validade_prescricao'] ?? '';
        $prescricao = new Prescricao($conn, $_POST['idPaciente'], $_POST['idConsulta'], $_POST['id_medicamento'], $_POST['dosagem'], $_POST['validade_prescricao']);

        if (
            $prescricao->verificandoFormularioVazio() == false &&
            $prescricao->prescricaoInvalido() == false
        ) {
            $prescricao->adicionarPrescricao();
        } else {
            GerenciadorSessao::setMensagem("Todos os campos são obrigatórios.");
            GerenciadorSessao::redirecionar("adicionarPrescricao.php");
        }
    }
} catch (Exception $e) {
    throw new ErrorException('Erro: ' . $e->getMessage());
} catch (PDOException $e) {
    throw new ErrorException('Erro: ' . $e->getMessage());
}
?>