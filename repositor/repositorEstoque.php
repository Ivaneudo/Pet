<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é um repositor
if ($_SESSION['tipo_usuario'] !== 'repositor') {
    header("Location: ../entrada/Entrar.php"); // Redireciona se não for repositor
    exit();
}

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];

// Inicializa a variável para o ID do produto pesquisado
$idProdutoPesquisado = '';

// Verifica se o formulário de pesquisa foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produto'])) {
    $idProdutoPesquisado = trim($_POST['id_produto']);

    // Se o campo de pesquisa estiver vazio, busca todos os produtos
    if ($idProdutoPesquisado === '') {
        $sql = "SELECT id_produto, nome_produto, estoque FROM produto";
    } else {
        // Consulta para buscar o produto pelo ID
        $sql = "SELECT id_produto, nome_produto, estoque FROM produto WHERE id_produto = ? LIMIT 1";
    }

    $stmt = $conn->prepare($sql);
    if ($idProdutoPesquisado !== '') {
        $stmt->bind_param("i", $idProdutoPesquisado);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Consulta para mostrar todos os produtos
    $sql = "SELECT id_produto, nome_produto, estoque FROM produto";
    $result = $conn->query($sql);
}

if ($result === false) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/repositor.css">
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
        <div class="estoque">
            <div class="esto">
                <form method="POST" action="" class='formnovo'>
                    <div class="pesquisa">
                        <div class="campo">
                            <input
                                type="text"
                                name="id_produto"
                                id="id_produto"
                                placeholder="Digite o ID do produto: "
                                autocomplete="off"
                                value="<?php echo htmlspecialchars($idProdutoPesquisado); ?>">
                                
                            <button type="submit" style="background: none; border: none; cursor: pointer;">
                                <img src="../img/search-svgrepo-com.svg" alt="">
                            </button>
                        </div>
                    </div>
                    <div class="Navbar">
                        <nav>
                            <ul>
                                <li><a href="repositor.php">Voltar</a></li>
                            </ul>
                        </nav>
                    </div>
                </form>
                <div class="produtos">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th>Estoque</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_produto']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nome_produto']); ?></td>
                                        <td><?php echo htmlspecialchars($row['estoque']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">Nenhum produto encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>