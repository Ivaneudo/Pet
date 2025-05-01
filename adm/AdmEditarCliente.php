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

// Inicializa variáveis
$cpfCliente = '';
$cliente = null;
$pets = [];
$petIndex = 0;

// Captura o CPF do cliente e índice do pet da requisição (GET ou POST)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['cpf'])) {
        $cpfCliente = $_GET['cpf'];
    }
    if (isset($_GET['petIndex'])) {
        $petIndex = intval($_GET['petIndex']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cpfCliente'])) {
        $cpfCliente = $_POST['cpfCliente'];
    }
    if (isset($_POST['petIndex'])) {
        $petIndex = intval($_POST['petIndex']);
    }
}

// Busca dados do cliente
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

    // Busca pets do cliente
    $sqlPets = "SELECT * FROM pet WHERE cpf_dono = ?";
    $stmtPets = $conn->prepare($sqlPets);
    $stmtPets->bind_param("s", $cpfCliente);
    $stmtPets->execute();
    $resultPets = $stmtPets->get_result();

    while ($row = $resultPets->fetch_assoc()) {
        $pets[] = $row;
    }
} else {
    die("CPF do cliente não informado.");
}

// Ajusta petIndex para não sair do intervalo dos pets disponíveis
if ($petIndex < 0) $petIndex = 0;
if ($petIndex >= count($pets)) $petIndex = count($pets) - 1;

// Variáveis para o pet atual (se existir)
$currentPet = null;
if (count($pets) > 0 && isset($pets[$petIndex])) {
    $currentPet = $pets[$petIndex];
}

// Trata o envio do formulário para modificar os dados
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar'])) {
    // Atualiza dados do cliente
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    $sqlUpdateCliente = "UPDATE cliente SET nome = ?, telefone = ?, email = ? WHERE cpf = ?";
    $stmtUpdateCliente = $conn->prepare($sqlUpdateCliente);
    $stmtUpdateCliente->bind_param("ssss", $nome, $telefone, $email, $cpfCliente);
    if (!$stmtUpdateCliente->execute()) {
        die("Erro ao atualizar cliente: " . $stmtUpdateCliente->error);
    }

    // Atualiza dados do pet se existir algum pet
    if ($currentPet) {
        $nomePet = $_POST['nomePet'];
        $idade = $_POST['idade'];
        $especie = $_POST['especie'];
        $sexo = $_POST['sexo'];
        $peso = $_POST['peso'] ?? null;  // campo extra, pode ser nulo
        $raca = $_POST['raca'] ?? null;

        $sqlUpdatePet = "UPDATE pet SET nome_pet = ?, idade = ?, especie = ?, sexo = ?, peso = ?, raca = ? WHERE id_pet = ?";
        $stmtUpdatePet = $conn->prepare($sqlUpdatePet);
        $id_pet = $currentPet['id_pet'];
        $stmtUpdatePet->bind_param("sissdsi", $nomePet, $idade, $especie, $sexo, $peso, $raca, $id_pet);
        if (!$stmtUpdatePet->execute()) {
            die("Erro ao atualizar pet: " . $stmtUpdatePet->error);
        }
    }

    $message = "Informações atualizadas com sucesso!";
    // Redireciona para a mesma página para evitar reenvio de formulário
    header("Location: AdmEditarCliente.php?cpf=" . urlencode($cpfCliente) . "&petIndex=" . $petIndex);
    exit();
}

// Navegação entre pets
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['navegar'])) {
    if ($_POST['navegar'] === 'proximo') {
        if ($petIndex < count($pets) - 1) {
            $petIndex++;
        }
    } elseif ($_POST['navegar'] === 'voltar') {
        if ($petIndex > 0) {
            $petIndex--;
        }
    }
    header("Location: AdmEditarCliente.php?cpf=" . urlencode($cpfCliente) . "&petIndex=" . $petIndex);
    exit();
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
    <script src="../js/racaSelect.js" defer></script>
    <script>
        window.racaAtual = '<?php echo $currentPet['raca'] ?? ''; ?>';
    </script>
    <script src="../js/mascara.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="AdmNovoFuncionario.php">Novo funcionario</a></li>
                    <li><a href="AdmFuncionarios.php">Funcionarios</a></li>
                    <li><a href="AdmClientes.php">Clientes</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
                <?php if($message): ?>
                    <p style="color:green; font-weight:bold;"><?php echo $message; ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="hidden" name="cpfCliente" value="<?php echo htmlspecialchars($cpfCliente); ?>" />
                    <input type="hidden" name="petIndex" value="<?php echo $petIndex; ?>" />

                    <div class="cliente">
                        <p>Cliente:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input type="text" name="nome" class="NomeCliente" placeholder="Nome do cliente" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                                <input type="text" name="telefone" class="Telefone" maxlength="14" placeholder="Telefone do cliente" value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
                            </div>
                            <div class="coluna">
                                <input type="email" name="email" class="Email" placeholder="E-mail do cliente" value="<?php echo htmlspecialchars($cliente['email']); ?>">
                            </div>
                        </div>
                    </div>

                    <p>Dados do pet</p>

                    <?php if ($currentPet): ?>
                    <div class="animais">
                        <div class="coluna">
                            <div class="AnimalTipo">
                                <input type="radio" class="tipo" name="especie" value="gato" id="gato" <?php if ($currentPet['especie'] === 'gato') echo 'checked'; ?>>
                                <label for="gato">Gato</label>
                                <input type="radio" class="tipo" name="especie" value="cachorro" id="cachorro" <?php if ($currentPet['especie'] === 'cachorro') echo 'checked'; ?>>
                                <label for="cachorro">Cachorro</label>
                            </div>

                            <input type="text" name="nomePet" class="nomePet" placeholder="Nome do animal" value="<?php echo htmlspecialchars($currentPet['nome_pet']); ?>" required>
                            <input type="number" name="idade" class="idade" placeholder="Idade do animal" value="<?php echo htmlspecialchars($currentPet['idade']); ?>">
                        </div>
                        <div class="coluna">
                            <div class="AnimalTipo">
                                <input type="radio" class="tipo" name="sexo" value="macho" id="sexoMacho" <?php if ($currentPet['sexo'] === 'macho') echo 'checked'; ?>>
                                <label for="sexoMacho">M</label>
                                <input type="radio" class="tipo" name="sexo" value="femea" id="sexoFemea" <?php if ($currentPet['sexo'] === 'femea') echo 'checked'; ?>>
                                <label for="sexoFemea">F</label>
                                <input type="radio" class="tipo" name="sexo" value="intersexo" id="sexoIntersexo" <?php if ($currentPet['sexo'] === 'intersexo') echo 'checked'; ?>>
                                <label for="sexoIntersexo">I</label>
                            </div>

                            <input type="text" name="peso" class="peso" placeholder="Peso" value="<?php echo htmlspecialchars($currentPet['peso'] ?? ''); ?>">

                            <select name="raca" id="raca">
                                <!-- Options will be populated by JS -->
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                        <p style="color: red;">Este cliente não possui pets cadastrados.</p>
                    <?php endif; ?>

                    <div class="botoes">
                        <div>
                            <button type="submit" name="navegar" value="voltar" class="voltar" style="color: black;" <?php if($petIndex <= 0) echo 'disabled'; ?>>Voltar</button>
                        </div>
                        <div>
                            <button type="submit" name="navegar" value="proximo" class="novo" style="color: black;" <?php if($petIndex >= count($pets)-1) echo 'disabled'; ?>>Próximo Pet</button>
                            <button type="submit" name="modificar" class="cade">Modificar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>