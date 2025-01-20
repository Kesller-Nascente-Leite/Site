<?php
require 'csrfPROTECAO.php';
require 'verifica_sessao.php';
require "GerenciadorDeSessoes.php";
require "../../configdb.php";

class VerificaFormulario
{


    private $conn;
    private $nome;
    private $email;
    private $senha;
    private $sexo;
    private $nascimento;
    private $telefone;
    private $tipoUsuario;
    private $especializacao;

    public function __construct($conn, $nome, $email, $sexo, $senha, $nascimento, $telefone, $tipoUsuario = 'Medico', $especializacao)
    {

        $this->conn = $conn;
        $this->nome = $nome;
        $this->email = trim($email);
        $this->senha = trim($senha);
        $this->sexo = $sexo;
        $this->nascimento = $nascimento;
        $this->telefone = trim($telefone);
        $this->tipoUsuario = trim($tipoUsuario);
        $this->especializacao = trim($especializacao);
    }

    public function checandoFormulario()
    {
        try {
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                GerenciadorSessao::setMensagem("Formato de email inválido.");
                GerenciadorSessao::redirecionar("adicionarMedico.php");
                exit();
            }
            if (
                empty($this->nome) || empty($this->email) || empty($this->sexo) ||
                empty($this->senha) || empty($this->nascimento) || empty($this->telefone || empty($this->especializacao))
            ) {
                GerenciadorSessao::setMensagem("Você precisa Preencher o adicionarMedico.phpnovo Medico");
                GerenciadorSessao::redirecionar("adicionarMedico.php");
                exit();
            }

            $dataRecebida = date_create_from_format('Y-m-d', $this->nascimento);
            $dataAtual = new DateTime();
            $limiteMinimo = new DateTime('-100 years');

            if (!$dataRecebida || $dataRecebida < $limiteMinimo || $dataRecebida > $dataAtual) {
                GerenciadorSessao::setMensagem("Data de nascimento inválida. Selecione uma data válida.");
                GerenciadorSessao::redirecionar("adicionarMedico.php");
                exit();
            }
            if (!preg_match('/^\d{9,15}$/', $this->telefone)) {
                GerenciadorSessao::setMensagem("Número de telefone inválido.");
                GerenciadorSessao::redirecionar("adicionarMedico.php");
                exit();
            }
        } catch (Exception $e) {
            throw new ErrorException("Algo errado com o banco de dados: " . $e);
        }


    }
    public function checandoEmail()
    {

        try {

            $query = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':email' => $this->email]);
            if ($stmt->rowCount() > 0) {
                GerenciadorSessao::setMensagem("<p style='color:red'>Email ja cadastrado.</p>");
                GerenciadorSessao::redirecionar("adicionarMedico.php");
                exit();
            }
        } catch (PDOException $e) {
            throw new ErrorException("<p style='color:red'>Algo errado com o banco de dados: " . $e . " </p>");
            ;
        }
    }
}

class AdicionandoMedico
{
    private $conn;
    private $nome;
    private $email;
    private $senha;
    private $sexo;
    private $nascimento;
    private $telefone;
    private $tipoUsuario;
    private $especializacao;

    public function __construct($conn, $nome, $email, $sexo, $senha, $nascimento, $telefone, $tipoUsuario = 'Medico', $especializacao)
    {

        $this->conn = $conn;
        $this->nome = $nome;
        $this->email = trim($email);
        $this->senha = trim($senha);
        $this->sexo = $sexo;
        $this->nascimento = $nascimento;
        $this->telefone = trim($telefone);
        $this->tipoUsuario = trim($tipoUsuario);
        $this->especializacao = $especializacao;
    }

    public function addMedico()
    {
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

            $ultimoID = $this->conn->lastInsertId();

            $query = "INSERT INTO medico(id_usuario,id_especializacao) VALUES (:id_usuario,:id_especializacao)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_usuario', $ultimoID);
            $stmt->bindParam(':id_especializacao', $this->especializacao);
            $stmt->execute();

            GerenciadorSessao::setMensagem("<p style='color:green'>Medico cadastrado com sucesso </p>");
            GerenciadorSessao::redirecionar("adicionarMedico.php");
            exit();
        }
    }
}


$verificacao = new VerificaFormulario($conn, $_POST['nome'], $_POST['email'], $_POST['sexo'], $_POST['senha'], $_POST['nascimento'], $_POST['telefone'], 'Medico', $_POST['especializacao']);


$addMedico = new AdicionandoMedico($conn, $_POST['nome'], $_POST['email'], $_POST['sexo'], $_POST['senha'], $_POST['nascimento'], $_POST['telefone'], 'Medico', $_POST['especializacao']);


if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $tokenRecebido = $_POST['csrf_token'] ?? '';
        Csrf::verificarToken($tokenRecebido);
        Csrf::limparToken();

        $verificacao->checandoFormulario();
        $verificacao->checandoEmail();

        $addMedico->addMedico();

    } catch (Exception $e) {
        throw new ErrorException("<p style='color:red'>Algo errado com o banco de dados: " . $e . " </p>");
    } catch (PDOException $e) {
        throw new ErrorException("<p style='color:red'>Algo errado com o banco de dados: " . $e . " </p>");
    }
}