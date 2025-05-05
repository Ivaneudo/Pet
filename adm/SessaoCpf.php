<?php
session_start();
if (isset($_POST['cpf'])) {
    $_SESSION['cpf_cliente'] = $_POST['cpf'];
}