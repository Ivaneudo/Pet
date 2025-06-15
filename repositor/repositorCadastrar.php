<?php
    session_start();
    include('../funcoes/conexao.php');

    // ! Verifica qual o cargo do funcionário logado
    if ($_SESSION['tipo_usuario'] !== 'repositor') {
        header("Location: ../entrada/Entrar.php"); // ! Redireciona se não for admin
        exit();
    }

    $nomeFuncionario = $_SESSION['usuario'];

    $mensagem = '';
    $classeMensagem = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // ! Guarda os dados do formulário
        $nomeProduto = trim($_POST['nome']);
        $precoProduto = floatval(str_replace(',', '.', $_POST['preco']));
        $estoqueProduto = intval($_POST['estoque']);
        $tamanhoProduto = trim($_POST['tamanho']);

        // ! Verifica se todos os campos estão preenchidos
        if (!empty($nomeProduto) && $precoProduto >= 0 && $estoqueProduto >= 0 && !empty($tamanhoProduto)) {
            // ! Insere o novo produto no banco de dados
            $sqlInsert = "INSERT INTO produto (nome_produto, preco, estoque, tamanho) VALUES (?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("sdis", $nomeProduto, $precoProduto, $estoqueProduto, $tamanhoProduto);

            if ($stmtInsert->execute()) {
                $mensagem = "Produto cadastrado com sucesso!";
                $classeMensagem = 'sucesso';
            } else {
                $mensagem = "Erro ao cadastrar o produto.";
                $classeMensagem = 'erro';
            }
        } else {
            $mensagem = "Por favor, preencha todos os campos corretamente.";
            $classeMensagem = 'erro';
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
    <link rel="stylesheet" href="../css/mensagem.css">
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
        <div class="cadastrar">
            <div class="cadastro" id="repositor">

                <?php if ($mensagem): ?>
                    <div class="mensagem-<?php echo $classeMensagem; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="cliente">
                        <h3 style="margin-bottom: 2.3rem;">Cadastrar Produtos:</h3>
                        <div class="colunas">
                            <div class="coluna">
                                <label for="nome">Nome do Produto:</label>
                                <input
                                    type="text"
                                    name="nome"
                                    class="Telefone"
                                    placeholder="Nome do produto"
                                    autocomplete="off" 
                                    required
                                >

                                <label for="preco">Preço:</label>
                                <input
                                    type="text"
                                    name="preco"
                                    id="preco"
                                    placeholder="Preço"
                                    autocomplete="off" 
                                    required
                                >
                            </div>
                            <div class="coluna">
                                <label for="tamanho">Tamanho:</label>
                                <input
                                    type="text"
                                    name="tamanho"
                                    class="Tamanho"
                                    placeholder="Tamanho do produto"
                                    autocomplete="off" 
                                    required
                                >

                                <label for="estoque">Estoque:</label>
                                <input
                                    type="number"
                                    name="estoque"
                                    class="Email"
                                    placeholder="Estoque"
                                    min="1"
                                    autocomplete="off" 
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
                            <button id="cade" type="submit">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>