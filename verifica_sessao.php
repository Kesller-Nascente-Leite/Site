<?php
require_once "verifica_sessao.php";
//Define o tempo que a sessão irá durar
if (isset($_SESSION['ULTIMA_ATIVIDADE']) && (time() - $_SESSION['ULTIMA_ATIVIDADE'] > 360000000)){
    
    #mudar 1800 para fazer testes 
    unset($_SESSION['ULTIMA_ATIVIDADE']); 
    unset($_SESSION['paciente']); 
    
    header("Location: index.php");
    session_unset();
    session_destroy();
    exit();
}
$_SESSION['ULTIMA_ATIVIDADE'] = time();
$msg = '';
