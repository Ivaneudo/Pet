<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é uma secretaria
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: ../entrada/Entrar.php"); // Redireciona se não for secretaria
    exit();
}

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];

// Inicializa o carrinho na sessão se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$erro = '';
$mensagem = '';
$valorTotal = 0.0;
$mostrarCpf = false;

// Processa adição de produto por código
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['adicionar'])) {
        $codigoProduto = trim($_POST['codigo_produto']);

        if ($codigoProduto === '') {
            $erro = "Por favor, digite o código do produto.";
        } else {
            // Consulta o produto no banco
            $sql = "SELECT id_produto, nome_produto, preco, estoque FROM produto WHERE id_produto = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $codigoProduto);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($produto = $result->fetch_assoc()) {
                // Verifica se já está no carrinho
                $exists = false;
                foreach ($_SESSION['carrinho'] as &$item) {
                    if ($item['id_produto'] == $produto['id_produto']) {
                        // Incrementa quantidade se estoque permitir
                        if ($item['quantidade'] < $produto['estoque']) {
                            $item['quantidade']++;
                            $mensagem = "Produto adicionado ao carrinho.";
                        } else {
                            $erro = "Estoque insuficiente para o produto: " . htmlspecialchars($produto['nome_produto']);
                        }
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    if ($produto['estoque'] > 0) {
                        // Adiciona novo produto no carrinho
                        $_SESSION['carrinho'][] = [
                            'id_produto' => $produto['id_produto'],
                            'nome_produto' => $produto['nome_produto'],
                            'preco' => $produto['preco'],
                            'quantidade' => 1
                        ];
                        $mensagem = "Produto adicionado ao carrinho.";
                    } else {
                        $erro = "Produto fora de estoque.";
                    }
                }
            } else {
                $erro = "Produto não encontrado para o código informado.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['excluir'])) {
        // Remove produto do carrinho pelo id_produto
        $idExcluir = $_POST['id_produto_excluir'];
        foreach ($_SESSION['carrinho'] as $key => $item) {
            if ($item['id_produto'] == $idExcluir) {
                unset($_SESSION['carrinho'][$key]);
                $_SESSION['carrinho'] = array_values($_SESSION['carrinho']); // Reindexar array
                $mensagem = "Produto removido do carrinho.";
                break;
            }
        }
    } elseif (isset($_POST['cancelar'])) {
        // Limpa o carrinho
        $_SESSION['carrinho'] = [];
        $mensagem = "Compra cancelada.";
    } elseif (isset($_POST['pontuar'])) {
        // Exibir CPF do cliente para digitar
        $mostrarCpf = true;
        // Mantém mensagens para a próxima renderização se precisarem (sem pontuar ainda)
    } elseif (isset($_POST['finalizar'])) {
        // Finalizar compra, salvar venda no banco (vai para tela pagamento)
        if (count($_SESSION['carrinho']) == 0) {
            $erro = "Carrinho vazio, não é possível finalizar a compra.";
        } else {
            $cpfCliente = trim($_POST['cpf_cliente']);
            if (empty($cpfCliente)) {
                $erro = "Informe o CPF do cliente para finalizar a compra.";
                $mostrarCpf = true; // para mostrar o campo CPF se o usuário esquecer
            } else {
                // Verificar cliente
                $sql = "SELECT id_cliente FROM cliente WHERE cpf = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $cpfCliente);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($cliente = $result->fetch_assoc()) {
                    // Armazenar dados para tela de pagamento
                    $_SESSION['dados_pagamento'] = [
                        'carrinho' => $_SESSION['carrinho'],
                        'valor_total' => 0,
                        'cpf_cliente' => $cpfCliente
                    ];
                    // Calcula o valor total e armazena
                    $total = 0;
                    foreach ($_SESSION['carrinho'] as $item) {
                        $total += $item['preco'] * $item['quantidade'];
                    }
                    $_SESSION['dados_pagamento']['valor_total'] = $total;

                    // Redireciona para SecretariaPagamento.php para escolher forma de pagamento
                    header("Location: SecretariaPagamento.php");
                    exit();
                } else {
                    $erro = "Cliente com CPF informado não encontrado.";
                    $mostrarCpf = true;
                }
                $stmt->close();
            }
        }
    }
}

// Calcula valor total atual
foreach ($_SESSION['carrinho'] as $item) {
    $valorTotal += $item['preco'] * $item['quantidade'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Produtos</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/produtos.css" />
    <script src="../js/CaixaProdutos.js" defer></script>
    <script src="../js/mascara.js" defer></script>
    <style>
        button {
            cursor: pointer;
        }
        .Escondido {
            display: none;
        }
        .Aparece {
            display: block;
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
                    <li><a href="SecretariaClientes.php">Clientes</a></li>
                    <li><a href="SecretariaPet.php">Pets</a></li>
                    <li><a href="SecretariaProdutos.php">Produtos</a></li>
                    <li><a href="SecretariaServiços.php">Serviço</a></li>
                </ul>
            </nav>
        </div>

        <div class="produtos">
            <div class="bloco">
                <!-- Mensagens -->
                <?php if ($mensagem): ?>
                    <div class="mensagem" role="alert" style="color:green; margin-bottom: 10px;"><?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
                <?php if ($erro): ?>
                    <div class="erro" role="alert" style="color:red; margin-bottom: 10px;"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <!-- Form para adicionar produtos -->
                <form method="POST" action="" aria-label="Adicionar produto">
                    <div class="pesquisa">
                        <div class="campo">
                            <input
                                type="text"
                                name="codigo_produto"
                                placeholder="Digite o código do produto: "
                                maxlength="10"
                                pattern="\d+"
                                title="Digite apenas números"
                                required
                                autocomplete="off"
                            />
                            <button type="submit" name="adicionar" aria-label="Adicionar produto" style="background:none; border:none; cursor:pointer;">
                                <img src="../img/search-svgrepo-com.svg" alt="Adicionar" style="width:24px; height:24px;" />
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabela de compras -->
                <form method="POST" action="" aria-label="Carrinho de compras" id="formCarrinho">
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
                                <?php if (count($_SESSION['carrinho']) === 0): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center;">Nenhum produto no carrinho</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($_SESSION['carrinho'] as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nome_produto']); ?></td>
                                    <td>R$: <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                    <td><?php echo (int)$item['quantidade']; ?></td>
                                    <td>
                                        <button type="submit" name="excluir" value="1" class="excluir" aria-label="Excluir <?php echo htmlspecialchars($item['nome_produto']); ?>" style="background:none; border:none; color:red; font-weight:bold; cursor:pointer;">X</button>
                                        <input type="hidden" name="id_produto_excluir" value="<?php echo $item['id_produto']; ?>" />
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <table class="final">
                            <tr>
                                <td>Valor final da compra</td>
                                <td>R$: <span><?php echo number_format($valorTotal, 2, ',', '.'); ?></span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="pesquisa" id="Pcpf" style="margin-top: 15px;">
                        <div id="Cpf" class="<?php echo ($mostrarCpf) ? 'Aparece' : 'Escondido'; ?>">
                            <input
                                type="text"
                                id="cpf"
                                name="cpf_cliente"
                                maxlength="14"
                                placeholder="CPF do cliente: "
                                autocomplete="off"
                                aria-label="CPF do cliente"
                                <?php echo ($mostrarCpf) ? 'required' : 'disabled'; ?>
                            />
                        </div>
                    </div>

                    <div class="botoes" style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="submit" class="voltar" name="cancelar">Cancelar Compra</button>
                        <button type="submit" name="pontuar" id="pont">Pontuar</button>
                        <button type="submit" name="finalizar">Finalizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

