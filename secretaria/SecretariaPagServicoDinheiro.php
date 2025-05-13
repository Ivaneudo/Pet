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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // valor pago informado pelo usuário
        $valorPago = floatval(str_replace(',', '.', $_POST['valor_pago'])); 

        if ($valorPago < $valorTotal) {
            $erro = "O valor pago deve ser igual ou maior que o valor total da compra.";
        } else {
            // Consulta para obter o ID da secretaria
            $sqlSecretaria = "SELECT secretaria_id FROM secretaria WHERE nome = ?";
            $stmtSecretaria = $conn->prepare($sqlSecretaria);
            $stmtSecretaria->bind_param("s", $nomeFuncionario);
            $stmtSecretaria->execute();
            $resultSecretaria = $stmtSecretaria->get_result();
            $secretaria = $resultSecretaria->fetch_assoc();
            $stmtSecretaria->close();

            $secretariaId = $secretaria['secretaria_id'];

            // Insere os dados na tabela servico
            foreach ($petsSelecionados as $idPet) {
                $sqlServico = "INSERT INTO servico (secretaria_id, id_pet, servico, valor_servico, forma_de_pagamento) VALUES (?, ?, ?, ?, 'Dinheiro')";
                $stmtServico = $conn->prepare($sqlServico);
                $stmtServico->bind_param("iiss", $secretariaId, $idPet, $servico, $valorTotal);
                $stmtServico->execute();
                $stmtServico->close();
            }

            // Redireciona para uma página de confirmação ou sucesso
            header("Location: sucesso.php");
            exit();
        }
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

    function formataMoeda($valor) {
        return 'R$ ' . number_format($valor, 2, ',', '.');
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
    <script src="../js/troco.js" defer></script>
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
        
        .desabilitado{
            cursor: not-allowed;
        }

        .erro {
            color: #b00020;
            font-weight: bold;
            margin-bottom: 10px;
        }

        input[readonly] {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="Logo Pethop" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="#" class="desabilitado">Menu</a></li>
                    <li><a href="#" class="desabilitado">Caixa</a></li>
                    <li><a href="#" class="desabilitado">Serviço</a></li>
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
                        <a href="SecretariaPagServico.php">Cartão</a>
                        <a href="#" id="selec">Dinheiro</a>
                    </nav>
                </div>
                <?php if (!empty($erro)): ?>
                    <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="lin">
                        <div class="PrimLin">
                            <div class="valor">
                                <input type="text" id="valor" placeholder="Valor: " value="<?php echo formataMoeda($valorTotal); ?>" readonly data-valor="<?php echo $valorTotal; ?>">
                            </div>
                            <div class="valor">
                                <input type="text" id="troco" placeholder="Troco: " value="" readonly>
                            </div>
                        </div>
                        <div class="SecundLin">
                            <div class="valor">
                                <input 
                                    type="number" 
                                    name="valor_pago" 
                                    id="valor_pago"
                                    placeholder="Valor Pago: "
                                    step="0.01" 
                                    min="<?php echo number_format($valorTotal, 2, '.', ''); ?>" 
                                    value="<?php echo isset($valorPago) ? htmlspecialchars(number_format($valorPago, 2, '.', '')) : ''; ?>" 
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    <div class="botoes" style="margin-top:15px; display:flex; gap:10px;">
                        <div>
                            <button type="button" class="voltar" id="volt" onclick="window.location.href='cancelarPagamento.php'">Cancelar</button> 
                        </div>
                        <div>
                            <button type="submit" id="cade">Finalizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>