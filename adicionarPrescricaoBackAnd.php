<?php
require_once '../../configdb.php';
require_once 'GerenciadorDeSessoes.php';
require_once 'verificaAutenticacao.php';
Autenticacao::AutenticacaoMedico();

class Prescricao
{
    private $conn;
    private $idConsulta;
    private $idMedicamento;
    private $dosagem;
    private $validadePrescricao;

    public function __construct($conn, $idConsulta, $idMedicamento, $dosagem, $validadePrescricao)
    {
        $this->conn = $conn;
        $this->idConsulta = (int)$idConsulta;
        $this->idMedicamento = (int)$idMedicamento; 
        $this->dosagem = trim($dosagem);
        $this->validadePrescricao = trim($validadePrescricao);
    }

    public function formularioInvalido()
    {
        return empty($_POST['id_consulta']) || empty($_POST['id_medicamento']) || empty($_POST['dosagem']) || empty($_POST['validade_prescricao']);

    }
    public function adicionarPrescricao()
    {
        try {if (empty($this->idConsulta)) {
            throw new Exception("ID da consulta não pode estar vazio.");
        }
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

            $query = "INSERT INTO prescricoes (id_consulta, id_medicamento, dosagem, validade_prescricao) 
                        VALUES (:id_consulta, :id_medicamento, :dosagem, :validade_prescricao)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':id_consulta' => $this->idConsulta,
                ':id_medicamento' => $this->idMedicamento,
                ':dosagem' => $this->dosagem,
                ':validade_prescricao' => $this->validadePrescricao,
            ]);

            GerenciadorSessao::setMensagem("<p style = 'color:green'>Prescrição adicionada com sucesso.</p>");
            GerenciadorSessao::redirecionar("AtendimentoEmEspera.php");

        } catch (Exception $e) {
            GerenciadorSessao::setMensagem("<p style = 'color:red'>Erro ao adicionar prescrição: " . $e->getMessage()."</p>");
            GerenciadorSessao::redirecionar("adicionarPrescricao.php");
        } catch (PDOException $e) {
            GerenciadorSessao::setMensagem("<p style = 'color:red'>Erro em PDO ao adicionar prescrição: " . $e->getMessage()."</p>");
            GerenciadorSessao::redirecionar("adicionarPrescricao.php");
        }
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $idConsulta = $_POST['idConsulta'] ?? '';
        $idMedicamento = $_POST['id_medicamento'] ?? '';
        $dosagem = $_POST['dosagem'] ?? '';
        $validadePrescricao = $_POST['validade_prescricao'] ?? '';

        $prescricao = new Prescricao($conn, $_POST['idConsulta'], $_POST['id_medicamento'], $_POST['dosagem'], $_POST['validade_prescricao']);
        if ($prescricao->formularioInvalido() == false) {
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