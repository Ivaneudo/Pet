<?php
    session_start();

    // ! Verifica qual o cargo do funcionário logado
    if ($_SESSION['tipo_usuario'] !== 'admin') {
        header("Location: ../entrada/Entrar.php"); // ! Redireciona se não for admin
        exit();
    }

    // ! Guarda o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
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
                        <a href="AdmEstoque.php">
                            <div class="icone">
                                <img src="../img/estoque.png" alt="estoque">
                                <p>Estoque</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="AdmFuncionarios.php">
                            <div class="icone">
                                <img src="../img/funcionarios.png" alt="funcionarios">
                                <p>Funcionários</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="AdmClientes.php">
                            <div class="icone">
                                <img src="../img/cliente.png" alt="cliente">
                                <p>Clientes</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="AdmPet.php">
                            <div class="icone">
                                <img src="../img/patinha.png" alt="patinha">
                                <p>Pets</p>
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>