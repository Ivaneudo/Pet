<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é um administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../entrada/Entrar.php");
    exit();
}

// Captura o CPF do cliente da URL
$cpfCliente = isset($_GET['cpf']) ? trim($_GET['cpf']) : '';

// Busca dados do cliente
$cliente = null;
if ($cpfCliente) {
    $sql = "SELECT * FROM cliente WHERE cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cpfCliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
    } else {
        die("Cliente não encontrado.");
    }
} else {
    die("CPF do cliente não informado.");
}

// Inicializa variáveis
$petIndex = 0;

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomePet = $_POST['nomePet'];
    $idade = $_POST['idade'];
    $especie = $_POST['animal'];
    $sexo = $_POST['sexo'];
    $peso = $_POST['peso'];
    $raca = $_POST['raca'];

    // Insere os dados do pet no banco de dados
    $sqlInsert = "INSERT INTO pet (nome_pet, idade, especie, sexo, peso, raca, cpf_dono) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sisssss", $nomePet, $idade, $especie, $sexo, $peso, $raca, $cpfCliente);

    if ($stmtInsert->execute()) {
        $_SESSION['message'] = "Pet cadastrado com sucesso!";
        // Redireciona para a mesma página para permitir o cadastro de outro pet
        header("Location: " . $_SERVER['PHP_SELF'] . "?cpf=" . urlencode($cpfCliente) . "&petIndex=" . $petIndex);
        exit();
    } else {
        die("Erro ao cadastrar pet: " . $stmtInsert->error);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Pets</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/caixaCadastro.css" />
    <link rel="stylesheet" href="../css/AdmFuncionarios.css" />
    <script src="../js/mascara.js" defer></script>
    <script src="../js/racaSelect2.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>, bem-vindo a mais um dia de trabalho!</p>
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
                <?php if (isset($_SESSION['message'])): ?>
                    <p style="color: #008B00; font-weight: bold;"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
                    <?php unset($_SESSION['message']); ?>
                <?php endif;?>
                <form method="POST" action="">
                    <div class="cliente">
                        <p>Cliente:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input type="text" name="nome" class="NomeCliente" placeholder="Nome do cliente: " value="<?php echo htmlspecialchars($cliente['nome']); ?>" disabled>
                                <input type="text" id="cpf" maxlength="14" placeholder="Cpf do cliente: " value="<?php echo htmlspecialchars($cliente['cpf']); ?>" disabled>
                            </div>
                            <div class="coluna">
                                <input type="text" name="telefone" class="Telefone" maxlength="14" placeholder="Telefone do cliente" value="<?php echo htmlspecialchars($cliente['telefone']); ?>" disabled>
                                <input type="email" name="email" class="Email" placeholder="E-mail do cliente: " value="<?php echo htmlspecialchars($cliente['email']); ?>" disabled>
                            </div>
                        </div>
                    </div>

                    <p>Dados do pet</p>
                    <div class="animais">
                        <div class="coluna">
                            <div class="AnimalTipo">
                                <input type="radio" class="tipo" name="animal" value="gato" id="gato" required>
                                <label for="gato">Gato</label>
                                <input type="radio" class="tipo" name="animal" value="cachorro" id="cachorro" required>
                                <label for="cachorro">Cachorro</label>
                            </div>

                            <input type="text" name="nomePet" class="nomePet" placeholder="Digite o nome do animal: " required>
                            <input type="number" name="idade" class="idade" placeholder="Digite a idade do animal: " required min="0">
                        </div>

                        <div class="coluna">
                            <div class="AnimalTipo">
                                <input type="radio" class="tipo" name="sexo" value="macho" id="sexoMacho" required>
                                <label for="sexoMacho">M</label>
                                <input type="radio" class="tipo" name="sexo" value="femea" id="sexoFemea" required>
                                <label for="sexoFemea">F</label>
                                <input type="radio" class="tipo" name="sexo" value="intersexo" id="sexoIntersexo" required>
                                <label for="sexoIntersexo">I</label>
                            </div>

                            <input type="text" name="peso" class="peso" placeholder="Digite o peso: " required pattern="[\d\.]+">

                            <select name="raca" id="raca" required>
                                <option value="">Escolha uma raça</option>
                                <!-- Será populado via JS -->
                            </select>
                        </div>
                    </div>

                    <div class="botoes">
                        <div>
                            <a href="AdmClientes.php" class="voltar"><button type="button">Voltar</button></a>
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