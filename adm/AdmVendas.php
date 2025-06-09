<?php
    session_start();
    include('../funcoes/conexao.php');

    // ! Verifica qual o cargo do funcionário logado
    if ($_SESSION['tipo_usuario'] !== 'admin'){
        header("Location: ../entrada/Entrar.php"); // ! Redireciona se não for admin
        exit();
    }

    // Verifica conexão com o banco de dados
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    // DEBUG: Verificar se a tabela vendas tem registros
    $check_vendas = $conn->query("SELECT COUNT(*) as total FROM vendas");
    $total_vendas = $check_vendas->fetch_assoc()['total'];
    echo "<!-- DEBUG: Total de vendas no banco: $total_vendas -->";

    // Guarda o nome do funcionário
    $nomeFuncionario = $_SESSION['usuario'] ?? 'Usuário';

    // Inicializa variáveis de filtro
    $filtro_data = $_POST['data_venda'] ?? '';
    $filtro_secretaria = $_POST['secretaria_id'] ?? '';

    $whereClauses = [];
    $params = [];
    $paramTypes = '';

    // Filtro por data_venda (YYYY-MM-DD)
    if (!empty($filtro_data)) {
        $whereClauses[] = "DATE(v.data_venda) = ?";
        $paramTypes .= 's';
        $params[] = $filtro_data;
    }

    // Filtro por secretaria_id
    if (!empty($filtro_secretaria)) {
        $whereClauses[] = "v.secretaria_id = ?";
        $paramTypes .= 'i';
        $params[] = $filtro_secretaria;
    }

    $whereSQL = '';
    if (count($whereClauses) > 0) {
        $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
    }

    // Consulta SQL revisada com LEFT JOIN para garantir que mostre vendas mesmo se algum relacionamento falhar
    $sql = "
        SELECT 
            v.data_venda,
            s.nome AS secretaria_nome,
            c.nome AS cliente_nome,
            p.nome_produto,
            v.quant_produto AS quantidade,
            v.valor_compra,
            v.forma_de_pagamento
        FROM vendas v
        LEFT JOIN secretaria s ON v.secretaria_id = s.secretaria_id
        LEFT JOIN produto p ON v.id_produto = p.id_produto
        LEFT JOIN cliente c ON v.cpf_cliente = c.cpf
        $whereSQL
        ORDER BY v.data_venda DESC
    ";

    // DEBUG: Mostrar consulta SQL
    echo "<!-- DEBUG SQL: $sql -->";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    if (count($params) > 0) {
        $stmt->bind_param($paramTypes, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Erro na execução da consulta: " . $conn->error);
    }

    // Buscar todas as secretarias para o select
    $secretarias = [];
    $sql_secretarias = "SELECT secretaria_id, nome FROM secretaria ORDER BY nome ASC";
    $result_secretarias = $conn->query($sql_secretarias);
    if ($result_secretarias) {
        while ($row = $result_secretarias->fetch_assoc()) {
            $secretarias[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <title>Histórico de Vendas</title>
    <link rel="stylesheet" href="../css/repositor.css">
    <link rel="stylesheet" href="../css/admVendas.css">
    <link rel="stylesheet" href="../css/responsivo.css">
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <strong><span><?php echo htmlspecialchars($nomeFuncionario); ?></span></strong>, bem-vindo a mais um dia de trabalho!</p>
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
        <main class="av-container" role="main">
            <header>
                <h1>Histórico de Vendas</h1>
            </header>

            <form method="POST" action="" aria-label="Formulário de filtrar vendas">
                <div>
                    <label for="data_venda">Data da Venda</label>
                    <input type="date" id="data_venda" name="data_venda" value="<?php echo htmlspecialchars($filtro_data, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label for="secretaria_id">Secretaria</label>
                    <select id="secretaria_id" name="secretaria_id" aria-describedby="secretariaHelp">
                        <option value="">-- Todas --</option>
                        <?php foreach ($secretarias as $sec): ?>
                        <option value="<?php echo htmlspecialchars($sec['secretaria_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($sec['secretaria_id'] == $filtro_secretaria ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($sec['nome'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" aria-label="Pesquisar vendas" class="btn-pesquisar-vendas">Pesquisar</button>
                </div>
            </form>

            <table class="av-table">
                <thead>
                    <tr>
                        <th>Data da Venda</th>
                        <th>Secretaria</th>
                        <th>Cliente</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Valor da Compra</th>
                        <th>Forma de Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Data da Venda"><?php echo date('d/m/Y', strtotime($row['data_venda'])); ?></td>
                            <td data-label="Secretaria"><?php echo htmlspecialchars($row['secretaria_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td data-label="Cliente"><?php echo htmlspecialchars($row['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td data-label="Produto"><?php echo htmlspecialchars($row['nome_produto'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td data-label="Quantidade"><?php echo (int)$row['quantidade']; ?></td>
                            <td data-label="Valor da Compra">R$ <?php echo number_format($row['valor_compra'], 2, ',', '.'); ?></td>
                            <td data-label="Forma de Pagamento"><?php echo htmlspecialchars($row['forma_de_pagamento'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">Nenhuma venda encontrada para os filtros selecionados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>