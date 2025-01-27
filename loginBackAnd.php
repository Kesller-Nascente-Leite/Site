<?php
require_once "../../configdb.php";
require "csrfPROTECAO.php";
require "GerenciadorDeSessoes.php";

class Usuario
{
    private $conn;
    private $email;
    private $senha;

    public function __construct($conn, $email, $senha)
    {
        $this->conn = $conn;
        $this->email = trim($email);
        $this->senha = trim($senha);

    }
    public function verificandoLogin()
    {
        if (empty($this->email) || empty($this->senha)) {
            GerenciadorSessao::setMensagem("Por favor, preencha todos os campos");
            GerenciadorSessao::redirecionar("index.php");
            exit();

        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            GerenciadorSessao::setMensagem("Informações invalidas");
            GerenciadorSessao::redirecionar("index.php");
            exit();
        }
        if (strlen($this->senha) < 6) {
            GerenciadorSessao::setMensagem("A senha deve ter pelo menos 6 caracteres.");
            GerenciadorSessao::redirecionar("index.php");
            exit();
        }
    }

    public function login()
    {

        try {

            $query = "SELECT * FROM usuarios WHERE email = :email";

            $stmt = $this->conn->prepare($query);
            if ($stmt->execute([':email' => $this->email])) {

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario && password_verify($this->senha, $usuario['senha'])) {

                    $_SESSION['id'] = $usuario['id'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['nome'] = $usuario['nome'];
                    $_SESSION['telefone'] = $usuario['telefone'];
                    $_SESSION['data_nascimento'] = $usuario['data_nascimento'];
                    $_SESSION['sexo'] = $usuario['id_sexo'];
                    $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

                    switch ($usuario['tipo_usuario']) {
                        case 'Paciente':
                            GerenciadorSessao::redirecionar("site.php");
                            break;
                        case 'Medico':
                            //$_SESSION[];
                            GerenciadorSessao::redirecionar("medico.php");
                            break;
                        case 'Admin':
                            GerenciadorSessao::redirecionar("admin.php");
                            break;
                        default:
                            GerenciadorSessao::setMensagem("Tipo de usuário inválido.");
                            GerenciadorSessao::redirecionar("index.php");
                            exit();
                    }
                } else {
                    GerenciadorSessao::setMensagem("Erro ao realizar o login");
                    GerenciadorSessao::redirecionar("index.php");
                    exit();
                }
                
            } else {
                GerenciadorSessao::setMensagem("Erro ao realizar o login");
                GerenciadorSessao::redirecionar("index.php");
                exit();
            }

        } catch (PDOException $e) {
            GerenciadorSessao::setMensagem("Erro de conexão: " . $e->getMessage());
            GerenciadorSessao::redirecionar("index.php");

            exit();
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tokenRecebido = $_POST['csrf_token'] ?? '';

    Csrf::verificarToken($tokenRecebido);
    Csrf::limparToken();

    $login = new Usuario($conn, $_POST["email"], $_POST["senha"]);
    $login->verificandoLogin();
    $login->login();
}
