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

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $cargo = $_POST['cargo'];

    // Prepara a consulta SQL com base no cargo
    if ($cargo === 'caixa') {
        $sql = "INSERT INTO caixa (nome, cpf, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
    } elseif ($cargo === 'repositor') {
        $sql = "INSERT INTO repositor (nome, cpf, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
    } elseif ($cargo === 'adm') {
        $sql = "INSERT INTO adm (nome, cpf, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
    } else {
        // Cargo inválido
        echo "Cargo inválido.";
        exit();
    }

    // Prepara e executa a consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome, $cpf, $telefone, $email, $senha);

    if ($stmt->execute()) {
        echo "Funcionário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar funcionário: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/caixa.css">
    <link rel="stylesheet" href="../css/caixaCadastro.css">
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

        <div class="cadastrar">
            <div class="cadastro">
                <form action="" method="POST">
                    <div class="cliente">
                        <p>Novo Funcionário:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input type="text" name="nome" class="NomeCliente" placeholder="Digite o nome do funcionário: " required>
                                <input type="text" name="cpf" id="cpf" maxlength="14" placeholder="Digite o CPF do funcionário: " required>
                                <input type="password" name="senha" id="senha" placeholder="Digite a senha do funcionário: " required>
                            </div>
                            <div class="coluna">
                                <input type="text" name="telefone" class="Telefone" maxlength="14" placeholder="Digite o telefone do funcionário" required>
                                <input type="email" name="email" class="Email" placeholder="Digite o e-mail do funcionário: " required>
                                <select name="cargo" id="" required>
                                    <option value="" disabled selected>Cargo do Funcionário</option>
                                    <option value="caixa">Caixa</option>
                                    <option value="repositor">Repositor</option>
                                    <option value="adm">Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="botoes">
                        <div>
                            <a href="Adm.php" class="voltar"><button type="button" class="voltar">Voltar</button></a>
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