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
$codigoProduto = $_POST['codigo'] ?? '';
$nomeProduto = '';
$precoProduto = '';
$estoqueProduto = '';
$mensagem = '';

if (!empty($codigoProduto)) {
    // Se atualizando estoque
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar'])) {
        $quantidadeSubtrair = intval($_POST['estoque']);
        // Busca estoque atual para verificar
        $sqlEstoque = "SELECT nome_produto, preco, estoque FROM produto WHERE id_produto = ? LIMIT 1";
        $stmtEstoque = $conn->prepare($sqlEstoque);
        $stmtEstoque->bind_param("i", $codigoProduto);
        $stmtEstoque->execute();
        $resultEstoque = $stmtEstoque->get_result();

        if ($resultEstoque->num_rows > 0) {
            $produto = $resultEstoque->fetch_assoc();
            $estoqueAtual = intval($produto['estoque']);
            $nomeProduto = $produto['nome_produto'];
            $precoProduto = $produto['preco'];

            if ($quantidadeSubtrair > $estoqueAtual) {
                $mensagem = "Não foi possível alterar o estoque: a quantidade a subtrair é maior que o estoque atual.";
                $estoqueProduto = $estoqueAtual;
            } elseif ($quantidadeSubtrair <= 0) {
                $mensagem = "Informe uma quantidade válida para subtrair.";
                $estoqueProduto = $estoqueAtual;
            } else {
                $novoEstoque = $estoqueAtual - $quantidadeSubtrair;
                // Atualiza o estoque
                $sqlUpdate = "UPDATE produto SET estoque = ? WHERE id_produto = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ii", $novoEstoque, $codigoProduto);
                if ($stmtUpdate->execute()) {
                    $mensagem = "Estoque atualizado com sucesso!";
                    $estoqueProduto = $novoEstoque;
                } else {
                    $mensagem = "Erro ao atualizar o estoque.";
                    $estoqueProduto = $estoqueAtual;
                }
            }
        } else {
            $mensagem = "Produto não encontrado.";
        }
    } else {
        // Se só buscando os dados do produto pelo código
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
    <link rel="stylesheet" href="../css/repositor.css" />
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
                <li><a href="repositorEstoque.php" id="selecionado">Estoque</a></li>
                <li><a href="repositorCadastrar.php">Cadastrar Produto</a></li>
                <li><a href="repositorEditar.php">Editar Produto</a></li>
                <li><a href="repositorExcluir.php">Excluir Produto</a></li>
            </ul>
        </nav>
    </div>
    <div class="cadastrar">
        <div class="cadastro">
            <?php if ($mensagem): ?>
                <p style="color: <?php echo (strpos($mensagem, 'sucesso') !== false ? 'green' : 'red'); ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </p>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="cliente">
                    <p>Excluir Produtos:</p>
                    <div class="colunas">
                        <div class="coluna">
                            <input
                                type="text"
                                name="codigo"
                                class="NomeCliente"
                                placeholder="Codigo"
                                value="<?php echo htmlspecialchars($codigoProduto); ?>"
                                required
                            >
                            <input
                                type="text"
                                id="cpf"
                                name="preco"
                                placeholder="Preço"
                                value="<?php echo htmlspecialchars(number_format($precoProduto ?? 0, 2, ',', '.')); ?>"
                                disabled
                            >
                        </div>

                        <div class="coluna">
                            <input
                                type="text"
                                name="Telefone"
                                class="Telefone"
                                placeholder="Nome do produto"
                                value="<?php echo htmlspecialchars($nomeProduto); ?>"
                                disabled
                            >
                            <input
                                type="number"
                                name="estoque"
                                class="Email"
                                placeholder="Quantidade para subtrair"
                                min="0"
                                value=""
                                <?php echo empty($estoqueProduto) ? 'disabled' : ''; ?>
                            >
                            <input type="hidden" name="estoque_atual" value="<?php echo htmlspecialchars($estoqueProduto); ?>">
                        </div>
                    </div>
                </div>
                <div class="botoes">
                    <div>
                        <a href="repositor.php">
                            <button class="voltar" id="volt" type="button">Voltar</button>
                        </a>
                        <button id="cade" type="submit" name="buscar">Buscar</button>
                    </div>
                    <div>
                        <button id="cade" type="submit" name="atualizar" <?php echo empty($codigoProduto) ? 'disabled' : ''; ?>>
                            <img src="../img/lata-de-lixo.png" alt="img lata de lixo" />
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>