<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é um repositor
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'repositor') {
        header("Location: ../entrada/Entrar.php"); // Redireciona se não for repositor
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
            
            // Verifica se a quantidade a subtrair é válida
            if ($quantidadeSubtrair > 0 && $quantidadeSubtrair <= $estoqueProduto) {
                $novoEstoque = $estoqueProduto - $quantidadeSubtrair;

                // Atualiza o estoque no banco de dados
                $sqlUpdate = "UPDATE produto SET estoque = ? WHERE id_produto = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ii", $novoEstoque, $codigoProduto);

                if ($stmtUpdate->execute()) {
                    $mensagem = "Estoque atualizado com sucesso!";
                    $estoqueProduto = $novoEstoque; // Atualiza a variável para refletir a nova quantidade
                } else {
                    $mensagem = "Erro ao atualizar o estoque.";
                }
            } else {
                $mensagem = "Quantidade inválida para subtrair.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Excluir Estoque</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/repositor.css" />
    <link rel="stylesheet" href="../css/Vendas.css">
    <link rel="stylesheet" href="../css/responsivo.css">
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
    <div class="navbar">
        <nav>
            <ul>
                <li><a href="repositor.php">Menu</a></li>
                <li><a href="#" class="desabilitado">Estoque</a></li>
                <li><a href="#" class="desabilitado">Cadastrar Produto</a></li>
                <li><a href="#" class="desabilitado">Editar Produto</a></li>
                <li><a href="repositorExcluir.php">Excluir Estoque</a></li>
            </ul>
        </nav>
    </div>
    <div class="cadastrar">
        <div class="cadastro">
        <?php if ($mensagem): ?>
                <strong><p style="color: <?php echo (strpos($mensagem, 'sucesso') !== false ? '#008B00' : '#CD0000'); ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </p></strong>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="pesquisa-produto">
                    <label for="codigo">Pesquisar ID do Produto:</label>
                    <input
                        type="text"
                        name="codigo"
                        id="codigo"
                        placeholder="Digite o ID do produto"
                        autocomplete=off 
                        value="<?php echo htmlspecialchars($codigoProduto); ?>"
                        required
                    >
                    <button type="submit">Buscar</button>
                </div>
            </form>

            <?php if (!empty($codigoProduto) && !empty($nomeProduto)): ?>
                <form method="POST" action="">
                    <div class="cliente">
                        <p>Subtrair Estoque:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input
                                    type="text"
                                    name="codigo"
                                    class="NomeCliente"
                                    placeholder="Código do Produto:"
                                    autocomplete=off 
                                    value="<?php echo htmlspecialchars($codigoProduto); ?>"
                                    readonly
                                >
                                <input
                                    type="text"
                                    id="preco"
                                    name="preco"
                                    placeholder="Preço do Produto: "
                                    autocomplete=off
                                    value="<?php echo htmlspecialchars(number_format($precoProduto ?? 0, 2, ',', '.')); ?>"
                                    disabled
                                >
                            </div>

                            <div class="coluna">
                                <input
                                    type="text"
                                    name="nome"
                                    class="Telefone"
                                    placeholder="Nome do produto: "
                                    autocomplete=off 
                                    value="<?php echo htmlspecialchars($nomeProduto); ?>"
                                    disabled
                                >
                                <input
                                    type="number"
                                    name="estoque"
                                    class="Email"
                                    placeholder="Quantidade para subtrair:"
                                    autocomplete=off 
                                    value=""
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
                                <img src="../img/lata-de-lixo.png" alt="">
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