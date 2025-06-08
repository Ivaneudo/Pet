<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é um administrador
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    // Guarda o nome do funcionário
    $nomeFuncionario = $_SESSION['usuario'];

    $cpfPesquisado = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cpf'])) {
        $cpfPesquisado = trim($_POST['cpf']);
        // Redireciona para a tela de edição de cliente
        header("Location: AdmEditarCliente.php?cpf=" . urlencode($cpfPesquisado));
        exit();
    } else {
        // Consulta para mostrar todos os clientes
        $sql = "SELECT cpf, nome FROM cliente ORDER BY nome ASC";
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
    <title>Clientes</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/repositor.css">
    <link rel="stylesheet" href="../css/AdmFuncionarios.css">
    <link rel="stylesheet" href="../css/responsivo.css">
    <script src="../js/mascara.js" defer></script>
    <script src="../js/excluirCliente.js" defer></script>
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
                    <li><a href="Adm.php"><span class="icons"><img src="../img/menu.png" alt=""></span>Menu</a></li>
                    <li><a href="AdmClientes.php"><span class="icons"><img src="../img/clientes.png" alt=""></span>Clientes</a></li>
                    <li><a href="AdmCadastrarCliente.php"><span class="icons"><img src="../img/novo-funci.png" alt=""></span>Cadastrar Cliente</a></li>
                    <li><a href="AdmEditarCliente.php"><span class="icons"><img src="../img/editarPessoa.png" alt=""></span>Editar Cliente</a></li>
                </ul>
            </nav>
        </div>
        <div class="estoque">
            <div class="esto">
                <form method="POST" action="">
                    <div class="pesquisa">
                        <div class="campo">
                            <input
                            type="text"
                            name="cpf"
                            id="cpf"
                            placeholder="Digite o cpf do cliente: "
                            autocomplete="off"
                            value="<?php echo htmlspecialchars($cpfPesquisado); ?>">
                            <button type="submit" style="background: none; border: none; cursor: pointer;">
                                <img src="../img/search-svgrepo-com.svg" alt="">
                            </button>
                        </div>
                    </div>
                </form>
                <div class="produtos">
                    <table>
                        <thead>
                            <tr>
                                <th>CPF</th>
                                <th>Nome</th>
                                <th>Remover</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($row['cpf']); ?>
                                        </td>
                                        <td class="ver">
                                            <?php echo htmlspecialchars($row['nome']); ?>
                                        </td>
                                        <td class="demitir">
                                            <a href="#" onclick="confirmarExclusao('<?php echo $row['cpf']; ?>', 'AdmClientes.php')">
                                                <img src="../img/lata-de-lixo.png" alt="Remover">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">Nenhum cliente encontrado.</td>
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
