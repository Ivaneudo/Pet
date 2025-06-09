<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é uma secretaria
    if ($_SESSION['tipo_usuario'] !== 'secretaria'){
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];

    // Inicializa a variável para armazenar o resultado da pesquisa e CPF pesquisado
    $result = null;
    $cpfPesquisado = '';

    // Verifica se o formulário de pesquisa foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cpf']) && !isset($_POST['finalizar'])) {
        $cpfPesquisado = trim($_POST['cpf']);

        // Consulta para buscar os pets do dono com o CPF informado
        $sql = "
            SELECT p.id_pet, p.nome_pet, p.especie, p.sexo
            FROM pet p
            WHERE p.cpf_dono = ?
            ORDER BY p.nome_pet ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cpfPesquisado);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['finalizar'])) {
        // Processa o envio do formulário com os pets e serviço selecionado
        $cpfPesquisado = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
        $servicos = isset($_POST['servico']) ? $_POST['servico'] : '';
        $petsSelecionados = isset($_POST['pets_selecionados']) ? $_POST['pets_selecionados'] : [];

        if (empty($petsSelecionados) || empty($servicos)) {
            header("Location: SecretariaServiços.php?erro=Selecione um serviço e pelo menos um pet");
            exit();
        }

        $valorTotal = 0;

        // Calcula o valor total com base nos serviços selecionados e número de pets
        foreach ($petsSelecionados as $pet) {
            switch ($servicos) {
                case 'banho':
                    $valorTotal += 90;
                    break;
                case 'tosa':
                    $valorTotal += 60;
                    break;
                case 'banho e tosa':
                    $valorTotal += 135;
                    break;
            }
        }

        // Armazena todos os dados na sessão
        $_SESSION['dados_pagamento'] = [
            'valor' => $valorTotal,
            'cpf' => $cpfPesquisado,
            'pets' => $petsSelecionados,
            'servico' => $servicos
        ];

        // Redireciona sem parâmetros na URL
        header("Location: SecretariaPagServico.php");
        exit();
    } else {
        $result = null;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Serviços</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/CaixaServico.css" />
    <link rel="stylesheet" href="../css/responsivo.css">
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
                    <li><a href="SecretariaVendas.php"><span class="icons"><img src="../img/compra.png" alt=""></span>Caixa</a></li>
                    <li><a href="SecretariaServiços.php"><span class="icons"><img src="../img/servicos.png" alt=""></span>Serviço</a></li>
                    <li><a href="SecretariaProdutos.php"><span class="icons"><img src="../img/produtos.png" alt=""></span>Estoque</a></li>
                </ul>
            </nav>
        </div>
        <div class="servico">
            <div class="servi">
                <form method="POST" action="">
                    <div class="pesquisa">
                        <div class="campo">
                            <input
                                type="text"
                                name="cpf"
                                placeholder="CPF do dono"
                                id="cpf"
                                autocomplete=off
                                maxlength="14"
                                value="<?php echo htmlspecialchars($cpfPesquisado); ?>"
                                required
                            />

                            <button type="submit" aria-label="Pesquisar" style="background:none; border:none; cursor:pointer;">
                                <img src="../img/search-svgrepo-com.svg" alt="Pesquisar" style="width: 24px; height: 24px;" />
                            </button>
                        </div>
                    </div>
                </form>

                <form method="POST" action="" id="formServico" style="margin-top:15px;">
                    <input type="hidden" name="cpf" value="<?php echo htmlspecialchars($cpfPesquisado); ?>" />
                    <div class="dadosPet">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Especie</th>
                                    <th>Sexo</th>
                                    <th>Selecionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['nome_pet']); ?></td>
                                            <td><?php echo htmlspecialchars($row['especie']); ?></td>
                                            <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                                            <td>
                                                <input type="checkbox" name="pets_selecionados[]" value="<?php echo htmlspecialchars($row['id_pet']); ?>" />
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align:center;">Nenhum pet encontrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($result && $result->num_rows > 0): ?>
                    <div class="Servico" style="margin-top: 15px;">
                        <select name="servico" required aria-label="Tipo de serviço">
                            <option value="" disabled selected>Tipo de serviço</option>
                            <option value="banho">Banho</option>
                            <option value="tosa">Tosa</option>
                            <option value="banho e tosa">Banho e Tosa</option>
                        </select>
                    </div>

                    <div class="botoes" style="margin-top: 15px; display: flex; gap: 10px;">
                        <button type="button" class="voltar" onclick="window.location.href='Secretaria.php'">Cancelar Serviço</button>

                        <button type="submit" name="finalizar">Pagar</button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('formServico').addEventListener('submit', function(e) {
            const pets = document.querySelectorAll('input[name="pets_selecionados[]"]:checked');
            if (pets.length === 0) {
                alert('Por favor, selecione ao menos um pet.');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>