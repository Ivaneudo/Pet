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

    // Lógica para processar o POST após o envio do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $formaPagamento = $_POST['cartao']; // 'Crédito' ou 'Débito'
        $valorPago = $_POST['valor']; // valor total da compra

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
            $sqlServico = "INSERT INTO servico (secretaria_id, id_pet, servico, valor_servico, forma_de_pagamento) VALUES (?, ?, ?, ?, ?)";
            $stmtServico = $conn->prepare($sqlServico);
            $stmtServico->bind_param("iisss", $secretariaId, $idPet, $servico, $valorPago, $formaPagamento);
            $stmtServico->execute();
            $stmtServico->close();
        }

        // Após inserir, pode redirecionar para página de sucesso
        header("Location: sucesso.php");
        exit();
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
    <link rel="stylesheet" href="../css/responsivo.css">
    <link rel="stylesheet" href="../css/Vendas.css">
    <script src="../js/pagamentoCredito.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a>
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
                        <a href="SecretariaPagServicoDinheiro.php">Dinheiro</a>
                    </nav>
                </div>
                
                <form method="POST" action="">
                    <input 
                        type="hidden" 
                        name="valor" 
                        value="<?php echo htmlspecialchars($valorCompra); ?>" />
                    
                    <input 
                        type="hidden" 
                        name="cpf" 
                        value="<?php echo htmlspecialchars($cpfCliente); ?>" />
                    
                    <input 
                        type="hidden" 
                        name="pets" 
                        value="<?php echo htmlspecialchars(implode(',', $petsSelecionados)); ?>" />
                    
                    <input 
                        type="hidden" 
                        name="servico" 
                        value="<?php echo htmlspecialchars($servico); ?>" />
                    
                    <div class="CredDeb">
                        <input 
                            type="radio" 
                            name="cartao" 
                            id="credito" 
                            value="Crédito" 
                            required>
                        <label for="credito">Crédito</label>
                        
                        <input 
                            type="radio" 
                            name="cartao" 
                            id="debito" 
                            value="Débito">
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
                        <button type="button" class="voltar" id="volt" onclick="window.location.href='cancelarPagamento.php'">Cancelar</button>
                        
                        <button type="submit" id="cade">Finalizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>