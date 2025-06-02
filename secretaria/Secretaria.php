<?php
    session_start();

    // Verifica se o usuário é uma secretaria
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
        header("Location: ../entrada/Entrar.php"); // Redireciona se não for secretaria
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
    <title>Secretaria</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/responsivo.css">
    <script src="../js/icons.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li>
                        <a href="SecretariaClientes.php">
                            <div class="icone">
                                <img src="../img/cliente.png" alt="cliente"> <!-- Alterado -->
                                <p>Clientes</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="SecretariaPet.php">
                            <div class="icone">
                                <img src="../img/patinha.png" alt="patinha"> <!-- Alterado -->
                                <p>Pets</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="SecretariaProdutos.php">
                            <div class="icone">
                                <img src="../img/estoque.png" alt="estoque"> <!-- Alterado -->
                                <p>Produtos</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="SecretariaServiços.php">
                            <div class="icone">
                                <img src="../img/caixa.png" alt="caixa">
                                <p>Serviços</p>
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>
