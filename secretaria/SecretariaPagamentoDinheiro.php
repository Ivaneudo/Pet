<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é uma secretaria
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: ../entrada/Entrar.php"); // Redireciona se não for secretaria
    exit();
}

// Verifica se existem dados de pagamento na sessão
if (!isset($_SESSION['dados_pagamento'])) {
    header("Location: SecretariaServiços.php");
    exit();
}

// Recupera os dados da sessão
$dadosPagamento = $_SESSION['dados_pagamento'];
$valorTotal = $dadosPagamento['valor'];
$cpfCliente = $dadosPagamento['cpf'];
$petsSelecionados = $dadosPagamento['pets'];
$servico = $dadosPagamento['servico'];

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];

// Consulta para obter o nome do cliente
$sqlCliente = "SELECT nome FROM cliente WHERE cpf = ?";
$stmtCliente = $conn->prepare($sqlCliente);
$stmtCliente->bind_param("s", $cpfCliente);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();
$cliente = $resultCliente->fetch_assoc();
$stmtCliente->close();

// Consulta para obter informações dos pets
$petsInfo = [];
foreach ($petsSelecionados as $idPet) {
    $sqlPet = "SELECT nome_pet, especie FROM pet WHERE id_pet = ?";
    $stmtPet = $conn->prepare($sqlPet);
    $stmtPet->bind_param("i", $idPet);
    $stmtPet->execute();
    $resultPet = $stmtPet->get_result();
    $petsInfo[] = $resultPet->fetch_assoc();
    $stmtPet->close();
}

function getNomeServico($codigo) {
    switch($codigo) {
        case 'banho':
            return 'Banho';
        case 'tosa':
            return 'Tosa';
        case 'banho e tosa':
            return 'Banho e Tosa';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pagamento</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/CaixaPagamento.css" />
    <style>
        .info-resumo p {
            color: #6c6b6b;
            font-size: 1em;
            margin: 4px 0;
        }
        .info-resumo {
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Secretaria.php">Menu</a></li>
                    <li><a href="SecretariaVendas.php">Caixa</a></li>
                    <li><a href="SecretariaServiços.php">Serviço</a></li>
                </ul>
            </nav>
        </div>
        <div class="pagamento">
            <div class="pag">
                <div class="info-resumo">
                    <h3 style="color: #4F4F4F;">Resumo do Serviço</h3>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente['nome']); ?></p>
                    <p><strong>Serviço:</strong> <?php echo htmlspecialchars(getNomeServico($servico)); ?></p>
                    <p><strong>Pets:</strong>
                        <?php
                            $nomesPets = array_map(function($pet) {
                                return htmlspecialchars($pet['nome_pet']) . ' (' . htmlspecialchars($pet['especie']) . ')';
                            }, $petsInfo);
                            echo implode(', ', $nomesPets);
                        ?>
                    </p>
                </div>
                <div class="CardDin">
                    <nav>
                        <a href="SecretariaPagamento.php">Cartão</a>
                        <a href="#" id="selec">Dinheiro</a>
                    </nav>
                </div>
                <form action="">
                    <div class="lin">
                        <div class="PrimLin">
                            <div class="valor">
                                <input type="text" id="valor" placeholder="Valor: " readonly>
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
                            <button type="button" class="voltar" id="volt" onclick="window.location.href='cancelarPagamento.php'">Cancelar</button> 
                        </div>
                        <div>
                            <button id="cade">Finalizar</button>
                        </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preenche o campo de valor com o valor total formatado
        document.addEventListener('DOMContentLoaded', function() {
            const valorTotal = <?php echo json_encode(number_format($valorTotal, 2, ',', '.')); ?>;
            document.getElementById('valor').value = 'R$ ' + valorTotal;
        });
    </script>
</body>
</html>
