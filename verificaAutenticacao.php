<?php
require_once 'Session_start.php';

class Autenticacao{

    public static function AutenticacaoMedico()
    {
        if (!isset($_SESSION['id']) || strtolower($_SESSION['tipo_usuario']) != 'medico') {
            GerenciadorSessao::setMensagem("Login necessário");
            GerenciadorSessao::redirecionar("index.php");
            exit();
        }
    }
    public static function AutenticacaoPaciente()
    {
        if (!isset($_SESSION['id']) || strtolower($_SESSION['tipo_usuario']) != 'paciente') {
            GerenciadorSessao::setMensagem("Login necessário");
            GerenciadorSessao::redirecionar("index.php");
            exit();
        }
    }
    public static function AutenticacaoAdmin()
    {
        if (!isset($_SESSION['id']) || strtolower($_SESSION['tipo_usuario']) != 'admin') {
            GerenciadorSessao::setMensagem("Login necessário");
            GerenciadorSessao::redirecionar("index.php");
            exit();
        }
    }
    

}
?>