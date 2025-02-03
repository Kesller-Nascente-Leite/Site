<?php
require_once '../../configdb.php';
require_once 'GerenciadorDeSessoes.php';
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoMedico();
# está deixando default
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
        foreach ($_POST['status'] as $consultaId => $novoStatus) {
            if (isset($consultaId) && $novoStatus == 3 || $novoStatus == 4) {

                $diagnostico = ($novoStatus == 3) ? 'Cancelado' : 'Pendente';

                $query = "UPDATE consulta SET id_status = :status, observacoes_diagnostico = :observacoes_diagnostico 
                            WHERE id = :consulta_id";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':status', (int) $novoStatus, PDO::PARAM_INT);
                $stmt->bindValue(':observacoes_diagnostico', $diagnostico, PDO::PARAM_STR);
                $stmt->bindValue(':consulta_id', (int) $consultaId, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    echo "Atualização realizada com sucesso.";
                } else {
                    echo "Erro ao atualizar consulta.";
                }
            } else {
                $query = "UPDATE consulta SET id_status = :status WHERE id = :consulta_id";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':status', (int) $novoStatus, PDO::PARAM_INT);
                $stmt->bindValue(':consulta_id', (int) $consultaId, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        GerenciadorSessao::setMensagem("Status atualizado com sucesso!");
        GerenciadorSessao::redirecionar("atendimentoEmEspera.php");
    } else {
        throw new Exception("Requisição inválida.");
    }
} catch (Exception | PDOException $e) {
    throw new ErrorException("<br>"."Erro: " . $e->getMessage());
}
?>