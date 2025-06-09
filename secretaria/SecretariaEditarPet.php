<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é uma secretaria
    if ($_SESSION['tipo_usuario'] !== 'secretaria'){
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    $nomeFuncionario = $_SESSION['usuario'];

    $pet = null;
    $mensagem = '';

    if (isset($_GET['id_pet'])) {
        $idPet = $_GET['id_pet'];

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

    // Quando o formulário for enviado:
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $pet) {
        $nomePet = $_POST['nomePet'];
        $idade = $_POST['idade'];
        $especie = $_POST['animal'];
        $sexo = $_POST['sexo'];
        $peso = str_replace(',', '.', $_POST['peso']);
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

    // Busca o nome do dono do pet
    $nomeDono = '';
    if ($pet) {
        $sqlDono = "SELECT nome FROM cliente WHERE cpf = ?";
        $stmtDono = $conn->prepare($sqlDono);
        $stmtDono->bind_param("s", $pet['cpf_dono']);
        $stmtDono->execute();
        $resultDono = $stmtDono->get_result();

        if ($resultDono->num_rows > 0) {
            $dono = $resultDono->fetch_assoc();
            $nomeDono = $dono['nome'];
        } else {
            $mensagem = "Dono não encontrado.";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Pet</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px.ico" type="image/x-icon" />
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
                    <li><a href="Secretaria.php">Menu</a></li>
                    <li><a href="SecretariaPet.php">Pets</a></li>
                    <li><a href="SecretariaCadastroPet.php">Cadastrar Pet</a></li>
                </ul>
            </nav>
        </div>

        <div class="cadastrar">
            <div class="cadastro">
                <?php if ($mensagem): ?>
                    <div class="mensagem-<?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <?php if ($pet): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="id_pet" value="<?php echo htmlspecialchars($pet['id_pet']); ?>">
                        <div class="coluna">
                            <p><strong>Dono(a):</strong> <?php echo htmlspecialchars($nomeDono); ?></p>
                        </div>

                        <p>Dados do Pet</p>

                        <div class="animais">
                            <div class="coluna">
                                <div class="AnimalTipo">
                                    <input 
                                    type="radio" 
                                    class="tipo" 
                                    id="gato" 
                                    name="animal" 
                                    value="Gato" 
                                    <?php echo ($pet['especie'] === 'Gato') ? 'checked' : ''; ?> 
                                    required>
                                    <label for="gato">Gato</label>

                                    <input type="radio" 
                                    class="tipo" 
                                    id="cachorro" 
                                    name="animal" 
                                    value="Cachorro" 
                                    <?php echo ($pet['especie'] === 'Cachorro') ? 'checked' : ''; ?> 
                                    required>
                                    <label for="cachorro">Cachorro</label>
                                </div>

                                <input type="text" 
                                name="nomePet" 
                                class="nomePet" 
                                placeholder="Nome do animal" 
                                value="<?php echo htmlspecialchars($pet['nome_pet']); ?>" 
                                autocomplete=off 
                                required>

                                <input 
                                type="number" 
                                name="idade" 
                                class="idade" 
                                placeholder="Idade do animal" 
                                min="0" 
                                value="<?php echo htmlspecialchars($pet['idade']); ?>" 
                                autocomplete=off 
                                required>
                            </div>

                            <div class="coluna">
                                <div class="AnimalTipo">
                                    <input 
                                    type="radio" 
                                    class="tipo" 
                                    id="sexoMacho" 
                                    name="sexo" 
                                    value="macho" 
                                    <?php echo ($pet['sexo'] === 'macho') ? 'checked' : ''; ?> 
                                    required>
                                    <label for="sexoMacho">M</label>
                                    
                                    <input 
                                    type="radio" 
                                    class="tipo" 
                                    id="sexoFemea" 
                                    name="sexo" 
                                    value="femea" 
                                    <?php echo ($pet['sexo'] === 'femea') ? 'checked' : ''; ?> 
                                    required>
                                    <label for="sexoFemea">F</label>

                                    <input 
                                    type="radio" 
                                    class="tipo" 
                                    name="sexo" 
                                    value="intersexo" 
                                    id="sexoIntersexo" 
                                    <?php echo ($pet['sexo'] === 'intersexo') ? 'checked' : ''; ?> 
                                    required>
                                    <label for="sexoIntersexo">I</label>
                                </div>

                                <input type="text" 
                                name="peso" 
                                class="peso" 
                                placeholder="Peso" 
                                autocomplete=off 
                                pattern="^\d{1,3}(,\d{1,2})?$" 
                                value="<?php echo htmlspecialchars(str_replace('.', ',', $pet['peso'])); ?>" 
                                required>

                                <input 
                                type="text" 
                                name="raca" 
                                class="raca" 
                                placeholder="Digite a raça" 
                                autocomplete=off 
                                value="<?php echo htmlspecialchars($pet['raca']); ?>" 
                                required>
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