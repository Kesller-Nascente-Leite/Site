<?php
require_once "Session_start.php";
class GerenciadorSessao
{
    public static function setMensagem($mensagem)
    {
        $_SESSION['msg'] = $mensagem;
    }


    public static function getMensagem()
    {
        if (isset($_SESSION['msg'])) {
            $mensagem = $_SESSION['msg'];
            unset($_SESSION['msg']);
            return $mensagem;
        }
        return null;
    }

    public static function redirecionar($url)
    {
        header("Location: $url");
        exit();
    }
    public static function limparSessao()
    {
        session_unset();
        session_destroy();
    }
}
