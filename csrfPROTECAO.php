<?php
require_once "Session_start.php";

class Csrf
{
    const EXPIRACAO = 3600; 

    // Método para gerar o token CSRF
    public static function gerarToken()
    {
        if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time']) || (time() - $_SESSION['csrf_token_time']) > self::EXPIRACAO) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
            $_SESSION['csrf_token_time'] = time(); 
        }
        return $_SESSION['csrf_token'];
    }

    public static function verificarToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            die("Ação não autorizada. Token CSRF inválido.");
        }

        if (time() - $_SESSION['csrf_token_time'] > self::EXPIRACAO) {
            die("Ação não autorizada. O token expirou.");
        }
    }


    public static function limparToken()
    {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
    }
}
?>