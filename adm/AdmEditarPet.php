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
$pet = null;
$mensagem = '';

// Verifica se o ID do pet foi passado na URL
if (isset($_GET['id_pet'])) {
    $idPet = $_GET['id_pet'];

    // Busca os dados do pet pelo ID
    $sql = "SELECT * FROM pet WHERE id_pet = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPet);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
    } else {
        $mensagem = "Pet não encontrado.";
    }
}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && $pet) {
    $nomePet = $_POST['nomePet'];
    $idade = $_POST['idade'];
    $especie = $_POST['animal'];
    $sexo = $_POST['sexo'];
    $peso = str_replace(',', '.', $_POST['peso']); // Substitui vírgula por ponto
    $raca = $_POST['raca'];

    // Atualiza os dados do pet no banco de dados
    $sqlUpdate = "UPDATE pet SET nome_pet = ?, idade = ?, especie = ?, sexo = ?, peso = ?, raca = ? WHERE id_pet = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sissssi", $nomePet, $idade, $especie, $sexo, $peso, $raca, $idPet);

    if ($stmtUpdate->execute()) {
        $mensagem = "Pet atualizado com sucesso!";
    } else {
        $mensagem = "Erro ao atualizar pet: " . $stmtUpdate->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Pet</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/caixaCadastro.css" />
    <link rel="stylesheet" href="../css/AdmFuncionarios.css" />
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
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Adm.php">Menu</a></li>
                    <li><a href="AdmPet.php">Pets</a></li>
                    <li><a href="AdmCadastroPet.php">Cadastrar Pet</a></li>
                </ul>
            </nav>
        </div>

        <div class="cadastrar">
            <div class="cadastro">
                <?php if ($mensagem): ?>
                    <strong><p style="color: <?php echo strpos($mensagem, 'sucesso') !== false ? '#008B00' : '#CD0000'; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </p></strong>
                <?php endif; ?>

                <?php if ($pet): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="id_pet" value="<?php echo htmlspecialchars($pet['id_pet']); ?>">
                        <div class="coluna">
                            <p><strong>CPF do Dono:</strong> <?php echo htmlspecialchars($pet['cpf_dono']); ?></p>
                        </div>

                        <p>Dados do Pet</p>

                        <div class="animais">
                            <div class="coluna">
                                <div class="AnimalTipo">
                                    <input type="radio" class="tipo" name="animal" value="Gato" id="gato" <?php echo ($pet['especie'] === 'Gato') ? 'checked' : ''; ?> required>
                                    <label for="gato">Gato</label>

                                    <input type="radio" class="tipo" name="animal" value="Cachorro" id="cachorro" <?php echo ($pet['especie'] === 'Cachorro') ? 'checked' : ''; ?> required>
                                    <label for="cachorro">Cachorro</label>
                                </div>

                                <input type="text" name="nomePet" class="nomePet" placeholder="Nome do animal" value="<?php echo htmlspecialchars($pet['nome_pet']); ?>" required>
                                <input type="number" name="idade" class="idade" placeholder="Idade do animal" min="0" value="<?php echo htmlspecialchars($pet['idade']); ?>" required>
                            </div>

                            <div class="coluna">
                                <div class="AnimalTipo">
                                    <input type="radio" class="tipo" name="sexo" value="macho" id="sexoMacho" <?php echo ($pet['sexo'] === 'macho') ? 'checked' : ''; ?> required>
                                    <label for="sexoMacho">M</label>
                                    
                                    <input type="radio" class="tipo" name="sexo" value="femea" id="sexoFemea" <?php echo ($pet['sexo'] === 'femea') ? 'checked' : ''; ?> required>
                                    <label for="sexoFemea">F</label>

                                    <input type="radio" class="tipo" name="sexo" value="intersexo" id="sexoIntersexo" <?php echo ($pet['sexo'] === 'intersexo') ? 'checked' : ''; ?> required>
                                    <label for="sexoIntersexo">I</label>
                                </div>

                                <input type="text" name="peso" class="peso" placeholder="Peso" pattern="^\d{1,3}(,\d{1,2})?$" title="Peso válido, use vírgula como separador decimal, ex: 12,34" value="<?php echo htmlspecialchars(str_replace('.', ',', $pet['peso'])); ?>" required>

                                <input type="text" name="raca" class="raca" placeholder="Digite a raça" value="<?php echo htmlspecialchars($pet['raca']); ?>" required>
                            </div>
                        </div>

                        <div class="botoes">
                            <div>
                                <a href="AdmPet.php">
                                    <button type="button" class="voltar" id="volt">Voltar</button>
                                </a>
                            </div>
                            <div>
                                <button type="submit" name="atualizarPet" class="cade">Atualizar Pet</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <p>Pet não encontrado ou não informado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>