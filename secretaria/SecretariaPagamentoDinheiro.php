<?php
    session_start();

    // Verifica se o usuário é um secretaria
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
    <title>Pagamento</title>
    <!-- TODO: link do ico -->
     <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <!-- TODO: link do css -->
     <link rel="stylesheet" href="../css/principal.css">
     <link rel="stylesheet" href="../css/caixa.css">
     <link rel="stylesheet" href="../css/CaixaPagamento.css">
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
                    <li><a href="SecretariaClientes.php">Clientes</a></li>
                    <li><a href="SecretariaPet.php">Pets</a></li>
                    <li><a href="SecretariaProdutos.php">Produtos</a></li>
                    <li><a href="SecretariaServiços.php">Serviço</a></li>
                </ul>
            </nav>
        </div>
        <div class="pagamento">
            <div class="pag">
                <div class="CardDin">
                    <nav>
                        <a href="CaixaPagamento.php">Cartão</a>
                        <a href="#" id="selec">Dinheiro</a>
                    </nav>
                </div>
                <form action="">
                    <div class="lin">
                        <div class="PrimLin">
                            <div class="valor">
                                <input type="text" placeholder="Valor: ">
                            </div>
                            <div class="valor">
                                <input type="text" placeholder="Troco:  ">
                            </div>
                        </div>
                        <div class="SecundLin">
                            <div class="valor">
                                <input type="text" placeholder="Valor Pago: ">
                            </div>
                        </div>
                    </div>
                    <div class="botoes">
                        <div>
                            <button class="voltar" id="volt">Cancelar</button>
                        </div>
                        <div>
                            <button id="cade">Finalizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>