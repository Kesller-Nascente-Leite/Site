<?php
require_once '../../configdb.php';
require_once 'Session_start.php';
require_once 'GerenciadorDeSessoes.php';
require_once 'verificaAutenticacao.php';
Autenticacao::AutenticacaoPaciente();

class MostrandoPrescricao
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function mostrarPrescricao()
    {
        try {
            $query = "SELECT usuarios.nome,medicamento.medicamento,prescricoes.dosagem,prescricoes.validade_prescricao  FROM prescricoes
                INNER JOIN medicamento ON medicamento.id = prescricoes.id_medicamento
                INNER JOIN usuarios ON usuarios.id = prescricoes.id_paciente
                WHERE prescricoes.id_paciente = :id_paciente";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id_paciente' => $_SESSION['id']]);
            if ($stmt->rowCount() > 0) {
                return $this->montandoTabelaPrescricao($stmt);
            } else {
                echo htmlspecialchars('Nenhuma prescrição adicionada');
            }
        } catch (Exception $e) {
            throw new ErrorException('Erro Exception: ' . $e->getMessage());
        } catch (PDOException $e) {
            throw new ErrorException('Erro PDOException: ' . $e->getMessage());
        }
    }
    private function montandoTabelaPrescricao($stmt)
    {
        ob_start();
        ?>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Medicamento</th>
                    <th>Dosagem</th>
                    <th>Validade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($linha['nome']); ?></td>
                        <td><?= htmlspecialchars($linha['medicamento']); ?></td>
                        <td><?= htmlspecialchars($linha['dosagem']); ?></td>
                        <td><?= htmlspecialchars($linha['validade_prescricao']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php
        return ob_get_clean();
    }
}
