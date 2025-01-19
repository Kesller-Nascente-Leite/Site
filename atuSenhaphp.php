<?php

require 'verifica_sessao.php';
require "../../configdb.php";
require_once "csrfPROTECAO.php";
require_once "GerenciadorDeSessoes.php";
require_once "Session_start.php";

class AtualizarSenha
{
    private $conn;
    private $email;
    private $senha;
    private $nome;

    public function __construct($conn, $email, $senha)
    {
        $this->conn = $conn;
        $this->email = $email;
        $this->senha = $senha;
    }

    public function checandoEmail()
    {
        try {

            $checandoEmailquery = "SELECT id, senha FROM usuarios where email = :email";

            $stmt = $this->conn->prepare($checandoEmailquery);

            $stmt->execute([':email' => $this->email]);

            if ($stmt->rowCount() > 0) {
                $this->nome = $stmt->fetch(PDO::FETCH_ASSOC);
                return true;

            } else {
                GerenciadorSessao::setMensagem("A nova senha deve ser diferente da senha atual.");
                GerenciadorSessao::redirecionar("atuSenha.php");
                $_SESSION['msg'] = "Informações invalidas";
                header("Location: atuSenha.php");
                exit();

            }
        } catch (PDOException $e) {
            GerenciadorSessao::setMensagem('Erro ao buscar o email: ' . $e->getMessage());
            GerenciadorSessao::redirecionar("atuSenha.php");
            exit();
        }
    }

    public function verificandoSenhaAtual()
    {
        $senhaAtual = $this->nome['senha'];
        if (password_verify($this->senha, $senhaAtual)) {

            GerenciadorSessao::setMensagem("A nova senha deve ser diferente da senha atual.");
            GerenciadorSessao::redirecionar("atuSenha.php");
            exit();
        }
    }
    public function Atualizando()
    {
        try {
            $SenhaCripto = password_hash($this->senha, PASSWORD_DEFAULT);
            $id = $this->nome['id'];
            if ($this->nome['id'] != 7) {
                $query = "UPDATE usuarios SET senha = :senha WHERE id = :id ";

                $stmt = $this->conn->prepare($query);
                $stmt->execute([':senha' => $SenhaCripto, ':id' => $id]);

                if ($stmt->rowCount() > 0) {
                    GerenciadorSessao::setMensagem("Senha alterada!");
                    GerenciadorSessao::redirecionar("index.php");
                    exit();
                } else {
                    GerenciadorSessao::setMensagem("Nenhuma alteração foi feita.");
                    GerenciadorSessao::redirecionar("atuSenha.php");
                    exit();
                }
            }else{
                GerenciadorSessao::setMensagem("Erro!");
                GerenciadorSessao::redirecionar("atuSenha.php");
                exit();
            }
        } catch (PDOException $e) {
            GerenciadorSessao::setMensagem("Erro ao atualizar a senha:" . $e->getMessage());
            GerenciadorSessao::redirecionar("atuSenha.php");
            exit();
        } catch (Exception $e) {
            GerenciadorSessao::setMensagem("Erro ao atualizar a senha:" . $e->getMessage());
            GerenciadorSessao::redirecionar("atuSenha.php");
            exit();
        }
    }
}
$atualizandoSenha = new AtualizarSenha($conn, $_POST['email'], $_POST['senha']);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['enviar'])) {

    try {
        $tokenRecebido = $_POST['csrf_token'] ?? '';
        Csrf::verificarToken($tokenRecebido);
        Csrf::limparToken();
        if ($atualizandoSenha->checandoEmail()) {
            $atualizandoSenha->verificandoSenhaAtual();
            $atualizandoSenha->Atualizando();
        }
    } catch (PDOException $e) {
        GerenciadorSessao::setMensagem("Erro:" . $e->getMessage());
        exit;
    }
}