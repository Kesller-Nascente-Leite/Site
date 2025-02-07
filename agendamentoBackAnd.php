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
        $this->id = (int) $id;
    }
    private function ordemDeAgendamentos()
    {
        try {
            if (!isset($_GET['botaoAgendamento'])) {

                $query = "SELECT 
                    paciente.nome AS paciente_nome, 
                    medico.nome AS medico_nome, 
                    especializacao.especializacao, 
                    consulta.data_horario, 
                    consulta.observacoes_diagnostico, 
                    status_consulta.descricao
                FROM consulta
                INNER JOIN usuarios AS paciente ON consulta.id_paciente = paciente.id
                INNER JOIN usuarios AS medico ON consulta.id_medico = medico.id
                INNER JOIN especializacao ON consulta.id_tipo_consulta = especializacao.id 
                INNER JOIN status_consulta ON consulta.id_status = status_consulta.id
                WHERE paciente.id = :id";

                $stmt = $this->conn->prepare($query);
                $stmt->execute([":id" => $this->id]);
                return $stmt;
            }

            $opcoes = [
                '1' => "ORDER BY data_horario ASC",
                '2' => "AND status_consulta.descricao = 'Concluído' ORDER BY data_horario ASC",
                '3' => "AND status_consulta.descricao = 'Pendente' ORDER BY data_horario ASC",
                '4' => "AND status_consulta.descricao = 'Cancelado' ORDER BY data_horario ASC",
                '5' => "AND status_consulta.descricao = 'Em espera' ORDER BY data_horario ASC"
            ];
            $filtro = $opcoes[$_GET['botaoAgendamento']] ?? '';

            $query = "SELECT 
                    paciente.nome AS paciente_nome, 
                    medico.nome AS medico_nome, 
                    especializacao.especializacao, 
                    consulta.data_horario, 
                    consulta.observacoes_diagnostico, 
                    status_consulta.descricao
                FROM consulta
                INNER JOIN usuarios AS paciente ON consulta.id_paciente = paciente.id
                INNER JOIN usuarios AS medico ON consulta.id_medico = medico.id
                INNER JOIN especializacao ON consulta.id_tipo_consulta = especializacao.id 
                INNER JOIN status_consulta on consulta.id_status = status_consulta.id
                WHERE paciente.id = :id $filtro";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $this->id]);
            return $stmt;

        } catch (PDOException $e) {
            throw new Exception("Erro em PDO: " . $e->getMessage());
        }
    }

    public function mostrandoAgendamento()
    {
        try {
            $ordemAgendamento = $this->ordemDeAgendamentos();

            if ($ordemAgendamento === null) {
                echo "<p>Por favor, selecione uma ordem de agendamento.</p>";
                return;
            }

            if ($ordemAgendamento->rowCount() === 0) {
                echo "<p>Nenhum agendamento encontrado.</p>";
                return;
            }

            echo "<table border='1'>\n";
            echo "<tr><th>Paciente</th><th>Médico</th><th>Especialização</th><th>Data da Consulta</th><th>Observações/Diagnóstico</th><th>Status</th></tr>\n";

            while ($linha = $ordemAgendamento->fetch(PDO::FETCH_ASSOC)) {
                echo "\t<tr>\n";
                echo "\t\t<td>" . htmlspecialchars($linha['paciente_nome']) . "</td>\n";
                echo "\t\t<td>" . htmlspecialchars($linha['medico_nome']) . "</td>\n";
                echo "\t\t<td>" . htmlspecialchars($linha['especializacao']) . "</td>\n";
                echo "\t\t<td>" . htmlspecialchars($linha['data_horario']) . "</td>\n";
                echo "\t\t<td>" . htmlspecialchars($linha['observacoes_diagnostico']) . "</td>\n";
                echo "\t\t<td>" . htmlspecialchars($linha['descricao']) . "</td>\n";
                echo "\t</tr>\n";
            }
            echo "</table>\n";

        } catch (PDOException $e) {
            throw new Exception("Erro em PDO: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Erro no codigo: " . $e->getMessage());
        }
    }
}

