<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é uma secretaria
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: ../entrada/Entrar.php");
    exit();
}

// Verifica se existem dados de pagamento na sessão
if (!isset($_SESSION['dados_pagamento'])) {
    header("Location: SecretariaServiços.php");
    exit();
}

// Recupera os dados da sessão
$dadosPagamento = $_SESSION['dados_pagamento'];
$valorCompra = $dadosPagamento['valor'];
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
        }
        #parcelasContainer {
            display: none; /* Esconde inicialmente */
            margin-top: 10px;
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
                    <p><strong>Serviço:</strong> 
                        <?php 
                            switch($servico) {
                                case 'banho': echo 'Banho'; break;
                                case 'tosa': echo 'Tosa'; break;
                                case 'banho e tosa': echo 'Banho e Tosa'; break;
                            }
                        ?>
                    </p>
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
                        <a href="#" id="selec">Cartão</a>
                        <a href="SecretariaPagamentoDinheiro.php">Dinheiro</a>
                    </nav>
                </div>
                
                <form method="POST" action="finalizarPagamento.php">
                    <input type="hidden" name="valor" value="<?php echo htmlspecialchars($valorCompra); ?>" />
                    <input type="hidden" name="cpf" value="<?php echo htmlspecialchars($cpfCliente); ?>" />
                    <input type="hidden" name="pets" value="<?php echo htmlspecialchars(implode(',', $petsSelecionados)); ?>" />
                    <input type="hidden" name="servico" value="<?php echo htmlspecialchars($servico); ?>" />
                    
                    <div class="CredDeb">
                        <input type="radio" name="cartao" id="credito" value="Crédito" required>
                        <label for="credito">Crédito</label>
                        <input type="radio" name="cartao" id="debito" value="Débito">
                        <label for="debito">Débito</label>
                    </div>
                    
                    <div class="lin">
                        <div class="PrimLin">
                            <div class="valor">
                                <input type="text" name="valor_display" placeholder="Valor:" value="R$ <?php echo number_format($valorCompra, 2, ',', '.'); ?>" readonly>
                            </div>
                            <div class="valor" id="parcelasContainer">
                                <select name="parcelas" id="parcelas">
                                    <option value="" disabled selected>Vezes</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="botoes" style="margin-top:15px; display:flex; gap:10px;">
                        <button type="button" class="voltar" id="volt" onclick="window.location.href='SecretariaServiços.php'">Cancelar</button>
                        
                        <button type="submit" id="cade">Finalizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const creditoRadio = document.getElementById('credito');
        const debitoRadio = document.getElementById('debito');
        const parcelasContainer = document.getElementById('parcelasContainer');
        const parcelasSelect = document.getElementById('parcelas');

        function atualizarParcelas() {
            if (creditoRadio.checked) {
                parcelasContainer.style.display = 'block';
                parcelasSelect.setAttribute('required', 'required');
            } else {
                parcelasContainer.style.display = 'none';
                parcelasSelect.removeAttribute('required');
                parcelasSelect.value = "";
            }
        }

        creditoRadio.addEventListener('change', atualizarParcelas);
        debitoRadio.addEventListener('change', atualizarParcelas);

        // Inicializa o estado correto ao carregar a página
        atualizarParcelas();
    });
    </script>
</body>
</html>
