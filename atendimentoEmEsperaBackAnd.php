<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once "../../configdb.php";
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoMedico();
class MostrandoUsuarios
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    

    public function usuarios($filtro = "", $offset = 0, $limite = 50)
    {
        $query = "SELECT 
        consulta.id,
        paciente.nome AS paciente_nome, 
        medico.nome AS medico_nome, 
        especializacao.especializacao, 
        consulta.data_horario, 
        consulta.observacoes_diagnostico, 
        consulta.status, 
        consulta.id AS consulta_id,
        consulta.id_paciente 
    FROM consulta
    INNER JOIN usuarios AS paciente ON consulta.id_paciente = paciente.id
    INNER JOIN usuarios AS medico ON consulta.id_medico = medico.id
    INNER JOIN especializacao ON consulta.id_tipo_consulta = especializacao.id
    WHERE consulta.observacoes_diagnostico = 'Em espera' 
       OR consulta.status = 'Em espera'";


        if (!empty($filtro)) {
            $query .= " AND (
                paciente.nome ILIKE :filtro OR 
                medico.nome ILIKE :filtro OR
                consulta.data_horario::text ILIKE :filtro OR 
                consulta.observacoes_diagnostico ILIKE :filtro OR
                especializacao.especializacao ILIKE :filtro
            )";
        }

        $query .= " ORDER BY consulta.id ASC LIMIT :limite OFFSET :offset";


        $stmt = $this->conn->prepare($query);

        if (!empty($filtro)) {
            $stmt->bindValue(':filtro', '%' . $filtro . '%');
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        echo "<form method='POST' action='editarStatus.php'>";
        echo "<table>";
        echo "<thead>
                <tr>
                <th>id Da consulta</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Especialização</th>
                    <th>Data</th>
                    <th>Diagnóstico</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>";
        echo "<tbody>";

        while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($linha['id']) . "</td>";
            echo "<td>
            <a href='editarDiagnostico.php?id_paciente=" . urlencode($linha['id_paciente']) . "'>"
                . htmlspecialchars($linha['paciente_nome']) . "</a>
        </td>";

            echo "<td>" . htmlspecialchars($linha['medico_nome']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['especializacao']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['data_horario']) . "</td>";
            echo "<td>" . htmlspecialchars($linha['observacoes_diagnostico']) . "</td>";
            echo "<td>
            <select name='status[" . $linha['consulta_id'] . "]' class='status-select'>
                <option value='Em espera'" . ($linha['status'] === 'Em espera' ? " selected" : "") . ">Em espera</option>
                <option value='Concluído'" . ($linha['status'] === 'Concluído' ? " selected" : "") . ">Concluído</option>
                <option value='Cancelado'" . ($linha['status'] === 'Cancelado' ? " selected" : "") . ">Cancelado</option>
                <option value='Pendente'" . ($linha['status'] === 'Pendente' ? " selected" : "") . ">Pendente</option>
            </select>
        </td>";
            echo "<td><button type='submit' name='editar' value='" . $linha['consulta_id'] . "'>Salvar</button></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</form>";
    }
}
