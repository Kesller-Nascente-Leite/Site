<?php
require_once 'csrfPROTECAO.php';
require_once 'GerenciadorDeSessoes.php';
require_once 'verificaAutenticacao.php';
require_once '../../configdb.php';
Autenticacao::AutenticacaoMedico();

class Prontuario
{
    private $conn;
    private $idPaciente;
    private $descricao;

    public function __construct($conn, $idPaciente, $descricao)
    {
        $this->conn = $conn;
        $this->idPaciente = (int)$idPaciente;
        $this->descricao = trim(htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'));
    }
    public function formularioInvalido()
    {
        return empty($this->idConsulta) || empty($this->idMedicamento) || empty($this->dosagem) || empty($this->validadePrescricao);
    }
    public function verificarPaciente()
    {
        try {


            $query = "SELECT tipo_usuario FROM usuarios WHERE id = :id;";
            $stmt = $this->conn->prepare($query);

            $stmt->execute([':id' => $this->idPaciente]);
            $tipo_paciente = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$tipo_paciente || $tipo_paciente['tipo_usuario'] != 'Paciente') {
                GerenciadorSessao::setMensagem('Este usuário não é um paciente, não é possível adicionar prontuário.');
                GerenciadorSessao::redirecionar('adicionarProntuario.php');
                return false;
            }

            return true;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar o paciente: " . $e->getMessage());
        }
    }

    public function adicionarProntuario()
    {
        try {
            if (!$this->verificarPaciente()) {
                return false;
            }

            $query = "INSERT INTO prontuario (id_usuario, observacoes_gerais) VALUES (:paciente_id, :descricao)";
            $stmt = $this->conn->prepare($query);

            return $stmt->execute([
                ':paciente_id' => $this->idPaciente,
                ':descricao' => $this->descricao
            ]);
        } catch (PDOException $e) {
            throw new Exception("Erro ao adicionar o Prontuario: " . $e->getMessage());
        }
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $tokenRecebido = $_POST['csrf_token'] ?? '';
        Csrf::verificarToken($tokenRecebido);
        Csrf::limparToken();

        //fazer a verificacao de baixo em TODOS os arquivos
        // e utulizando o return para deixar mais limpo
        $idPaciente = $_POST['paciente_id'] ?? '';
        $descricao = $_POST['descricao'] ?? '';


        $prontuario = new Prontuario($conn, $_POST['paciente_id'], $_POST['descricao']);

        if ($prontuario->formularioInvalido() == false) {
            if ($prontuario->verificarPaciente() == true) {
                if ($prontuario->adicionarProntuario()) {
                    GerenciadorSessao::setMensagem('Prontuário adicionado com sucesso!');
                    GerenciadorSessao::redirecionar('adicionarProntuario.php');
                    exit();
                } else {
                    GerenciadorSessao::setMensagem('Erro ao adicionar o prontuário.');
                    GerenciadorSessao::redirecionar('adicionarProntuario.php');
                    exit();
                }
            } else {
                GerenciadorSessao::setMensagem('Por favor, preencha todos os campos obrigatórios.');
                GerenciadorSessao::redirecionar('adicionarProntuario.php');
                exit();
            }
        }
    }
} catch (Exception $e) {
    throw new ErrorException("Erro: " . $e->getMessage());
} catch (PDOException $e) {
    throw new Exception("Erro: " . $e->getMessage());
}
?>