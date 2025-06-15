<?php
    session_start();
    include('../funcoes/conexao.php');

    if ($_SESSION['tipo_usuario'] !== 'admin'){
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    $nomeFuncionario = $_SESSION['usuario'];

    $codigoProduto = '';
    $nomeProduto = '';
    $precoProduto = '';
    $estoqueProduto = '';
    $mensagem = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['codigo']) && !empty(trim($_POST['codigo']))) {
            $codigoProduto = trim($_POST['codigo']);

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
                $nomeProduto = '';
                $precoProduto = '';
                $estoqueProduto = '';
            }
        }

        if (isset($_POST['subtrair'])) {
            $quantidadeSubtrair = intval($_POST['estoque']);
            
            if ($quantidadeSubtrair == $estoqueProduto) {
                $sqlDelete = "DELETE FROM produto WHERE id_produto = ?";
                $stmtDelete = $conn->prepare($sqlDelete);
                $stmtDelete->bind_param("i", $codigoProduto);

                if ($stmtDelete->execute()) {
                    $mensagem = "Produto excluído com sucesso!";
                } else {
                    $mensagem = "Erro ao excluir o produto: " . $stmtDelete->error;
                }
            } else {
                $novoEstoque = $estoqueProduto - $quantidadeSubtrair;
                $sqlUpdate = "UPDATE produto SET estoque = ? WHERE id_produto = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("ii", $novoEstoque, $codigoProduto);

                if ($stmtUpdate->execute()) {
                    $mensagem = "Estoque atualizado com sucesso!";
                } else {
                    $mensagem = "Erro ao atualizar o estoque.";
                }
            }

            // Limpa sempre as informações independente da operação
            $codigoProduto = '';
            $nomeProduto = '';
            $precoProduto = '';
            $estoqueProduto = '';
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
    <link rel="stylesheet" href="../css/responsivo.css">
    <link rel="stylesheet" href="../css/mensagem.css">
    <script src="../js/excluirProduto.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="Logo da Pethop" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem-vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Adm.php" id="selecionado"><span class="icons"><img src="../img/menu.png" alt="ícone menu"></span>Menu</a></li>
                    <li><a href="AdmEstoque.php" id="selecionado"><span class="icons"><img src="../img/produtos.png" alt="ícone produtos"></span>Estoque</a></li>
                    <li><a href="AdmCadastrarProduto.php"><span class="icons"><img src="../img/novo-produto.png" alt="ícone novo produto"></span>Cadastrar Produto</a></li>
                    <li><a href="AdmEditarProduto.php"><span class="icons"><img src="../img/editar-prod.png" alt="ícone editar produto"></span>Editar Produto</a></li>
                    <li><a href="AdmExcluirProduto.php"><span class="icons"><img src="../img/remover-produto.png" alt="ícone remover produto"></span>Excluir Estoque</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
                <?php if ($mensagem): ?>
                    <div class="mensagem-<?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
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
                            value="<?php echo htmlspecialchars($codigoProduto); ?>"
                            required
                        >
                        <button type="submit">Buscar</button>
                    </div>
                </form>

                <?php if (!empty($codigoProduto) && !empty($nomeProduto)): ?>
                    <form method="POST" action="" onsubmit="return confirmDelete(<?php echo htmlspecialchars($estoqueProduto); ?>);">
                        <div class="cliente">
                            <div class="colunas">
                                <div class="coluna">
                                    <label for="codigo">Código:</label>
                                    <input
                                        type="number"
                                        name="codigo"
                                        class="NomeCliente"
                                        placeholder="Código: "
                                        autocomplete="off"
                                        max="999"
                                        min="1"
                                        maxlength="3"
                                        value="<?php echo htmlspecialchars($codigoProduto); ?>"
                                        readonly
                                        style="color: #4d4848; cursor: not-allowed;"
                                    >
                                    <label for="estoque-atual">Estoque atual:</label>
                                    <input
                                        type="number"
                                        name="estoque-atual"
                                        class="Email"
                                        placeholder="Estoque"
                                        value="<?php echo htmlspecialchars($estoqueProduto); ?>"
                                        min="0"
                                        autocomplete="off"
                                        max="<?php echo htmlspecialchars($estoqueProduto); ?>"
                                        readonly
                                        style="color: #4d4848; cursor: not-allowed;"
                                        required
                                    >
                                </div>

                                <div class="coluna">
                                    <label for="nome">Nome do Produto:</label>
                                    <input
                                        type="text"
                                        name="nome"
                                        class="Telefone"
                                        placeholder="Nome do produto"
                                        autocomplete="off"
                                        value="<?php echo htmlspecialchars($nomeProduto); ?>"
                                        disabled
                                        style="color: #4d4848; cursor: not-allowed;"
                                    >
                                    <label for="subtrair">Subtrair:</label>
                                    <input
                                        type="number"
                                        name="estoque"
                                        id="subtrairInput"
                                        class="Email"
                                        placeholder="Quantidade para subtrair"
                                        value=""
                                        min="1"
                                        autocomplete="off"
                                        max="<?php echo htmlspecialchars($estoqueProduto); ?>"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="botoes">
                            <div>
                                <a href="AdmEstoque.php">
                                    <button class="voltar" id="volt" type="button">Voltar</button>
                                </a>
                            </div>
                            <div>
                                <button id="cade" type="submit" name="subtrair">
                                    <img src="../img/lata-de-lixo-preta.png" alt="Ícone de lixeira para subtrair estoque">
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