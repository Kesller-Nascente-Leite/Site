<?php
require "../../configdb.php";
require "verifica_sessao.php";
require_once "csrfPROTECAO.php";
require_once "GerenciadorDeSessoes.php";
class Consulta
{

    private $conn;
    private $dataHora;
    private $consulta;
    private $medico;


    public function __construct($conn, $dataHora, $consulta, $medico)
    {
        $this->conn = $conn;
        $this->dataHora = $dataHora;
        $this->consulta = $consulta;
        $this->medico = $medico;
    }


    public function MarcandoConsulta()
    {
        try {

            $query = "INSERT INTO consulta(id_paciente,id_tipo_consulta,id_medico,data_horario_consulta) VALUES(:paciente,:consulta,:medico,:data_horario)";
            $stmt = $this->conn->prepare($query);

            if (
                $stmt->execute([
                    ':paciente' => $_SESSION['id'],
                    ':consulta' => $this->consulta,
                    ':medico' => $this->medico,
                    ':data_horario_consulta' => $this->dataHora
                ])
            ) {
                GerenciadorSessao::setMensagem("Agendamento feito com sucesso!");
                GerenciadorSessao::redirecionar("agendamento.php");
                exit();
            }


        } catch (Exception $e) {
            echo "Erro: $e";
        }
    }
    
}


class Formulario extends Consulta
{
    private $conn;
    private $dataHora;

    private $medico;
    public function __construct($conn, $dataHora, $medico)
    {
        $this->conn = $conn;
        $this->dataHora = $dataHora;

        $this->medico = $medico;
    }
    public function formularioVazio()
    {
        if (isset($_POST['enviar']) && (empty($_POST['dataHora']) || empty($_POST['consulta']) || empty($_POST['medico']))) {

            GerenciadorSessao::setMensagem("Preencha o formulario");
            GerenciadorSessao::redirecionar("site.php");
            return TRUE;
        }
    }
    public function verificarDisponibilidadeDoMedico()
    {
        try {
            $query = "SELECT * FROM consulta WHERE id_medico = :id_medico AND data_horario_consulta = :data_horario_consulta";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ":id_medico" => $this->medico,
                ":data_horario_consulta" => $this->dataHora
            ]);
            if ($stmt->rowCount() > 0) {
                GerenciadorSessao::setMensagem("O médico está ocupado neste horário.");
                GerenciadorSessao::redirecionar("site.php");
                return TRUE;

            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar horário: " . $e->getMessage());
        }

    }

}


$consulta = new Consulta($conn, $_POST['dataHora'], $_POST['consulta'], $_POST['medico']);
$formulario = new Formulario($conn, $_POST['dataHora'], $_POST['medico']);

if ($_SERVER["REQUEST_METHOD"] === "POST" and isset($_POST['enviar'])) {
    if ($formulario->formularioVazio() == FALSE or $formulario->verificarDisponibilidadeDoMedico() == FALSE) {
        $tokenRecebido = $_POST['csrf_token'] ?? '';
        Csrf::verificarToken($tokenRecebido);
        Csrf::limparToken();
        $consulta->MarcandoConsulta();
    }
}

