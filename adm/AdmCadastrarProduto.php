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
    $mensagem = '';

    // Se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Captura os dados do formulário
        $codigoProduto = trim($_POST['codigo']);
        $nomeProduto = trim($_POST['nome']);
        $precoProduto = floatval(str_replace(',', '.', $_POST['preco'])); // Converte para float
        $estoqueProduto = intval($_POST['estoque']);
        $tamanhoProduto = trim($_POST['tamanho']); // Captura o tamanho do produto

        // Verifica se todos os campos estão preenchidos
        if (!empty($codigoProduto) && !empty($nomeProduto) && $precoProduto >= 0 && $estoqueProduto >= 0 && !empty($tamanhoProduto)) {
            // Insere o novo produto no banco de dados
            $sqlInsert = "INSERT INTO produto (id_produto, nome_produto, preco, estoque, tamanho) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("isdis", $codigoProduto, $nomeProduto, $precoProduto, $estoqueProduto, $tamanhoProduto);

            if ($stmtInsert->execute()) {
                $mensagem = "Produto cadastrado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar o produto.";
            }
        } else {
            $mensagem = "Por favor, preencha todos os campos corretamente.";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar produto</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/repositor.css">
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
                <a href="../funcoes/logout.php"><p>sair</p></a>
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
                    <strong><p style="color: <?php echo (strpos($mensagem, 'sucesso') !== false ? '#008B00' : '#CD0000'); ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </p></strong>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="cliente">
                        <p>Cadastrar Produtos:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input
                                    type="text"
                                    name="codigo"
                                    class="NomeCliente"
                                    placeholder="Código: "
                                    autocomplete=off 
                                    required
                                >
                                <input
                                    type="text"
                                    name="preco"
                                    id="cpf"
                                    placeholder="Preço"
                                    autocomplete=off 
                                    required
                                >
                                <input
                                    type="text"
                                    name="tamanho"
                                    class="Tamanho"
                                    placeholder="Tamanho do produto"
                                    autocomplete=off 
                                    required
                                >
                            </div>
                            <div class="coluna">
                                <input
                                    type="text"
                                    name="nome"
                                    class="Telefone"
                                    placeholder="Nome do produto"
                                    autocomplete=off 
                                    required
                                >
                                <input
                                    type="number"
                                    name="estoque"
                                    class="Email"
                                    placeholder="Estoque"
                                    min="0"
                                    autocomplete=off 
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
                            <button id="cade" type="submit">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>