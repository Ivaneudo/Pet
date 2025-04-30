<?php
session_start();
session_destroy(); // Destrói todas as informações da sessão
header("Location: ../entrada/BoasVindas.php"); // Redireciona para a página BoasVindas
exit();
?>