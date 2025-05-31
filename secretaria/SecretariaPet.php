<?php
    session_start();
    include('../funcoes/conexao.php'); // Inclua a conexão com o banco

    // Verifica se o usuário é uma secretaria
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
        header("Location: ../entrada/Entrar.php"); // Redireciona se não for secretaria
        exit();
    }

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];

    // Inicializa a variável para armazenar o resultado da pesquisa
    $result = null;

    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cpf'])) {
        $cpfPesquisado = trim($_POST['cpf']);

        // Consulta para buscar os pets do dono com o CPF informado
        $sql = "
            SELECT p.id_pet, p.nome_pet, p.raca, p.especie, c.nome AS dono_nome, p.cpf_dono
            FROM pet p
            JOIN cliente c ON p.cpf_dono = c.cpf
            WHERE p.cpf_dono = ?
            ORDER BY c.nome ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cpfPesquisado);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        // Consulta para buscar todos os pets se nenhum CPF for pesquisado
        $sql = "
            SELECT p.id_pet, p.nome_pet, p.raca, p.especie, c.nome AS dono_nome, p.cpf_dono
            FROM pet p
            JOIN cliente c ON p.cpf_dono = c.cpf
            ORDER BY c.nome, p.nome_pet ASC
        ";
        $result = $conn->query($sql);
    }

    if ($result === false) {
        die("Erro na consulta: " . $conn->error);
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pets</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/repositor.css" />
    <link rel="stylesheet" href="../css/AdmFuncionarios.css" />
    <link rel="stylesheet" href="../css/responsivo.css">
    <script src="../js/mascara.js" defer></script>
    <script src="../js/excluirPet.js" defer></script>
    <script>
        function confirmarExclusao(cpf, petId, petNome) {
            if (confirm("Tem certeza que deseja remover o pet '" + petNome + "' do dono com CPF " + cpf + "?")) {
                window.location.href = "excluirPet.php?cpf=" + encodeURIComponent(cpf) + "&petId=" + encodeURIComponent(petId);
            }
        }
    </script>
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
                    <li><a href="Secretaria.php">Menu</a></li>
                    <li><a href="SecretariaPet.php">Pets</a></li>
                    <li><a href="SecretariaCadastroPet.php">Cadastrar Pet</a></li>
                </ul>
                </nav>
                </div>
                <div class="estoque">
                    <div class="esto">
                        <form method="POST" action="">
                            <div class="pesquisa">
                                <div class="campo">
                                    <input type="text" name="cpf" id="cpf" placeholder="Digite o CPF do dono: " maxlength="14" autocomplete="off" value="<?php echo isset($cpfPesquisado) ? htmlspecialchars($cpfPesquisado) : ''; ?>"/>
                                    <button type="submit" style="background: none; border: none; cursor: pointer;">
                                        <img src="../img/search-svgrepo-com.svg" alt="Buscar">
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="produtos">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Dono</th>
                                        <th>Pet</th>
                                        <th>Espécie</th>
                                        <th>Raça</th>
                                        <th>Editar</th>
                                        <th>Remover</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($row['dono_nome']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($row['nome_pet']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($row['especie']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($row['raca']); ?>
                                            </td>
                                            <td>
                                                <a href="SecretariaEditarPet.php?id_pet=<?php echo urlencode($row['id_pet']); ?>" style="color: #40005C;">
                                                    <img src="../img/editar.png" alt="">
                                                </a>
                                            </td>
                                            <td class="demitir">
                                                <a href="javascript:void(0);" onclick="confirmarExclusao('<?php echo htmlspecialchars($row['cpf_dono']); ?>', '<?php echo $row['id_pet']; ?>', '<?php echo htmlspecialchars($row['nome_pet']); ?>')">
                                                    <img src="../img/lata-de-lixo.png" alt="Remover">
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">Nenhum pet encontrado.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>