<?php
    session_start();
    include('../funcoes/conexao.php');

    // ! Verifica qual o cargo do funcionário logado
    if ($_SESSION['tipo_usuario'] !== 'admin'){
        header("Location: ../entrada/Entrar.php"); // ! Redireciona se não for admin
        exit();
    }

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];

    // Inicializa variáveis para mensagens
    $mensagemSucesso = '';
    $mensagemErro = '';

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Captura os dados do formulário
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        $cargo = $_POST['cargo'] ?? '';

        // Validações básicas
        if (empty($nome) || empty($cpf) || empty($telefone) || empty($email) || empty($senha) || empty($cargo)) {
            $mensagemErro = "Por favor, preencha todos os campos.";
        } else {
            // Verificação do CPF em todas as tabelas
            $sqlCheckCpf = "SELECT COUNT(*) as total FROM (
                            SELECT cpf FROM adm WHERE cpf = ?
                            UNION ALL
                            SELECT cpf FROM secretaria WHERE cpf = ?
                            UNION ALL
                            SELECT cpf FROM repositor WHERE cpf = ?
                            ) as cpf_unico";
            
            $stmtCheckCpf = $conn->prepare($sqlCheckCpf);
            if (!$stmtCheckCpf) {
                $mensagemErro = "Erro ao preparar a verificação de CPF: " . $conn->error;
            } else {
                $stmtCheckCpf->bind_param("sss", $cpf, $cpf, $cpf);
                $stmtCheckCpf->execute();
                $result = $stmtCheckCpf->get_result();
                $row = $result->fetch_assoc();
                $totalCpfCount = $row['total'];
                $stmtCheckCpf->close();

                if ($totalCpfCount > 0) {
                    $mensagemErro = "Este usuário já existe.";
                } else {
                    // Prepara a consulta de inserção com base no cargo selecionado
                    if ($cargo === 'secretaria') {
                        $sql = "INSERT INTO secretaria (nome, cpf, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
                    } elseif ($cargo === 'repositor') {
                        $sql = "INSERT INTO repositor (nome, cpf, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
                    } elseif ($cargo === 'adm') {
                        $sql = "INSERT INTO adm (nome, cpf, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
                    } else {
                        $mensagemErro = "Cargo inválido.";
                    }

                    if (empty($mensagemErro)) {
                        // Prepara e executa a consulta
                        $stmt = $conn->prepare($sql);
                        if ($stmt) {
                            $stmt->bind_param("sssss", $nome, $cpf, $telefone, $email, $senha);
                            if ($stmt->execute()) {
                                $mensagemSucesso = "Funcionário cadastrado com sucesso.";
                                // Limpa os campos do formulário
                                $nome = $cpf = $telefone = $email = $senha = $cargo = '';
                            } else {
                                $mensagemErro = "Erro ao cadastrar funcionário: " . $stmt->error;
                            }
                            $stmt->close();
                        } else {
                            $mensagemErro = "Erro ao preparar a consulta: " . $conn->error;
                        }
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastrar Funcionários</title>
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
                    <li><a href="Adm.php"><span class="icons"><img src="../img/menu.png" alt=""></span>Menu</a></li>
                    <li><a href="AdmFuncionarios.php"><span class="icons"><img src="../img/funci.png" alt=""></span>Funcionários</a></li>
                    <li><a href="AdmNovoFuncionario.php"><span class="icons"><img src="../img/novo-funci.png" alt=""></span>Cadastrar funcionário</a></li>
                    <li><a href="AdmEditarFuncionario.php"><span class="icons"><img src="../img/editarPessoa.png" alt=""></span>Editar funcionário</a></li>
                    <li><a href="AdmVendas.php"><span class="icons"><img src="../img/vendas.png" alt=""></span>Vendas</a></li>
            </ul>
        </nav>
    </div>

        <div class="cadastrar">
            <div class="cadastro">
                <?php if (!empty($mensagemSucesso)): ?>
                    <div class="mensagem-sucesso"><?php echo htmlspecialchars($mensagemSucesso); ?></div>
                <?php elseif (!empty($mensagemErro)): ?>
                    <div class="mensagem-erro"><?php echo htmlspecialchars($mensagemErro); ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="cliente">
                        <p>Novo Funcionário:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input 
                                    type="text" 
                                    name="nome" 
                                    class="NomeCliente" 
                                    placeholder="Digite o nome do funcionário: " 
                                    value="<?php echo isset($nome) ? htmlspecialchars($nome) : ''; ?>" 
                                    autocomplete="off" 
                                    required />

                                <input 
                                    type="text" 
                                    name="cpf" 
                                    id="cpf" 
                                    placeholder="Digite o CPF do funcionário: " 
                                    value="<?php echo isset($cpf) ? htmlspecialchars($cpf): ''; ?>" 
                                    maxlength="14" 
                                    autocomplete="off" 
                                    required />

                                <input 
                                    type="password" 
                                    name="senha" 
                                    id="senha" 
                                    placeholder="Digite a senha do funcionário: " 
                                    autocomplete="off" 
                                    required />
                            </div>
                            <div class="coluna">
                                <input 
                                type="text" 
                                name="telefone" 
                                class="Telefone" 
                                maxlength="14" 
                                placeholder="Digite o telefone do funcionário" 
                                value="<?php echo isset($telefone) ? htmlspecialchars($telefone) : ''; ?>" 
                                autocomplete="off" 
                                required />

                                <input 
                                type="email" 
                                name="email" 
                                class="Email" 
                                placeholder="Digite o e-mail do funcionário: " 
                                value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                autocomplete="off" 
                                required />

                                <select name="cargo" required>
                                    <option value="" disabled <?php echo !isset($cargo) ? 'selected' : ''; ?>>Cargo do Funcionário</option>

                                    <option value="secretaria" <?php echo (isset($cargo) && $cargo === "secretaria") ? 'selected' : ''; ?>>Secretaria</option>

                                    <option value="repositor" <?php echo (isset($cargo) && $cargo === "repositor") ? 'selected' : ''; ?>>Repositor</option>
                                    
                                    <option value="adm" <?php echo (isset($cargo) && $cargo === "adm") ? 'selected' : ''; ?>>Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="botoes">
                        <div>
                            <a href="AdmFuncionarios.php" class="voltar">
                                <button type="button" class="voltar">Voltar</button>
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