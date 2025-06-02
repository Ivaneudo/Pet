<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é uma secretaria
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];

    // Inicializa variáveis
    $cpfCliente = '';
    $cliente = null;
    $mensagem = '';

    // Se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Se o CPF do cliente foi enviado
        if (isset($_POST['cpf']) && !empty(trim($_POST['cpf']))) {
            $cpfCliente = trim($_POST['cpf']);
            $cliente = buscarCliente($conn, $cpfCliente);

            if (!$cliente) {
                $mensagem = "Cliente não encontrado.";
            }
        }

        // Se o botão de modificar foi clicado
        if (isset($_POST['modificar']) && $cliente) {
            $nome = trim($_POST['nome']);
            $telefone = trim($_POST['telefone']);
            $email = trim($_POST['email']);

            // Atualiza os dados do cliente
            $sqlUpdateCliente = "UPDATE cliente SET nome = ?, telefone = ?, email = ? WHERE cpf = ?";
            $stmtUpdateCliente = $conn->prepare($sqlUpdateCliente);
            $stmtUpdateCliente->bind_param("ssss", $nome, $telefone, $email, $cpfCliente);
            
            if ($stmtUpdateCliente->execute()) {
                // Armazena mensagem na sessão para exibir após redirecionamento
                $_SESSION['message'] = "Informações atualizadas com sucesso!";
                // Limpa os campos após a atualização
                $cpfCliente = '';
                $cliente = null;
            } else {
                die("Erro ao atualizar cliente: " . $stmtUpdateCliente->error);
            }
        }
    }

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

    // Mensagem de sucesso armazenada na sessão
    if (isset($_SESSION['message'])) {
        $mensagem = $_SESSION['message'];
        unset($_SESSION['message']); // Limpa a mensagem após exibi-la
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
    <link rel="stylesheet" href="../css/responsivo.css">
    <script src="../js/mascara.js" defer></script>
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
                    <li><a href="Secretaria.php">Menu</a></li>
                    <li><a href="SecretariaClientes.php">Clientes</a></li>
                    <li><a href="SecretariaCadastrarCliente.php">Cadastrar Cliente</a></li>
                    <li><a href="SecretariaEditarCliente.php">Editar Cliente</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
                <?php if($mensagem): ?>
                    <div class="alert alert-success" style="color: #008B00; font-weight: bold; text-align: left;">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="pesquisa-cliente">
                        <label for="cpf">Pesquisar CPF do Cliente:</label>
                        <input
                            type="text"
                            name="cpf"
                            id="cpf"
                            maxlength="14"
                            placeholder="Digite o CPF do cliente"
                            autocomplete="off" 
                            value="<?php echo htmlspecialchars($cpfCliente); ?>"
                            required
                        />
                        <button type="submit" name="buscar">Buscar</button>
                    </div>
                </form>

                <?php if ($cliente): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="cpfCliente" value="<?php echo htmlspecialchars($cpfCliente); ?>" />

                        <p><strong>Editar Cliente:</strong></p>
                        <div class="colunas">
                            <div class="coluna">
                                <p><strong>CPF:</strong></p>
                                <input
                                type="text" 
                                name="cpf_display" 
                                class="CPFCliente" 
                                placeholder="CPF do cliente" 
                                value="<?php echo htmlspecialchars($cpfCliente); ?>" 
                                style="color: #6c6b6b; cursor: not-allowed;"
                                disabled>
                                
                                <p><strong>Telefone:</strong></p>
                                <input type="text" 
                                name="telefone" 
                                class="Telefone" 
                                maxlength="14" 
                                placeholder="Telefone do cliente" 
                                autocomplete="off" 
                                value="<?php echo htmlspecialchars($cliente['telefone']); ?>" required>
                            </div>
                            <div class="coluna">
                                <p><strong>Nome:</strong></p>
                                <input 
                                type="text" 
                                name="nome" 
                                class="NomeCliente" 
                                placeholder="Nome do cliente" 
                                autocomplete="off" 
                                value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                                
                                <p><strong>E-mail:</strong></p>
                                <input 
                                type="email" 
                                name="email" 
                                class="Email" 
                                placeholder="E-mail do cliente" 
                                autocomplete="off" 
                                value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>