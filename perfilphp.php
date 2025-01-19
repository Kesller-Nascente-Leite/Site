<?php
require "verifica_sessao.php";
require_once "../../configdb.php";
require_once "csrfPROTECAO.php";
require_once "GerenciadorDeSessoes.php";

class Perfilphp
{
    private $conn;
    private $senha;
    public function __construct($conn, $senha)
    {
        $this->conn = $conn;
        $this->senha = $senha;
    }

    public function Deletando()
    {
        try {

            $query = "SELECT senha,id FROM paciente WHERE email = :email";
            $stmt = $this->conn->prepare($query);

            $stmt->execute([":email" => $_SESSION['email']]);

            if ($stmt->rowCount() > 0) {

                $nome = $stmt->fetch(PDO::FETCH_ASSOC);
                $id = $nome['id'];
                $senhaDoPaciente = $nome["senha"];

                if (password_verify($this->senha, $senhaDoPaciente)) {
                    $query = "DELETE FROM paciente WHERE id = :id";
                    $deletando = $this->conn->prepare($query);

                    $deletando->execute([":id" => $id]);

                    if ($deletando->rowCount() > 0) {
                        session_unset();
                        session_destroy();
                        GerenciadorSessao::setMensagem("Conta excluida");
                        GerenciadorSessao::redirecionar("index.php");
                        exit();
                    }
                } else {
                    GerenciadorSessao::setMensagem("Senha invalida");
                    GerenciadorSessao::redirecionar("perfil.php");
                    exit();

                }
            }
        } catch (PDOException $e) {
            echo "Erro: ", $e->getMessage();
        }
    }

}


$perfil = new Perfilphp($conn, $_POST["senha"]);

if ($_SERVER["REQUEST_METHOD"] === "POST" and isset($_POST['delete'])) {
    $tokenRecebido = $_POST['csrf_token'] ?? '';
    Csrf::verificarToken($tokenRecebido);
    Csrf::limparToken();
    $perfil->Deletando();
}



