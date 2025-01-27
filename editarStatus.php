<?php
require_once '../../configdb.php';
require_once 'GerenciadorDeSessoes.php';
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoMedico();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    foreach ($_POST['status'] as $consultaId => $novoStatus) {
        if ($novoStatus == 'Cancelado' || $novoStatus == 'Pendendte') {

            $query = "UPDATE consulta SET status = :status 
        AND observacoes_diagnostico = :observacoes_diagnostico WHERE id = :consulta_id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':status', $novoStatus);
            $stmt->bindValue(':observacoes_diagnostico',$novoStatus);
            $stmt->bindValue(':consulta_id', $consultaId, PDO::PARAM_INT);
            $stmt->execute();

        }
        $query = "UPDATE consulta SET status = :status 
        WHERE id = :consulta_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':status', $novoStatus);
        $stmt->bindValue(':consulta_id', $consultaId, PDO::PARAM_INT);
        $stmt->execute();

    }

    GerenciadorSessao::setMensagem("Status atualizado com sucesso!");
    header("Location: AtendimentoEmEspera.php");
    exit();
} else {
    echo "Requisição inválida.";
    exit();
}
?>