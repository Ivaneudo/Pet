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
    $valorCompra = $dadosPagamento['valor_total']; // Valor total da compra
    $cpfCliente = $dadosPagamento['cpf_cliente']; // CPF do cliente (pode ser null)
    $carrinho = $dadosPagamento['carrinho']; // Carrinho de compras (array de produtos)

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

    // Consulta para obter nome do cliente, se CPF informado
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
        $formaPagamento = $_POST['cartao'] ?? '';
        if (empty($formaPagamento)) {
            $erro = "Por favor, selecione a forma de pagamento.";
        } else {
            // Inserir uma linha na tabela vendas para cada produto no carrinho
            foreach ($carrinho as $item) {
                $sqlVenda = "INSERT INTO vendas (secretaria_id, cpf_cliente, valor_compra, forma_de_pagamento) VALUES (?, ?, ?, ?)";
                $stmtVenda = $conn->prepare($sqlVenda);

                // cpf_cliente pode ser null para clientes sem cpf informado
                if (empty($cpfCliente)) {
                    $cpfParaInsert = null;
                } else {
                    $cpfParaInsert = $cpfCliente;
                }

                // Valor da compra para o produto: preco * quantidade
                $valorProduto = $item['preco'] * $item['quantidade'];

                if ($cpfParaInsert === null) {
                    // Para inserir null diretamente no SQL
                    $stmtVenda->close();
                    $sqlVendaNullCpf = "INSERT INTO vendas (secretaria_id, cpf_cliente, valor_compra, forma_de_pagamento) VALUES (?, NULL, ?, ?)";
                    $stmtVendaNullCpf = $conn->prepare($sqlVendaNullCpf);
                    $stmtVendaNullCpf->bind_param("ids", $secretariaId, $valorProduto, $formaPagamento);
                    $stmtVendaNullCpf->execute();
                    $stmtVendaNullCpf->close();
                } else {
                    $stmtVenda->bind_param("isds", $secretariaId, $cpfCliente, $valorProduto, $formaPagamento);
                    $stmtVenda->execute();
                    $stmtVenda->close();
                }

                // Atualiza o estoque para cada produto no carrinho
                $sqlAtualizaEstoque = "UPDATE produto SET estoque = estoque - ? WHERE id_produto = ?";
                $stmtEstoque = $conn->prepare($sqlAtualizaEstoque);
                $stmtEstoque->bind_param("ii", $item['quantidade'], $item['id_produto']);
                $stmtEstoque->execute();
                $stmtEstoque->close();
            }

            // Limpa os dados de pagamento da sessão após finalizar
            unset($_SESSION['dados_pagamento']);
            unset($_SESSION['carrinho']);

            // Redireciona para a página de sucesso
            header("Location: sucesso.php");
            exit();
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
                    <h3 style="color: #4F4F4F;">Resumo da Compra</h3>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($clienteNome); ?></p>
                    <p><strong>Valor Total:</strong> R$ <?php echo number_format($valorCompra, 2, ',', '.'); ?></p>
                </div>
                <div class="CardDin">
                    <nav>
                        <a href="#" id="selec">Cartão</a>
                        <a href="SecretariaPagamentoDinheiro.php">Dinheiro</a>
                    </nav>
                </div>
                <?php if (!empty($erro)): ?>
                    <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <input 
                        type="hidden" 
                        name="valor" 
                        value="<?php echo htmlspecialchars($valorCompra); ?>" />
                    
                    <input 
                        type="hidden" 
                        name="cpf" 
                        value="<?php echo htmlspecialchars($cpfCliente); ?>" />

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
                                <input 
                                    type="text" 
                                    name="valor_display" 
                                    placeholder="Valor:" 
                                    value="R$ <?php echo number_format($valorCompra, 2, ',', '.'); ?>" 
                                    readonly>
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