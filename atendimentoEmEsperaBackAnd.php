<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once "../../configdb.php";
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoMedico();

class Consulta
{
    private $conn;
    private $especializacao;
    private $listaDeStatus;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->especializacao = $this->getEspecializacaoDoMedico();
        $this->listaDeStatus = $this->getListaDeStatus();
    }

    private function getEspecializacaoDoMedico()
    {
        $query = 'SELECT id_especializacao FROM medico WHERE id_usuario = :id_usuario';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":id_usuario" => $_SESSION['id']
        ]);
        $especializacao = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$especializacao) {
            GerenciadorSessao::limparSessao();
            GerenciadorSessao::setMensagem('Você não é bem-vindo a este local.');
            GerenciadorSessao::redirecionar('index.php');
        }

        return $especializacao;
    }

    private function getListaDeStatus()
    {
        $statusQuery = "SELECT id, descricao FROM status_consulta";
        $statusStmt = $this->conn->prepare($statusQuery);
        $statusStmt->execute();
        return $statusStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarConsultasEmEspera($filtro = "", $offset = 0, $limite = 50)
    {
        $query = "SELECT 
            consulta.id,
            paciente.nome AS paciente_nome, 
            medico.nome AS medico_nome, 
            especializacao.especializacao, 
            consulta.data_horario, 
            consulta.observacoes_diagnostico, 
            status_consulta.descricao AS status_nome, 
            consulta.id AS consulta_id,
            consulta.id_paciente,
            consulta.id_status
        FROM consulta
        INNER JOIN usuarios AS paciente ON consulta.id_paciente = paciente.id
        INNER JOIN usuarios AS medico ON consulta.id_medico = medico.id
        INNER JOIN especializacao ON consulta.id_tipo_consulta = especializacao.id
        INNER JOIN status_consulta ON consulta.id_status = status_consulta.id
        WHERE consulta.id_medico = :id_medico 
          AND (consulta.observacoes_diagnostico = 'Em espera' 
           OR status_consulta.descricao = 'Em espera')";

        if (!empty($filtro)) {
            $query .= " AND (
                paciente.nome ILIKE :filtro OR 
                medico.nome ILIKE :filtro OR
                consulta.data_horario::text ILIKE :filtro OR 
                consulta.observacoes_diagnostico ILIKE :filtro OR
                especializacao.especializacao ILIKE :filtro OR
                status_consulta.descricao ILIKE :filtro
            )";
        }

        $query .= " ORDER BY consulta.id ASC LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id_medico', $_SESSION['id'], PDO::PARAM_INT);
        if (!empty($filtro)) {
            $stmt->bindValue(':filtro', '%' . $filtro . '%');
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $this->renderizarTabelaEmEspera($stmt);
        }else{
            return "<center>".htmlspecialchars('Nada encontrado')."</center>";
        }

    }

    private function renderizarTabelaEmEspera($stmt)
    {
        ob_start(); // Captura a saída do HTML para retornar como string, se eu soubesse disso antes teria ajudado mt
?>
        <form method='POST' action='<?= htmlspecialchars('editarStatus.php') ?>'>
            <table>
                <thead>
                    <tr>
                        <th>Id da consulta</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Especialização</th>
                        <th>Data</th>
                        <th>Diagnóstico</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($linha['id']); ?></td>
                            <td>
                                <a
                                    href='editarDiagnostico.php?id_paciente=<?= urlencode($linha['id_paciente']); ?>&id_consulta=<?= urlencode($linha['id']) ?>'>
                                    <?= htmlspecialchars($linha['paciente_nome']); ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($linha['medico_nome']); ?></td>
                            <td><?= htmlspecialchars($linha['especializacao']); ?></td>
                            <td><?= htmlspecialchars($linha['data_horario']); ?></td>
                            <td><?= htmlspecialchars($linha['observacoes_diagnostico']); ?></td>
                            <td>
                                <select name='status[<?= $linha['consulta_id']; ?>]' class='status-select'>
                                    <?php foreach ($this->listaDeStatus as $status): ?>
                                        <option value='<?= $status['id']; ?>' <?= ($linha['id_status'] == $status['id']) ? ' selected' : ''; ?>>
                                            <?= htmlspecialchars($status['descricao']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button type='submit'>Salvar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </form>
    <?php
        return ob_get_clean(); // Retorna o HTML capturado
    }
    public function listarConsultasConcluidas($filtro = "", $offset = 0, $limite = 50)
    {
        $query = "SELECT 
            consulta.id,
            paciente.nome AS paciente_nome, 
            medico.nome AS medico_nome, 
            especializacao.especializacao, 
            consulta.data_horario, 
            consulta.observacoes_diagnostico, 
            status_consulta.descricao AS status_nome, 
            consulta.id AS consulta_id,
            consulta.id_paciente,
            consulta.id_status
        FROM consulta
        INNER JOIN usuarios AS paciente ON consulta.id_paciente = paciente.id
        INNER JOIN usuarios AS medico ON consulta.id_medico = medico.id
        INNER JOIN especializacao ON consulta.id_tipo_consulta = especializacao.id
        INNER JOIN status_consulta ON consulta.id_status = status_consulta.id
        WHERE consulta.id_medico = :id_medico 
        AND (status_consulta.descricao = 'Concluído' OR status_consulta.descricao = 'Pendente' OR
        status_consulta.descricao = 'Cancelado')";

        if (!empty($filtro)) {
            $query .= " AND (
                paciente.nome ILIKE :filtro OR 
                medico.nome ILIKE :filtro OR
                consulta.data_horario::text ILIKE :filtro OR 
                consulta.observacoes_diagnostico ILIKE :filtro OR
                especializacao.especializacao ILIKE :filtro OR
                status_consulta.descricao ILIKE :filtro
            )";
        }

        $query .= " ORDER BY consulta.id ASC LIMIT :limite OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id_medico', $_SESSION['id'], PDO::PARAM_INT);
        if (!empty($filtro)) {
            $stmt->bindValue(':filtro', '%' . $filtro . '%');
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $this->renderizarTabelaConcluidos($stmt);
        }else{
            return "<center>".htmlspecialchars('Nada encontrado')."</center>";

        }

        
    }
    private function renderizarTabelaConcluidos($stmt)
    {
        ob_start(); 
    ?>
        <form method='POST'>
            <table>
                <thead>
                    <tr>
                        <th>Id da consulta</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Especialização</th>
                        <th>Data</th>
                        <th>Diagnóstico</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($linha['id']); ?></td>
                            <td>
                                    <?= htmlspecialchars($linha['paciente_nome']); ?>
                            </td>
                            <td><?= htmlspecialchars($linha['medico_nome']); ?></td>
                            <td><?= htmlspecialchars($linha['especializacao']); ?></td>
                            <td><?= htmlspecialchars($linha['data_horario']); ?></td>
                            <td><?= htmlspecialchars($linha['observacoes_diagnostico']); ?></td>
                            <td>
                                <?= htmlspecialchars($linha['status_nome']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </form>
<?php
        return ob_get_clean(); 
    }
}
