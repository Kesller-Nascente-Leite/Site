<?php
require "verifica_sessao.php";
require "../../configdb.php";
require_once "Session_start.php";
require_once "GerenciadorDeSessoes.php";
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoPaciente();
class Agendamento
{
    private $conn;
    private $id;
    public function __construct($conn, $id)
    {
        $this->conn = $conn;
        $this->id = (int)$id;
    }
    public function mostrandoAgendamento()
    {
        try {
            $query = "SELECT 
            paciente.nome AS paciente_nome, 
            medico.nome AS medico_nome, 
            especializacao.especializacao, 
            consulta.data_horario, 
            consulta.observacoes_diagnostico, 
            consulta.status
        FROM consulta
        INNER JOIN usuarios AS paciente ON consulta.id_paciente = paciente.id
        INNER JOIN usuarios AS medico ON consulta.id_medico = medico.id
        INNER JOIN especializacao ON consulta.id_tipo_consulta = especializacao.id 
        WHERE paciente.id = :id";


            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $this->id]);

            if ($stmt->rowCount() > 0) {
                echo "<table border='1'>\n";
                echo "<tr><th>Paciente</th><th>Médico</th><th>Especialização</th><th>Data da Consulta</th><th>Observações/Diagnóstico</th><th>Status</th></tr>\n";
                while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "\t<tr>\n";
                    echo "\t\t<td>" . htmlspecialchars($linha['paciente_nome']) . "</td>\n";
                    echo "\t\t<td>" . htmlspecialchars($linha['medico_nome']) . "</td>\n";
                    echo "\t\t<td>" . htmlspecialchars($linha['especializacao']) . "</td>\n";
                    echo "\t\t<td>" . htmlspecialchars($linha['data_horario']) . "</td>\n";
                    echo "\t\t<td>" . htmlspecialchars($linha['observacoes_diagnostico']) . "</td>\n";
                    echo "\t\t<td>" . htmlspecialchars($linha['status']) . "</td>\n";
                    echo "\t</tr>\n";
                }
                echo "</table>\n";
            } else {
                echo "<p>Sem atendimentos no momento.</p>";
            }
        } catch (PDOException $e) {
            throw new ("Erro em PDO: " . $e->getMessage());
        } catch (Exception $e) {
            throw new ("Erro no codigo: " . $e->getMessage());
        }
    }

}
$agendamento = new Agendamento($conn, $_SESSION['id']);
if (!isset($_SESSION['id'])) {
    GerenciadorSessao::setMensagem("login Necessario");
    GerenciadorSessao::redirecionar("index.php");
    exit();
}