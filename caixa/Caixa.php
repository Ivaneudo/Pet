<?php
session_start();

// Verifica se o usuário é um administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'caixa') {
    header("Location: ../entrada/Entrar.php"); // Redireciona se não for caixa
    exit();
}

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/caixa.css">
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../entrada/logout.php"><p>sair</p></a> <!-- logout -->
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="CaixaCadastrar.php">Cadastrar</a></li>
                    <li><a href="CaixaProdutos.php">Produtos</a></li>
                    <li><a href="CaixaServiços.php">Serviço</a></li>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>