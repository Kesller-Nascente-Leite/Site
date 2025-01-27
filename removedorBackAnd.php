<?php
require_once 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require_once '../../configdb.php';
require_once 'verificaAutenticacao.php';

Autenticacao::AutenticacaoAdmin();
class Removedor
{

    private $conn;
    private $id_usuario_deletar;
    private $senha;

    public function __construct($conn, $id_usuario_deletar, $senha)
    {
        $this->conn = $conn;
        $this->id_usuario_deletar = $id_usuario_deletar;
        $this->senha = trim($senha);
    }
    public function verificandoIdMaximo()
    {
        try {
            $query = "SELECT MAX(id) FROM usuarios;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $maiorID = $stmt->fetchColumn();

            if ($this->id_usuario_deletar > $maiorID) {
                GerenciadorSessao::setMensagem('<p style = color:red;>ID fornecido é maior do que o maior ID existente.');
                GerenciadorSessao::redirecionar('removedor.php');
                return;
            }
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro: " . $e->getMessage());
        } catch (PDOException $e) {
            throw new PDOException("Erro: " . $e->getMessage());
        }
    }

    public function removendo()
    {
        try {
            if (!filter_var($this->id_usuario_deletar, FILTER_VALIDATE_INT)) {
                throw new Exception("ID inválido.");
            }
            $query = "SELECT COUNT(id) FROM usuarios WHERE id = :id;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $this->id_usuario_deletar]);
            $resultado = $stmt->fetchColumn();

            if ($resultado > 0) {
                $query = "SELECT senha FROM usuarios WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([':id' => $_SESSION['id']]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($admin && password_verify($this->senha, $admin['senha'])) {
                    $query = "DELETE FROM usuarios WHERE id = :id;";
                    $stmt = $this->conn->prepare($query);
                    $stmt->execute([':id' => $this->id_usuario_deletar]);
                    GerenciadorSessao::setMensagem('<p style = color:green;>Usuario deletado com Sucesso.</p>');
                    GerenciadorSessao::redirecionar('removedor.php');
                    return;
                } else {
                    GerenciadorSessao::setMensagem('<p style = color:red;>Senha Errada.</p>');
                    GerenciadorSessao::redirecionar('removedor.php');
                    return;
                }

            } else {
                GerenciadorSessao::setMensagem('<p style = color:red;>Não é possivel remover Usuario com id Inexistente</p>');
                GerenciadorSessao::redirecionar('removedor.php');
                return;
            }
        } catch (Exception $e) {
            throw new Exception("Erro: " . $e->getMessage());
        } catch (PDOException $e) {
            throw new PDOException("Erro: " . $e->getMessage());
        }
    }
}


class RemovendoFormulario extends Removedor
{
    public function verificandoFormulario()
    {
        try {
            if (empty($_POST['id']) || empty($_POST['senha'])) {
                GerenciadorSessao::setMensagem('Nenhum crendencia pode estar vazia!');
                GerenciadorSessao::redirecionar('removedor.php');
                return;
            } else {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro: " . $e->getMessage());
        } catch (PDOException $e) {
            throw new PDOException("Erro: " . $e->getMessage());
        }
    }
}
$removendo = new Removedor($conn, $_POST['id'], $_POST['senha']);

$formulario = new RemovendoFormulario($conn, $_POST['id'], $_POST['senha']);

try {


    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (
            $formulario->verificandoFormulario() == true &&

            $removendo->verificandoIdMaximo() == true
        ) {
            $tokenRecebido = $_POST['csrf_token'] ?? '';
            Csrf::verificarToken($tokenRecebido);
            Csrf::limparToken();
            $removendo->removendo();

        }
    }
} catch (Exception $e) {
    throw new Exception("Erro: " . $e->getMessage());
} catch (PDOException $e) {
    throw new PDOException("Erro: " . $e->getMessage());
}
?>