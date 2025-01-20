<?php
require_once "Session_start.php";
require 'verifica_sessao.php';
require_once "../../configdb.php";
require_once "GerenciadorDeSessoes.php";


class Atendimento
{
    private $conn;
    private $id;

    public function __construct($conn, $id, )
    {
        $this->conn = $conn;
        $this->id = $id;

    }


    public function historico()
    {
        try {

            $query = 'SELECT usuarios.nome, prontuario.observacoes_gerais, prontuario.historico_atendimento 
        FROM prontuario 
        INNER JOIN usuarios ON prontuario.id_usuario = usuarios.id
        WHERE prontuario.id_usuario =:id';

            $stmt = $this->conn->prepare($query);

            $stmt->execute([':id' => $this->id]);

            if ($stmt->rowCount() > 0) {

                echo "<table border='1'>\n";

                echo "<tr><th>Paciente</th><th>Resultado</th><th>Data do Exame</th></tr>";

                while ($linha = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "\t<tr>\n";
                    foreach ($linha as $colunas) {
                        echo "\t<td>" . htmlspecialchars($colunas) . "</td>\n";
                    }
                    echo "\t</tr>\n";
                }
                echo "</table>\n";
            } else {
                echo "<p>Sem atendimentos no momento</p>";
            }

        } catch (Exception $e) {
            echo $e;
        } catch (PDOException $e) {
            echo $e;
        }
    }



    public function totalDeAtendimentos()
    {
        try {
            $query = 'SELECT COUNT(*) FROM prontuario WHERE id_usuario = :id';

            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $this->id]);

            return $stmt->fetch(PDO::FETCH_COLUMN);

        } catch (PDOException $e) {
            throw new ("Erro em PDO: $e");
        } catch (Exception $e) {
            throw new ("Erro no codigo: $e");
        }
    }
}

$atendimento = new Atendimento($conn, $_SESSION['id']);

if (isset($_SESSION['id'])) {
    $nome = $_SESSION['nome'];
    $nascimento = date("d-m-Y", strtotime($_SESSION['data_nascimento']));

} else {
    GerenciadorSessao::setMensagem("login Necessario");
    GerenciadorSessao::redirecionar("index.php");
    exit();
}