<?php
    session_start();

    // Verifica se o usuário é um repositor
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'repositor') {
        header("Location: ../entrada/Entrar.php"); // Redireciona se não for repositor
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
    <title>Cadastrar produto</title>
    <!-- TODO: link do ico -->
     <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <!-- TODO: link do css -->
     <link rel="stylesheet" href="../css/principal.css">
     <link rel="stylesheet" href="../css/repositor.css">
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="repositorEstoque.php" id="selecionado">Estoque</a></li>
                    <li><a href="#">Cadastrar Produto</a></li>
                    <li><a href="repositorExcluir.php">Excluir Produto</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">

                
                <form action="">

                    <div class="cliente">
                        <p>Cadastrar Produtos:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input
                                type="text"
                                name="codigo"
                                class="NomeCliente"
                                placeholder="Codigo: ">
                                <input
                                type="text"
                                id="cpf"
                                placeholder="Preço">
                            </div>
                        
                            <div class="coluna">
                                <input
                                type="text"
                                name="Telefone"
                                class="Telefone"
                                placeholder="Nome do produto">
                                <input
                                type="text"
                                name="estoque"
                                class="Email"
                                placeholder="Estoque">
                            </div>
                        </div>
                    </div>

                    <div class="botoes">
                        <div>
                            <button class="voltar" id="volt">Voltar</button>
                        </div>
                        <div>
                            <button id="cade">Modificar</button>
                            <button id="cade">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>