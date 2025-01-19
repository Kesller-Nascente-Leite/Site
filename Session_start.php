<?php
//verifica se a sessao esta estartada e não repete ela
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>