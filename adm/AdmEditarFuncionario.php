<?php
    session_start();
    include('../funcoes/conexao.php');

    // ! Verifica qual o cargo do funcionário logado
    if ($_SESSION['tipo_usuario'] !== 'admin') {
        header("Location: ../entrada/Entrar.php"); // ! Redireciona se não for admin
        exit();
    }

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];

    // Inicializa variáveis
    $cpfFuncionario = '';
    $funcionario = null;
    $mensagem = '';
    $classeMensagem = ''; // Adiciona a variável para a classe da mensagem

    // Se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Se o CPF do funcionário foi enviado
        if (isset($_POST['cpf']) && !empty(trim($_POST['cpf']))) {
            $cpfFuncionario = trim($_POST['cpf']);

            // Verifica em cada tabela se o funcionário existe
            $tables = ['adm', 'secretaria', 'repositor'];
            foreach ($tables as $table) {
                $sql = "SELECT * FROM $table WHERE cpf = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                
                if ($stmt === false) {
                    die("Erro ao preparar a consulta: " . $conn->error);
                }

                $stmt->bind_param("s", $cpfFuncionario);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $funcionario = $result->fetch_assoc();
                    $funcionario['cargo'] = $table; // Define o cargo com base na tabela
                    break; // Sai do loop se encontrar o funcionário
                }
            }

            if (!$funcionario) {
                $mensagem = "Funcionário não encontrado.";
                $classeMensagem = 'erro'; // Define a classe de erro
            }
        }

        // Se o botão de modificar foi clicado
        if (isset($_POST['modificar']) && $funcionario) {
            $nome = trim($_POST['nome']);
            $telefone = trim($_POST['telefone']);
            $email = trim($_POST['email']);
            $senha = trim($_POST['senha']);
            $cargo = $funcionario['cargo']; // O cargo é inalterável

            // Atualiza os dados do funcionário na tabela correspondente
            $sqlUpdate = "UPDATE $cargo SET nome = ?, telefone = ?, email = ?, senha = ? WHERE cpf = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            
            if ($stmtUpdate === false) {
                die("Erro ao preparar a atualização: " . $conn->error);
            }

            $stmtUpdate->bind_param("sssss", $nome, $telefone, $email, $senha, $cpfFuncionario);

            if ($stmtUpdate->execute()) {
                $mensagem = "Funcionário atualizado com sucesso!";
                $classeMensagem = 'sucesso'; // Define a classe de sucesso
                // Limpa os campos após a atualização
                $cpfFuncionario = '';
                $funcionario = null;
            } else {
                $mensagem = "Erro ao atualizar o funcionário: " . $stmtUpdate->error;
                $classeMensagem = 'erro'; // Define a classe de erro
            }
            $stmtUpdate->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Funcionário</title>
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
                <?php if ($mensagem): ?>
                    <div class="mensagem-<?php echo $classeMensagem; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="pesquisa-funcionario">
                        <h3>Editar Funcionário:</h3>
                        <label for="cpf">Pesquisar CPF do Funcionário:</label>
                        <input
                            type="text"
                            name="cpf"
                            id="cpf"
                            maxlength="14"
                            placeholder="Digite o CPF do funcionário"
                            autocomplete="off" 
                            value="<?php echo htmlspecialchars($cpfFuncionario); ?>"
                            required
                        />
                        <button type="submit" name="buscar">Buscar</button>
                    </div>
                </form>

                <?php if ($funcionario): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="cpf" value="<?php echo htmlspecialchars($cpfFuncionario); ?>" autocomplete="off">

                        <div class="colunas">
                            <div class="coluna">
                                <label for="nome">Nome:</label>
                                <input
                                    type="text"
                                    name="nome"
                                    placeholder="Nome do funcionário"
                                    autocomplete="off" 
                                    value="<?php echo htmlspecialchars($funcionario['nome']); ?>"
                                    required
                                />
                                
                                <label for="senha">Senha:</label>
                                <input 
                                    type="password" 
                                    name="senha" 
                                    id="senha" 
                                    placeholder="Digite a senha do funcionário: " 
                                    autocomplete="off" 
                                    required
                                />

                                <label for="cpf">CPF:</label>
                                <input 
                                    type="text" 
                                    name="cpf" 
                                    id="cpf" 
                                    placeholder="CPF do funcionário" 
                                    value="<?php echo htmlspecialchars($funcionario['cpf']); ?>" 
                                    maxlength="14" 
                                    autocomplete="off" 
                                    readonly
                                    style="color: #6c6b6b; cursor: not-allowed;"
                                    required 
                                />
                            </div>

                            <div class="coluna">
                                <label for="email">E-mail:</label>
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="E-mail"
                                    autocomplete="off" 
                                    value="<?php echo htmlspecialchars($funcionario['email']); ?>"
                                    required
                                />

                                <label for="telefone">Telefone:</label>
                                <input
                                    type="text"
                                    name="telefone"
                                    maxlength="14"
                                    placeholder="Telefone"
                                    class="Telefone"
                                    autocomplete="off" 
                                    value="<?php echo htmlspecialchars($funcionario['telefone']); ?>"
                                    required
                                />

                                <label for="cargo">Cargo do funcionário:</label>
                                <input 
                                    type="text"
                                    name="cargo"
                                    value="<?php echo ucfirst(htmlspecialchars($funcionario['cargo'])); ?>" 
                                    disabled 
                                    style="color: #6c6b6b; cursor: not-allowed;"
                                >
                            </div>
                        </div>

                        <div class="botoes">
                            <div>
                                <a href="AdmFuncionarios.php" class="voltar">
                                    <button type="button" class="voltar">Voltar</button>
                                </a>
                            </div>
                            <div>
                                <button type="submit" name="modificar" id="cade">Atualizar</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>