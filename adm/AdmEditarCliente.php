<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é um administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../entrada/Entrar.php");
    exit();
}

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];

// Captura o CPF do cliente da sessão ou do POST (caso de redirecionamento)
if (isset($_POST['cpfCliente'])) {
    $cpfCliente = $_POST['cpfCliente'];
    $_SESSION['cpf_cliente'] = $cpfCliente;
} elseif (isset($_SESSION['cpf_cliente'])) {
    $cpfCliente = $_SESSION['cpf_cliente'];
} else {
    die("CPF do cliente não informado.");
}

$cliente = null;

// Função para buscar dados do cliente
function buscarCliente($conn, $cpfCliente) {
    $sql = "SELECT * FROM cliente WHERE cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cpfCliente);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Busca dados iniciais do cliente
$cliente = buscarCliente($conn, $cpfCliente);
if (!$cliente) {
    die("Cliente não encontrado.");
}

// Mensagem de sucesso armazenada na sessão
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Limpa a mensagem após exibi-la
}

// Trata o envio do formulário para modificar os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar'])) {
    // Atualiza dados do cliente
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    $sqlUpdateCliente = "UPDATE cliente SET nome = ?, telefone = ?, email = ? WHERE cpf = ?";
    $stmtUpdateCliente = $conn->prepare($sqlUpdateCliente);
    $stmtUpdateCliente->bind_param("ssss", $nome, $telefone, $email, $cpfCliente);
    
    if ($stmtUpdateCliente->execute()) {
        // Armazena mensagem na sessão para exibir após redirecionamento
        $_SESSION['message'] = "Informações atualizadas com sucesso!";
        
        // Atualiza os dados na variável $cliente para mostrar os valores atualizados
        $cliente = buscarCliente($conn, $cpfCliente);
        
        // Redireciona para a mesma página para evitar reenvio do formulário
        header("Location: AdmEditarCliente.php");
        exit();
    } else {
        die("Erro ao atualizar cliente: " . $stmtUpdateCliente->error);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Cliente</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/caixaCadastro.css" />
    <link rel="stylesheet" href="../css/AdmFuncionarios.css" />
    <script src="../js/mascaraTelefone.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem-vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Adm.php">Menu</a></li>
                    <li><a href="AdmClientes.php">Clientes</a></li>
                    <li><a href="AdmCadastrarCliente.php">Cadastrar Cliente</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
                <?php if($message): ?>
                    <div class="alert alert-success" style="color: #008B00; font-weight: bold; text-align: left;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="hidden" name="cpfCliente" value="<?php echo htmlspecialchars($cpfCliente); ?>" />

                    <div class="cliente">
                        <p>Cliente:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <P for="cpf_display">CPF:</P>
                                <input type="text" name="cpf_display" class="CPFCliente" placeholder="CPF do cliente" value="<?php echo htmlspecialchars($cpfCliente); ?>" disabled style="color: #6c6b6b; cursor: not-allowed;">
                                
                                <P for="telefone">Telefone:</P>
                                <input type="text" name="telefone" class="Telefone" maxlength="14" placeholder="Telefone do cliente" autocomplete=off value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
                            </div>
                            <div class="coluna">
                                <P for="nome">Nome:</P>
                                <input type="text" name="nome" class="NomeCliente" placeholder="Nome do cliente" autocomplete=off value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                                
                                <P for="email">E-mail:</P>
                                <input type="email" name="email" class="Email" placeholder="E-mail do cliente" autocomplete=off value="<?php echo htmlspecialchars($cliente['email']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="botoes">
                        <div>
                            <a href="AdmClientes.php" style="text-decoration:none;">
                                <button type="button" class="voltar" style="color: black;">Voltar</button>
                            </a>
                        </div>
                        <div>
                            <button type="submit" name="modificar" class="cade">Modificar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>