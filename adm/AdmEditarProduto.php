<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é um adm
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("Location: ../entrada/Entrar.php"); // Redireciona se não for adm
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

        // Se o botão de modificar foi clicado
        if (isset($_POST['modificar'])) {
            $nomeProduto = trim($_POST['nome']);
            // Substitui vírgula por ponto para ponto decimal, se enviado com vírgula
            $precoProduto = floatval(str_replace(',', '.', $_POST['preco']));
            $estoqueProduto = intval($_POST['estoque']);
            $codigoProduto = trim($_POST['codigo']); // Garante que o código está definido

            // Atualiza o produto no banco de dados
            $sqlUpdate = "UPDATE produto SET nome_produto = ?, preco = ?, estoque = ? WHERE id_produto = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sdii", $nomeProduto, $precoProduto, $estoqueProduto, $codigoProduto);

            if ($stmtUpdate->execute()) {
                $mensagem = "Produto atualizado com sucesso!";
                // Limpa os campos após a atualização
                $codigoProduto = '';
                $nomeProduto = '';
                $precoProduto = '';
                $estoqueProduto = '';
            } else {
                $mensagem = "Erro ao atualizar o produto.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Produto</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/repositor.css" />
    <link rel="stylesheet" href="../css/responsivo.css">
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem-vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Adm.php" id="selecionado">Menu</a></li>
                    <li><a href="AdmEstoque.php" id="selecionado">Estoque</a></li>
                    <li><a href="AdmCadastrarProduto.php">Cadastrar Produto</a></li>
                    <li><a href="AdmEditarProduto.php">Editar Produto</a></li>
                    <li><a href="AdmExcluirProduto.php">Excluir Estoque</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
            <?php if ($mensagem): ?>
                    <strong><p style="color: <?php echo strpos($mensagem, 'sucesso') !== false ? '#008B00' : '#CD0000'; ?>">
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
                            value="<?php echo htmlspecialchars($codigoProduto); ?>"
                            required
                        >
                        <button type="submit">Buscar</button>
                    </div>
                </form>

                <?php if (!empty($codigoProduto) && !empty($nomeProduto)): ?>
                    <form method="POST" action="">
                        <div class="cliente">
                            <p>Editar Produtos:</p>
                            <div class="colunas">

                                <div class="coluna">
                                    <input
                                        type="text"
                                        name="codigo"
                                        class="NomeCliente"
                                        placeholder="Código: "
                                        value="<?php echo htmlspecialchars($codigoProduto); ?>"
                                        readonly
                                        autocomplete=off 
                                        style="color: #6c6b6b; cursor: not-allowed;"
                                    >
                                    <input
                                        type="text"
                                        id="preco"
                                        name="preco"
                                        placeholder="Preço"
                                        autocomplete=off 
                                        value="<?php echo htmlspecialchars(number_format($precoProduto ?? 0, 2, ',', '.')); ?>"
                                    >
                                </div>

                                <div class="coluna">
                                    <input
                                        type="text"
                                        name="nome"
                                        class="Telefone"
                                        placeholder="Nome do produto"
                                        autocomplete=off 
                                        value="<?php echo htmlspecialchars($nomeProduto); ?>"
                                        required
                                    >
                                    <input
                                        type="number"
                                        name="estoque"
                                        class="Email"
                                        placeholder="Estoque"
                                        autocomplete=off 
                                        value="<?php echo htmlspecialchars($estoqueProduto); ?>"
                                        min="0"
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
                                <button name="modificar" id="cade" type="submit">Modificar</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html> 