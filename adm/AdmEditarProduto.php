<?php
    session_start();
    include('../funcoes/conexao.php');

    // ! Verifica qual o cargo do funcionário logado
    if ($_SESSION['tipo_usuario'] !== 'admin'){
        header("Location: ../entrada/Entrar.php"); // ! Redireciona se não for admin
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
    <link rel="stylesheet" href="../css/mensagem.css">
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
                    <li><a href="Adm.php" id="selecionado"><span class="icons"><img src="../img/menu.png" alt=""></span>Menu</a></li>
                    <li><a href="AdmEstoque.php" id="selecionado"><span class="icons"><img src="../img/produtos.png" alt=""></span>Estoque</a></li>
                    <li><a href="AdmCadastrarProduto.php"><span class="icons"><img src="../img/novo-produto.png" alt=""></span>Cadastrar Produto</a></li>
                    <li><a href="AdmEditarProduto.php"><span class="icons"><img src="../img/editar-prod.png" alt=""></span>Editar Produto</a></li>
                    <li><a href="AdmExcluirProduto.php"><span class="icons"><img src="../img/remover-produto.png" alt=""></span>Excluir Estoque</a></li>
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
                                    <label for="codigo">Código:</label>
                                    <input
                                        type="number"
                                        name="codigo"
                                        class="NomeCliente"
                                        placeholder="Código: "
                                        autocomplete=off 
                                        max="999"
                                        min="1"
                                        maxlength="3"
                                        value="<?php echo htmlspecialchars($codigoProduto); ?>"
                                        readonly
                                        style="color: #4d4848; cursor: not-allowed;"
                                    >
                                    <label for="preco">Preço:</label>
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
                                    <label for="nome">Nome do Produto:</label>
                                    <input
                                        type="text"
                                        name="nome"
                                        class="Telefone"
                                        placeholder="Nome do produto"
                                        autocomplete=off 
                                        value="<?php echo htmlspecialchars($nomeProduto); ?>"
                                        required
                                    >
                                    <label for="estoque">Estoque</label>
                                    <input
                                        type="number"
                                        name="estoque"
                                        class="Email"
                                        placeholder="Estoque"
                                        autocomplete=off 
                                        value="<?php echo htmlspecialchars($estoqueProduto); ?>"
                                        min="1"
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