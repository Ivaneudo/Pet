<?php
    session_start();

    // Verifica se o usuário é um caixa
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'caixa') {
        header("Location: ../entrada/Entrar.php"); // Redireciona se não for admin
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
    <!-- TODO: link do ico -->
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <!-- TODO: link do css -->
     <link rel="stylesheet" href="../css/principal.css">
     <link rel="stylesheet" href="../css/caixa.css">
     <link rel="stylesheet" href="../css/produtos.css">
    <!-- TODO: link do js-->
     <script src="../js/CaixaProdutos.js" defer></script>
        <!-- TODO: link da mascara -->
     <script src="../js/mascara.js" defer></script>
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
                    <li><a href="CaixaCadastrar.php">Cadastrar</a></li>
                    <li><a href="CaixaProdutos.php" id="selecionado">Produtos</a></li>
                    <li><a href="CaixaServiços.php">Serviço</a></li>
                </ul>
            </nav>
        </div>

        <div class="produtos">
            <div class="bloco">
                <div class="pesquisa">
                    <div class="campo">
                        <input
                        type="text"
                        placeholder="Digite o código do produto: ">
                        <img src="../img/search-svgrepo-com.svg" alt="">
                    </div>
                </div>

                <div class="compras">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome do produto</th>
                                <th>Valor</th>
                                <th>Quantidade</th>
                                <th>Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                            <tr>
                                <td>Coleira de cachorro</td>
                                <td>24,00</td>
                                <td>1</td>
                                <td class="excluir">X</td>
                            </tr>
                        </tbody>
                        <table class="final">
                            <tr>
                                <td>Valor final da compra</td>
                                <td>R$: <span>24,99</span></td>
                            </tr>
                        </table>
                    </table>
                    
                </div>
                <div class="pesquisa" id="Pcpf">
                    <div class="Escondido" id="Cpf"> 
                        <input type="text" id="cpf" maxlength="14" placeholder="CPF do cliente: "> 
                    </div>
                </div>

                <div class="botoes">
                    <div>
                        <button class="voltar" id="cancel">Cancelar Compra</button>
                    </div>
                    <div>
                        <button id="pont">Pontuar</button>
                        <button>Finalizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>