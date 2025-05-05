<?php
session_start();

// Verifica se o usuário é um adm
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../entrada/Entrar.php"); // Redireciona se não for adm
    exit();
}

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];

// Inicializa variáveis
$clientes = [];
$pets = [];
$mensagem = '';

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados do cliente
    $clienteNome = $_POST['nome'] ?? '';
    $clienteCpf = $_POST['cpf'] ?? '';
    $clienteTelefone = $_POST['Telefone'] ?? '';
    $clienteEmail = $_POST['email'] ?? '';

    // Captura os dados do pet
    $petNome = $_POST['nomePet'] ?? '';
    $petIdade = $_POST['idade'] ?? '';
    $petPeso = $_POST['peso'] ?? '';
    $petSexo = $_POST['sexo'] ?? '';
    $petEspecie = $_POST['animal'] ?? '';
    $petRaca = $_POST['raça'] ?? '';

    // Salva o cliente e o pet (a lógica de inserção no banco de dados deve ser implementada aqui)
    if (!empty($clienteNome)) {
        // Aqui você pode adicionar a lógica para salvar o cliente no banco de dados
        $clientes[] = [
            'nome' => $clienteNome,
            'cpf' => $clienteCpf,
            'telefone' => $clienteTelefone,
            'email' => $clienteEmail,
        ];
    }

    if (!empty($petNome)) {
        // Aqui você pode adicionar a lógica para salvar o pet no banco de dados
        $pets[] = [
            'nome' => $petNome,
            'idade' => $petIdade,
            'peso' => $petPeso,
            'sexo' => $petSexo,
            'especie' => $petEspecie,
            'raca' => $petRaca,
        ];
    }

    $mensagem = "Cliente e pet cadastrados com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Clientes</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/caixa.css">
    <link rel="stylesheet" href="../css/caixaCadastro.css">
    <script src="../js/mascara.js" defer></script>
    <script src="../js/racaSelect2.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
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
                    <li><a href="AdmClientes.php">Clientes</a></li>
                    <li><a href="AdmCadastrarCliente.php">Cadastrar Cliente</a></li>
                    <li><a href="AdmCadastrarPet.php">Cadastrar Pet</a></li>
                </ul>
            </nav>
        </div>

        <div class="cadastrar">
            <div class="cadastro">
                <?php if ($mensagem): ?>
                    <p style="color: green;"><?php echo htmlspecialchars($mensagem); ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="cliente">
                        <p>Cliente:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input type="text" name="nome" class="NomeCliente" placeholder="Nome do cliente: " required>
                                <input type="text" id="cpf" name="cpf" maxlength="14" placeholder="Digite o CPF do cliente: " required>
                            </div>
                            <div class="coluna">
                                <input type="text" name="Telefone" class="Telefone" maxlength="14" placeholder="Digite o telefone do cliente" required>
                                <input type="email" name="email" class="Email" placeholder="Digite o e-mail do cliente: " required>
                            </div>
                        </div>
                    </div>

                    <p>Dados do Pet:</p>
                    <div class="animais">
                        <div class="coluna">
                            <div class="AnimalTipo">
                                <input type="radio" class="tipo" name="animal" value="gato" id="gato">
                                <label for="gato">Gato</label>
                                <input type="radio" class="tipo" name="animal" value="cachorro" id="cachorro">
                                <label for="cachorro">Cachorro</label>
                            </div>

                            <input type="text" name="nomePet" class="nomePet" placeholder="Digite o nome do animal: ">
                            <input type="text" name="idade" class="idade" placeholder="Digite a idade do animal: ">
                        </div>

                        <div class="coluna">
                            <div class="AnimalTipo">
                                <input type="radio" class="tipo" name="sexo" value="macho" id="sexoMacho" >
                                <label for="sexoMacho">M</label>
                                <input type="radio" class="tipo" name="sexo" value="femea" id="sexoFemea" >
                                <label for="sexoFemea">F</label>
                                <input type="radio" class="tipo" name="sexo" value="intersexo" id="sexoIntersexo" >
                                <label for="sexoIntersexo">I</label>
                            </div>

                            <input type="text" name="peso" class="peso" placeholder="Digite o peso: ">

                            <select name="raça" id="raca" required>
                                <option value="">Escolha uma raça</option>
                                <!-- As opções serão preenchidas pelo JavaScript -->
                            </select>
                        </div>
                    </div>

                    <div class="botoes">
                        <div>
                            <button type="button" class="voltar" id="volt" onclick="window.history.back();">Voltar</button>
                        </div>
                        <div>
                            <button type="button" id="novo" onclick="document.getElementById('raca').selectedIndex = 0; document.querySelector('input[name=nomePet]').value = ''; document.querySelector('input[name=idade]').value = ''; document.querySelector('input[name=peso]').value = ''; document.querySelector('input[name=sexo]:checked').checked = false;">Novo Pet</button>
                            <button type="submit" id="cade">Cadastrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>