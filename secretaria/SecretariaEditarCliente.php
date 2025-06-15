<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica qual o cargo do funcionário logado
    if ($_SESSION['tipo_usuario'] !== 'secretaria') {
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    $nomeFuncionario = $_SESSION['usuario'];
    $cpfCliente = '';
    $cliente = null;
    $mensagem = '';
    $classeMensagem = '';

    // Se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Se o botão de buscar foi clicado
        if (isset($_POST['buscar']) && isset($_POST['cpf']) && !empty(trim($_POST['cpf']))) {
            $cpfCliente = trim($_POST['cpf']);
            $cliente = buscarCliente($conn, $cpfCliente);

            if (!$cliente) {
                $mensagem = "Cliente não encontrado.";
                $classeMensagem = 'erro';
            }
        }

        // Se o botão de modificar foi clicado
        if (isset($_POST['modificar']) && isset($_POST['cpfCliente'])) {
            $cpfCliente = trim($_POST['cpfCliente']);
            $nome = trim($_POST['nome']);
            $telefone = trim($_POST['telefone']);
            $email = trim($_POST['email']);

            // Atualiza os dados do cliente
            $sqlUpdateCliente = "UPDATE cliente SET nome = ?, telefone = ?, email = ? WHERE cpf = ?";
            $stmtUpdateCliente = $conn->prepare($sqlUpdateCliente);
            
            if ($stmtUpdateCliente) {
                $stmtUpdateCliente->bind_param("ssss", $nome, $telefone, $email, $cpfCliente);
                
                if ($stmtUpdateCliente->execute()) {
                    $_SESSION['message'] = "Informações atualizadas com sucesso!";
                    $classeMensagem = 'sucesso';
                    // Limpa as informações do cliente após a atualização
                    $cpfCliente = '';
                    $cliente = null; // Limpa o cliente
                } else {
                    $mensagem = "Erro ao atualizar cliente: " . $stmtUpdateCliente->error;
                    $classeMensagem = 'erro';
                }
            } else {
                $mensagem = "Erro na preparação da consulta: " . $conn->error;
                $classeMensagem = 'erro';
            }
        }
    }

    function buscarCliente($conn, $cpfCliente) {
        $sql = "SELECT nome, telefone, email, cpf FROM cliente WHERE cpf = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cpfCliente);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    if (isset($_SESSION['message'])) {
        $mensagem = $_SESSION['message'];
        $classeMensagem = 'sucesso';
        unset($_SESSION['message']);
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
    <link rel="stylesheet" href="../css/mensagem.css">
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
                    <li><a href="Secretaria.php"><span class="icons"><img src="../img/menu.png" alt=""></span>Menu</a></li>
                    <li><a href="SecretariaClientes.php"><span class="icons"><img src="../img/clientes.png" alt=""></span>Clientes</a></li>
                    <li><a href="SecretariaCadastrarCliente.php"><span class="icons"><img src="../img/cadastrar.png" alt=""></span>Cadastrar Cliente</a></li>
                    <li><a href="SecretariaEditarCliente.php"><span class="icons"><img src="../img/editarPessoa.png" alt=""></span>Editar Cliente</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
                <?php if($mensagem): ?>
                    <div class="mensagem-<?php echo $classeMensagem; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="pesquisa-cliente">
                        <h3>Editar Cliente:</h3>
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

                        <div class="colunas">
                            <div class="coluna">
                                <label for="nome">Nome:</label>
                                <input 
                                    type="text" 
                                    name="nome" 
                                    class="NomeCliente" 
                                    placeholder="Nome do cliente" 
                                    autocomplete="off" 
                                    value="<?php echo htmlspecialchars($cliente['nome']); ?>" 
                                    required
                                >
                                
                                <label for="telefone">Telefone:</label>
                                <input 
                                    type="text" 
                                    name="telefone" 
                                    class="Telefone" 
                                    placeholder="Telefone do cliente" 
                                    maxlength="14" 
                                    autocomplete="off" 
                                    value="<?php echo htmlspecialchars($cliente['telefone'] ?? ''); ?>" 
                                >
                            </div>
                            <div class="coluna">
                                <label for="cpf">CPF:</label>
                                <input 
                                    type="text" 
                                    name="cpf" 
                                    class="CPFCliente" 
                                    placeholder="CPF do cliente" 
                                    value="<?php echo htmlspecialchars($cpfCliente); ?>" 
                                    style="color: #6c6b6b; cursor: not-allowed;"
                                    disabled 
                                >

                                <label for="email">E-mail:</label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="Email" 
                                    placeholder="E-mail do cliente" 
                                    autocomplete="off" 
                                    value="<?php echo htmlspecialchars($cliente['email']); ?>" 
                                    required
                                >
                            </div>
                        </div>

                        <div class="botoes">
                            <div>
                                <a href="SecretariaClientes.php" style="text-decoration:none;">
                                    <button type="button" class="voltar">Voltar</button>
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