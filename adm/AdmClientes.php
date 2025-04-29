<?php
session_start();
include('../entrada/conexao.php');

// Verifica se o usuário é um administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../entrada/Entrar.php"); // Redireciona se não for admin
    exit();
}

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];

// Consulta para Obter Clientes
$sql = "SELECT cpf, nome FROM cliente";
$result = $conn->query($sql);

// Verifica se a consulta foi bem-sucedida
if ($result === false) {
    die("Erro na consulta: " . $conn->error); // Encerra e mostra o erro
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/repositor.css">
    <link rel="stylesheet" href="../css/AdmFuncionarios.css">
    <script src="../js/confirmExclusao.js" defer></script>
    <script src="../js/buscarClientes.js"></script>
    <script src="../js/mascara.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../entrada/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="AdmNovoFuncionario.php">Novo funcionário</a></li>
                    <li><a href="AdmFuncionarios.php">Funcionários</a></li>
                    <li><a href="AdmClientes.php">Clientes</a></li>
                </ul>
            </nav>
        </div>
        <div class="estoque">
            <div class="esto">
                <div class="pesquisa">
                    <div class="campo">
                        <input
                        type="text"
                        id="cpf"
                        placeholder="Digite o CPF do cliente: "
                        onkeyup="buscarCliente()">
                        <img src="../img/search-svgrepo-com.svg" alt="">
                    </div>
                    <div id="resultado-nome"></div> <!-- Exibe o nome do cliente encontrado -->
                </div>
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
                                        <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                                        <td class="ver"><?php echo htmlspecialchars($row['nome']); ?></td>
                                        <td class="demitir">
                                            <a href="#" onclick="confirmarExclusao('<?php echo $row['cpf']; ?>')">
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