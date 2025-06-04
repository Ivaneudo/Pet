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
        header("Location: SecretariaVendas.php");
        exit();
    }

    // Recupera os dados da sessão
    $dadosPagamento = $_SESSION['dados_pagamento'];
    $valorTotal = $dadosPagamento['valor_total'];
    $cpfCliente = $dadosPagamento['cpf_cliente'];
    $carrinho = $dadosPagamento['carrinho'];

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];

    // Consulta para obter o ID da secretaria
    $sqlSecretaria = "SELECT secretaria_id FROM secretaria WHERE nome = ?";
    $stmtSecretaria = $conn->prepare($sqlSecretaria);
    $stmtSecretaria->bind_param("s", $nomeFuncionario);
    $stmtSecretaria->execute();
    $resultSecretaria = $stmtSecretaria->get_result();
    $secretaria = $resultSecretaria->fetch_assoc();
    $stmtSecretaria->close();

    $secretariaId = $secretaria['secretaria_id'];

    // Consulta para obter nome do cliente
    $clienteNome = 'Cliente não informado';
    if (!empty($cpfCliente)) {
        $sqlCliente = "SELECT nome FROM cliente WHERE cpf = ?";
        $stmtCliente = $conn->prepare($sqlCliente);
        $stmtCliente->bind_param("s", $cpfCliente);
        $stmtCliente->execute();
        $resultCliente = $stmtCliente->get_result();
        if ($resultCliente && $resultCliente->num_rows > 0) {
            $clienteData = $resultCliente->fetch_assoc();
            $clienteNome = $clienteData['nome'];
        }
        $stmtCliente->close();
    }

    $erro = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $valorPago = floatval(str_replace(',', '.', $_POST['valor_pago']));
        if ($valorPago < $valorTotal) {
            $erro = "O valor pago deve ser igual ou maior que o valor total da compra.";
        } else {
            // Apenas salva na tabela vendas os campos existentes (sem valor_pago e troco)
            if (empty($cpfCliente)) {
                $sqlVenda = "INSERT INTO vendas (secretaria_id, valor_compra, forma_de_pagamento) VALUES (?, ?, 'Dinheiro')";
                $stmtVenda = $conn->prepare($sqlVenda);
                $stmtVenda->bind_param("id", $secretariaId, $valorTotal);
            } else {
                $sqlVenda = "INSERT INTO vendas (secretaria_id, cpf_cliente, valor_compra, forma_de_pagamento) VALUES (?, ?, ?, 'Dinheiro')";
                $stmtVenda = $conn->prepare($sqlVenda);
                $stmtVenda->bind_param("isd", $secretariaId, $cpfCliente, $valorTotal);
            }

            if ($stmtVenda->execute()) {
                // Atualiza o estoque para cada produto no carrinho
                foreach ($carrinho as $item) {
                    $sqlAtualizaEstoque = "UPDATE produto SET estoque = estoque - ? WHERE id_produto = ?";
                    $stmtEstoque = $conn->prepare($sqlAtualizaEstoque);
                    $stmtEstoque->bind_param("ii", $item['quantidade'], $item['id_produto']);
                    $stmtEstoque->execute();
                    $stmtEstoque->close();
                }

                unset($_SESSION['dados_pagamento']);
                unset($_SESSION['carrinho']);
                header("Location: sucesso.php");
                exit();
            } else {
                $erro = "Erro ao registrar a venda: " . $stmtVenda->error;
            }
            $stmtVenda->close();
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
    <title>Pagamento em Dinheiro</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px.ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/CaixaPagamento.css" />
    <link rel="stylesheet" href="../css/responsivo.css">
    <link rel="stylesheet" href="../css/Vendas.css">
    <script src="../js/troco.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="Logo Pethop" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair"><a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a></div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Secretaria.php">Menu</a></li>
                    <li><a href="SecretariaVendas.php">Caixa</a></li>
                    <li><a href="SecretariaServicos.php">Serviço</a></li>
                </ul>
            </nav>
        </div>
        <div class="pagamento">
            <div class="pag">
                <div class="info-resumo">
                    <h3 style="color:#4F4F4F;">Resumo da Compra</h3>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($clienteNome); ?></p>
                    <p><strong>Valor Total:</strong> <?php echo formataMoeda($valorTotal); ?></p>
                </div>
                <div class="CardDin">
                    <nav>
                        <a href="SecretariaPagamento.php">Cartão</a>
                        <a href="#" id="selec">Dinheiro</a>
                    </nav>
                </div>
                <?php if ($erro): ?>
                    <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="lin">
                        <div class="PrimLin">
                            <div class="valor">
                                <input 
                                    type="text" 
                                    id="valor" 
                                    placeholder="Valor:" 
                                    value="<?php echo formataMoeda($valorTotal); ?>" 
                                    readonly 
                                    data-valor="<?php echo $valorTotal; ?>" />
                            </div>
                            <div class="valor">
                                <input 
                                    type="text" 
                                    id="troco" 
                                    placeholder="Troco:" 
                                    value="" 
                                    readonly />
                            </div>
                        </div>
                        <div class="SecundLin">
                            <div class="valor">
                                <input 
                                    type="number" 
                                    name="valor_pago" 
                                    id="valor_pago" 
                                    placeholder="Valor Pago" 
                                    step="0.01" 
                                    min="<?php echo number_format($valorTotal, 2, '.', ''); ?>" 
                                    value="<?php echo isset($valorPago) ? htmlspecialchars(number_format($valorPago, 2, '.', '')) : ''; ?>" 
                                    required
                                />
                            </div>
                        </div>
                    </div>
                    <div class="botoes" style="margin-top:15px; display:flex; gap:10px;">
                        <button type="button" class="voltar" onclick="window.location.href='cancelarPagamento.php'">Cancelar</button>
                        
                        <button type="submit">Finalizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>