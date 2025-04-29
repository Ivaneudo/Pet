<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa</title>
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
                <p>Olá <span id="colaborador">[nome do funcionário]</span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <p>sair</p>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="repositorEstoque.php" id="selecionado">Estoque</a></li>
                    <li><a href="repositorCadastrar.php">Cadastrar Produto</a></li>
                    <li><a href="#">Excluir Produto</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">

                
                <form action="">

                    <div class="cliente">
                        <p>Excluir Produtos:</p>
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
                            <button id="cade">
                                <img src="../img/lata-de-lixo.png" alt="">
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>