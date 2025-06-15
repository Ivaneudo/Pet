<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é um repositor
    if ($_SESSION['tipo_usuario'] !== 'repositor'){
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];

    // Inicializa variáveis
    $codigoProduto = '';
    $nomeProduto = '';
    $precoProduto = '';
    $estoqueProduto = '';
    $mensagem = '';

    // Se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Se o código do produto foi enviado
        if (isset($_POST['codigo']) && !empty(trim($_POST['codigo']))) {
            $codigoProduto = trim($_POST['codigo']);

            // Busca o produto pelo ID
            $sql = "SELECT nome_produto, preco, estoque FROM produto WHERE id_produto = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $codigoProduto);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $produto = $result->fetch_assoc();
                $nomeProduto = $produto['nome_produto'];
                $precoProduto = $produto['preco'];
                $estoqueProduto = $produto['estoque'];
            } else {
                $mensagem = "Produto não encontrado.";
            }
        }

        // Se o botão de subtrair estoque foi clicado
        if (isset($_POST['subtrair'])) {
            $quantidadeSubtrair = intval($_POST['estoque']);
            $codigoProduto = intval($_POST['codigo_hidden']);
            
            // Busca novamente o estoque atual para garantir que não foi alterado
            $sqlEstoque = "SELECT estoque FROM produto WHERE id_produto = ? LIMIT 1";
            $stmtEstoque = $conn->prepare($sqlEstoque);
            $stmtEstoque->bind_param("i", $codigoProduto);
            $stmtEstoque->execute();
            $resultEstoque = $stmtEstoque->get_result();
            
            if ($resultEstoque->num_rows > 0) {
                $produtoAtual = $resultEstoque->fetch_assoc();
                $estoqueAtual = $produtoAtual['estoque'];
                
                // Verifica se a quantidade a subtrair é válida
                if ($quantidadeSubtrair > 0 && $quantidadeSubtrair <= $estoqueAtual) {
                    $novoEstoque = $estoqueAtual - $quantidadeSubtrair;

                    // Atualiza o estoque no banco de dados
                    $sqlUpdate = "UPDATE produto SET estoque = ? WHERE id_produto = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("ii", $novoEstoque, $codigoProduto);

                    if ($stmtUpdate->execute()) {
                        $_SESSION['message'] = "Estoque atualizado com sucesso!";
                        $_SESSION['message_type'] = 'sucesso';
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit();
                    } else {
                        $mensagem = "Erro ao atualizar o estoque.";
                    }
                } else {
                    $mensagem = "Quantidade inválida para subtrair. Estoque atual: $estoqueAtual";
                }
            } else {
                $mensagem = "Produto não encontrado para atualização.";
            }
        }
    }

    // Mensagem de sucesso armazenada na sessão
    if (isset($_SESSION['message'])) {
        $mensagem = $_SESSION['message'];
        $classeMensagem = $_SESSION['message_type'];
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Subtrair Estoque</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/repositor.css" />
    <link rel="stylesheet" href="../css/Vendas.css">
    <link rel="stylesheet" href="../css/responsivo.css">
    <link rel="stylesheet" href="../css/mensagem.css">
    <script defer>
    document.addEventListener('DOMContentLoaded', function() {
        const codigoProduto = "<?php echo $codigoProduto; ?>";
        const voltarSomeDiv = document.querySelector('.voltarSome');
        
        if (codigoProduto && voltarSomeDiv) {
            voltarSomeDiv.style.display = 'none';
        }
    });
    </script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem-vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a>
            </div>
        </div>
        <div class="cadastrar" id="repositor">
            <div class="cadastro">
                <?php if ($mensagem): ?>
                    <div class="mensagem-<?php echo $classeMensagem ?? 'erro'; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="pesquisa-produto">
                        <h3>Subtrair Estoque:</h3>
                        <label for="codigo">Pesquisar ID do Produto:</label>
                        <input
                            type="text"
                            name="codigo"
                            id="codigo"
                            placeholder="Digite o ID do produto"
                            autocomplete="off"
                            maxlength="3"
                            value="<?php echo htmlspecialchars($codigoProduto); ?>"
                            required
                        >
                        <button type="submit">Buscar</button>
                    </div>
                    <div class="botoes">
                        <div class='voltarSome'>
                            <a href="repositor.php">
                                <button class="voltar" id="volt" type="button">Voltar</button>
                            </a>
                        </div>
                    </div>
                </form>

                <?php if (!empty($codigoProduto) && !empty($nomeProduto)): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="codigo_hidden" value="<?php echo htmlspecialchars($codigoProduto); ?>">
                        <div class="cliente">
                            <div class="colunas">
                                <div class="coluna">
                                    <label for="codigo">Código:</label>
                                    <input
                                        type="number"
                                        name="codigo_display"
                                        class="NomeCliente"
                                        placeholder="Código do Produto:"
                                        autocomplete="off"
                                        min="1"
                                        max="999"
                                        maxlength="3"
                                        value="<?php echo htmlspecialchars($codigoProduto); ?>"
                                        style="color: #4d4848; cursor: not-allowed;"
                                        disabled
                                    >
                                    <label for="estoque-atual">Estoque Atual:</label>
                                    <input
                                        type="number"
                                        name="estoque-atual"
                                        class="Email"
                                        placeholder="Estoque:"
                                        autocomplete="off"
                                        value="<?php echo htmlspecialchars($estoqueProduto); ?>"
                                        style="color: #4d4848; cursor: not-allowed;"
                                        disabled
                                    >
                                </div>

                                <div class="coluna">
                                    <label for="nome">Nome do Produto:</label>
                                    <input
                                        type="text"
                                        name="nome"
                                        class="Telefone"
                                        placeholder="Nome do produto: "
                                        autocomplete="off"
                                        value="<?php echo htmlspecialchars($nomeProduto); ?>"
                                        style="color: #4d4848; cursor: not-allowed;"
                                        disabled
                                    >

                                    <label for="subtrair">Subtrair:</label>
                                    <input
                                        type="number"
                                        name="estoque"
                                        class="Email"
                                        placeholder="Quantidade para subtrair:"
                                        autocomplete="off"
                                        min="1"
                                        max="<?php echo htmlspecialchars($estoqueProduto); ?>"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="botoes">
                            <div>
                                <a href="repositor.php">
                                    <button class="voltar" id="volt" type="button">Voltar</button>
                                </a>
                            </div>
                            <div>
                                <button id="cade" type="submit" name="subtrair">
                                    <img src="../img/lata-de-lixo-preta.png" alt="Subtrair Estoque">
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>