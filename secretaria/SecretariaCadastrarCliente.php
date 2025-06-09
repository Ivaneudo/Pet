<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verificação de secretaria
    if ($_SESSION['tipo_usuario'] !== 'secretaria') {
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    $nomeFuncionario = $_SESSION['usuario'];
    $mensagem = '';
    $classeMensagem = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitização dos dados (mantendo a formatação do CPF)
        $clienteNome = trim($_POST['nome'] ?? '');
        $clienteCpf = trim($_POST['cpf'] ?? '');
        $clienteTelefone = trim($_POST['Telefone'] ?? '');
        $clienteEmail = trim($_POST['email'] ?? '');

        // Validações básicas
        if (empty($clienteNome) || empty($clienteCpf) || empty($clienteTelefone) || empty($clienteEmail)) {
            $mensagem = "Por favor, preencha todos os campos obrigatórios.";
            $classeMensagem = 'erro';
        } else {
            // Verificação do CPF (com formatação)
            $sqlCheckCpf = "SELECT COUNT(*) as total FROM (
                            SELECT cpf FROM adm WHERE cpf = ?
                            UNION ALL
                            SELECT cpf FROM cliente WHERE cpf = ?
                            UNION ALL
                            SELECT cpf FROM secretaria WHERE cpf = ?
                            UNION ALL
                            SELECT cpf FROM repositor WHERE cpf = ?
                            ) as cpf_unico";
            
            $stmtCheckCpf = $conn->prepare($sqlCheckCpf);
            $stmtCheckCpf->bind_param("ssss", $clienteCpf, $clienteCpf, $clienteCpf, $clienteCpf);
            $stmtCheckCpf->execute();
            $result = $stmtCheckCpf->get_result();
            $row = $result->fetch_assoc();
            $totalCpfCount = $row['total'];
            $stmtCheckCpf->close();

            if ($totalCpfCount > 0) {
                $mensagem = "Este usuário já existe.";
                $classeMensagem = 'erro';
            } else {
                // Insere com CPF formatado
                $sqlInsertCliente = "INSERT INTO cliente (nome, cpf, telefone, email) VALUES (?, ?, ?, ?)";
                $stmtInsert = $conn->prepare($sqlInsertCliente);
                $stmtInsert->bind_param("ssss", $clienteNome, $clienteCpf, $clienteTelefone, $clienteEmail);

                if ($stmtInsert->execute()) {
                    $mensagem = "Cliente cadastrado com sucesso!";
                    $classeMensagem = 'sucesso';
                    // Limpa os campos
                    $clienteNome = $clienteCpf = $clienteTelefone = $clienteEmail = '';
                } else {
                    $mensagem = "Erro ao cadastrar cliente: " . $conn->error;
                    $classeMensagem = 'erro';
                }
                $stmtInsert->close();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastrar Clientes</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/caixaCadastro.css" />
    <link rel="stylesheet" href="../css/responsivo.css">
    <link rel="stylesheet" href="../css/mensagem.css">
    <script src="../js/mascara.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="Logo Pethop" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem-vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Secretaria.php"><span class="icons"><img src="../img/menu.png" alt=""></span>Menu</a></li>
                    <li><a href="SecretariaClientes.php"><span class="icons"><img src="../img/clientes.png" alt=""></span>Clientes</a></li>
                    <li><a href="SecretariaCadastrarCliente.php"><span class="icons"><img src="../img/cadastrar.png" alt=""></span>Cadastrar Cliente</a></li>
                    <li><a href="SecretariaEditarCliente.php"><span class="icons"><img src="../img/editarPessoa.png" alt=""></span>Editar Cliente</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
                <?php if ($mensagem): ?>
                    <div class="mensagem-<?php echo $classeMensagem; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="cliente">
                        <p>Cliente:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input 
                                    type="text" 
                                    name="nome" 
                                    class="NomeCliente" 
                                    placeholder="Nome do cliente: " 
                                    autocomplete=off 
                                    value="<?php echo htmlspecialchars($clienteNome ?? ''); ?>" 
                                    required>

                                <input 
                                    type="text" 
                                    id="cpf" 
                                    name="cpf" 
                                    maxlength="14" 
                                    placeholder="Digite o CPF do cliente: " 
                                    autocomplete=off 
                                    value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>" 
                                    required>
                            </div>
                            <div class="coluna">
                                <input 
                                    type="text" 
                                    name="Telefone" 
                                    class="Telefone" 
                                    maxlength="14" 
                                    placeholder="Digite o telefone do cliente" 
                                    autocomplete=off 
                                    value="<?php echo htmlspecialchars($clienteTelefone ?? ''); ?>" 
                                    required>

                                <input 
                                    type="email" 
                                    name="email" 
                                    class="Email" 
                                    placeholder="Digite o e-mail do cliente: " 
                                    autocomplete=off 
                                    value="<?php echo htmlspecialchars($clienteEmail ?? ''); ?>" 
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="botoes">
                        <div>
                            <a href="SecretariaClientes.php">
                                <button type="button" class="voltar" id="volt">Voltar</button>
                            </a>
                        </div>
                        <div>
                            <button type="submit" id="cade">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>