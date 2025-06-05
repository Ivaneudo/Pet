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

        // Consulta para buscar o funcionário pelo CPF nas três tabelas
        $sql = "SELECT cpf, nome, 'administrador' AS cargo FROM adm WHERE cpf = ? 
                UNION 
                SELECT cpf, nome, 'repositor' AS cargo FROM repositor WHERE cpf = ? 
                UNION 
                SELECT cpf, nome, 'secretaria' AS cargo FROM secretaria WHERE cpf = ? 
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $cpfPesquisado, $cpfPesquisado, $cpfPesquisado);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    else {
        // Consulta para mostrar todos os funcionários
        $sql = "SELECT cpf, nome, 'Administrador' AS cargo FROM adm 
            UNION 
            SELECT cpf, nome, 'Repositor' AS cargo FROM repositor 
            UNION 
            SELECT cpf, nome, 'Secretaria' AS cargo FROM secretaria 
            ORDER BY nome ASC";
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
    <title>Funcionários</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/repositor.css">
    <link rel="stylesheet" href="../css/AdmFuncionarios.css">
    <link rel="stylesheet" href="../css/responsivo.css">
    <script src="../js/mascara.js" defer></script>
    <script src="../js/excluirFuncionario.js" defer></script>
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
                    <li><a href="Adm.php">Menu</a></li>
                    <li><a href="AdmFuncionarios.php">Funcionários</a></li>
                    <li><a href="AdmNovoFuncionario.php">Cadastrar funcionário</a></li>
                    <li><a href="AdmEditarFuncionario.php">Editar funcionário</a></li>
                    <li><a href="AdmVendas.php">Vendas</a></li>
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
                            placeholder="Digite o CPF do funcionário: "
                            autocomplete="off"
                            maxlength=14
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
                                <th>Cargo</th>
                                <th>Demitir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                                        <td class="ver"><?php echo htmlspecialchars($row['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($row['cargo']); ?></td>
                                        <td class="demitir">
                                            <a href="#" onclick="excluirFuncionario('<?php echo $row['cpf']; ?>')">
                                                <img src="../img/lata-de-lixo.png" alt="Remover">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">Nenhum funcionário encontrado.</td>
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