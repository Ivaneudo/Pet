<?php
session_start();
session_destroy(); // Destrói todas as informações da sessão
header("Location: BoasVindas.php"); // Redireciona para a página BoasVindas
exit();
?>