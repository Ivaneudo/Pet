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
    $mensagem = '';
    $classeMensagem = ''; // Adiciona a variável para a classe da mensagem

    // Se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Se o CPF do cliente foi enviado
        if (isset($_POST['cpfCliente']) && !empty(trim($_POST['cpfCliente']))) {
            $cpfCliente = trim($_POST['cpfCliente']);

            // Busca o cliente pelo CPF
            $sql = "SELECT * FROM cliente WHERE cpf = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $cpfCliente);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $cliente = $result->fetch_assoc();
            } else {
                $mensagem = "Cliente não encontrado.";
                $classeMensagem = 'erro'; // Define a classe de erro
            }
        }

        // Se o botão de cadastrar pet foi clicado
        if (isset($_POST['cadastrarPet']) && $cliente) {
            $nomePet = $_POST['nomePet'];
            $idade = $_POST['idade'];
            $especie = $_POST['animal'];
            $sexo = $_POST['sexo'];
            $peso = str_replace(',', '.', $_POST['peso']); // Substitui vírgula por ponto
            $raca = $_POST['raca'];

            // Insere os dados do pet no banco de dados
            $sqlInsert = "INSERT INTO pet (nome_pet, idade, especie, sexo, peso, raca, cpf_dono) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("sisssss", $nomePet, $idade, $especie, $sexo, $peso, $raca, $cpfCliente);

            if ($stmtInsert->execute()) {
                $mensagem = "Pet cadastrado com sucesso!";
                $classeMensagem = 'sucesso'; // Define a classe de sucesso
                // Não redireciona para manter a mensagem após cadastro
                // Limpa os dados do formulário para novo cadastro
                $cliente = $cliente; // Mantém o cliente para mostrar o formulário novamente
            } else {
                $mensagem = "Erro ao cadastrar pet: " . $stmtInsert->error;
                $classeMensagem = 'erro'; // Define a classe de erro
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastrar Pets</title>
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
                <form method="POST" action="">
                    <div class="pesquisa-cliente">
                        <label for="cpfCliente">Pesquisar CPF do Cliente:</label>
                        <input type="text" name="cpfCliente" id="cpf" maxlength="14" placeholder="Digite o CPF do cliente" value="<?php echo htmlspecialchars($cpfCliente); ?>" required>
                        <button type="submit" name="pesquisar">Pesquisar</button>
                    </div>
                </form>

                <?php if ($mensagem): ?>
                    <div class="mensagem-<?php echo $classeMensagem; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <?php if ($cliente): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="cpfCliente" value="<?php echo htmlspecialchars($cpfCliente); ?>" autocomplete=off>
                        <div class="coluna">
                            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente['nome']); ?></p>
                        </div>
                        <div class="coluna">
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
                        </div>

                        <p>Dados do Pet</p>

                        <div class="animais">
                            <div class="coluna">
                                <div class="AnimalTipo">
                                    <input 
                                        type="radio" 
                                        class="tipo" 
                                        name="animal" 
                                        value="Gato" 
                                        id="gato" 
                                        required>
                                    <label for="gato">Gato</label>

                                    <input 
                                        type="radio" 
                                        class="tipo" 
                                        name="animal" 
                                        value="Cachorro" 
                                        id="cachorro" 
                                        required>
                                    <label for="cachorro">Cachorro</label>
                                </div>

                                <input 
                                    type="text" 
                                    name="nomePet" 
                                    class="nomePet" 
                                    placeholder="Nome do animal" 
                                    autocomplete=off 
                                    required>

                                <input 
                                    type="number" 
                                    name="idade" 
                                    class="idade" 
                                    placeholder="Idade do animal" 
                                    autocomplete=off 
                                    required 
                                    min="0">
                            </div>

                            <div class="coluna">
                                <div class="AnimalTipo">
                                    <input 
                                        type="radio" 
                                        class="tipo" 
                                        name="sexo" 
                                        value="macho" 
                                        id="sexoMacho" 
                                        autocomplete=off 
                                        required>
                                    <label for="sexoMacho">M</label>
                                    
                                    <input 
                                        type="radio" 
                                        class="tipo" 
                                        name="sexo" 
                                        value="femea" 
                                        id="sexoFemea" 
                                        autocomplete=off 
                                        required>
                                    <label for="sexoFemea">F</label>

                                    <input 
                                        type="radio" 
                                        class="tipo" 
                                        name="sexo" 
                                        value="intersexo" 
                                        id="sexoIntersexo" 
                                        autocomplete=off 
                                        required>
                                    <label for="sexoIntersexo">I</label>
                                </div>

                                <input 
                                    type="text" 
                                    name="peso" 
                                    class="peso" 
                                    placeholder="Peso" 
                                    required 
                                    pattern="^\d{1,3}(,\d{1,2})?$" 
                                    autocomplete=off>

                                <input 
                                    type="text" 
                                    name="raca" 
                                    class="raca" 
                                    placeholder="Digite a raça" 
                                    autocomplete=off 
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
                                <button type="submit" name="cadastrarPet" class="cade">Cadastrar Pet</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>