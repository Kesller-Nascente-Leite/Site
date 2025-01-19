<?php
session_start();
require_once "../../configdb.php";
require_once "csrfPROTECAO.php";
require "GerenciadorDeSessoes.php";
require_once "Session_start.php";

class Cadastro
{
    private $conn;
    private $nome;
    private $email;
    private $senha;
    private $sexo;
    private $nascimento;
    private $telefone;

    private $tipoUsuario;

    public function __construct($conn, $nome, $email, $sexo, $senha, $nascimento, $telefone, $tipoUsuario = 'Paciente') {

        $this->conn = $conn;
        $this->nome = $nome;
        $this->email = trim($email);
        $this->senha = trim($senha);
        $this->sexo = $sexo;
        $this->nascimento = $nascimento;
        $this->telefone = trim($telefone);
        $this->tipoUsuario = trim($tipoUsuario);
    }

    public function checandoFormulario()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            GerenciadorSessao::setMensagem("Formato de email inválido.");
            GerenciadorSessao::redirecionar("cadastro.php");
            exit();
        }
        if (
            empty($this->nome) || empty($this->email) || empty($this->sexo) ||
            empty($this->senha) || empty($this->nascimento) || empty($this->telefone)
        ) {
            GerenciadorSessao::setMensagem("Você precisa Preencher o Cadastro");
            GerenciadorSessao::redirecionar("cadastro.php");
            exit();
        }

        $dataRecebida = date_create_from_format('Y-m-d', $this->nascimento);
        $dataAtual = new DateTime();
        $limiteMinimo = new DateTime('-100 years');

        if (!$dataRecebida || $dataRecebida < $limiteMinimo || $dataRecebida > $dataAtual) {
            GerenciadorSessao::setMensagem("Data de nascimento inválida. Selecione uma data válida.");
            GerenciadorSessao::redirecionar("cadastro.php");
            exit();
        }
        if (!preg_match('/^\d{9,15}$/', $this->telefone)) {
            GerenciadorSessao::setMensagem("Número de telefone inválido.");
            GerenciadorSessao::redirecionar("cadastro.php");
            exit();
        }

    }

    public function checandoEmail()
    {

        try {

            $query = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':email' => $this->email]);
            if ($stmt->rowCount() > 0) {
                GerenciadorSessao::setMensagem("Email ja cadastrado.");
                GerenciadorSessao::redirecionar("cadastro.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Erro ao acessar o banco de dados.".error_log($e->getMessage());;
        }
    }


    public function cadastrando()
    {
        try {
            $usuario = '';
            $cripto = password_hash($this->senha, PASSWORD_DEFAULT);

            $query = "INSERT INTO usuarios (nome,email,senha,data_nascimento,telefone,id_sexo,tipo_usuario) VALUES (:nome,:email,:senha,:data_nascimento,:telefone,:sexo,:tipo_usuario)";
            $enviando = $this->conn->prepare($query);

            // Mudando de ultima hora e colocando por um array
            if (
                $enviando->execute([
                    ':nome' => $this->nome,
                    ':email' => $this->email,
                    ':senha' => $cripto,
                    ':data_nascimento' => $this->nascimento,
                    ':telefone' => $this->telefone,
                    ':sexo' => $this->sexo,
                    ':tipo_usuario' => $this->tipoUsuario
                ])
            ) {
                //pega o ultimo id inserido
                $ultimoID = $this->conn->lastInsertId();
                $query = "SELECT * FROM usuarios WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $ultimoID);
                $stmt->execute();
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION['id'] = $usuario['id'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['telefone'] = $usuario['telefone'];
                $_SESSION['data_nascimento'] = $usuario['data_nascimento'];
                $_SESSION['sexo'] = $usuario['id_sexo'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

                $query = "INSERT INTO paciente(id_usuario) VALUES (:id_usuario)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id_usuario', $ultimoID);
                $stmt->execute();

                GerenciadorSessao::redirecionar("site.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Erro ao acessar o banco de dados.".$e->getMessage();
        }
    }
}
$cadastro = new Cadastro($conn, $_POST['nome'], $_POST['email'], $_POST['sexo'], $_POST['senha'], $_POST['nascimento'], $_POST['telefone']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {

        //consultando o Token do csrf
        $tokenRecebido = $_POST['csrf_token'] ?? '';
        Csrf::verificarToken($tokenRecebido);
        Csrf::limparToken();
        $cadastro->checandoEmail();
        $cadastro->cadastrando();

    } catch (Exception $e) {
        error_log($e->getMessage());
        GerenciadorSessao::setMensagem("Ocorreu um erro. Por favor, tente novamente.");
        GerenciadorSessao::redirecionar("cadastro.php");
        exit();
    }
}

