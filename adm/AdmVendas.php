<?php
    session_start();
    include('../funcoes/conexao.php');

    // Verifica se o usuário é um administrador
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("Location: ../entrada/Entrar.php");
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
    <style>
        :root {
            --input: #E5E8EB;
            --AzulCarinho: #E6F2FF;
            --Botoes: #5A799A;
            --Verde: #4CAF50;
            --Cinza: #6c6b6b;
            --CinzaClarinho: #cecece;
            --Laranja: #FF9800;
            --LaranjaHover: #ec9108;
            --LinksHover: #40005C;
            --gradiente: linear-gradient(#CFE2F8, #5A799A);
            --gradiente2: linear-gradient(150deg, #e5dcdc, #d2caca);
            --background: #5691cf;
            --EfeitoVidro: #ffffff77;
        }

        /* Reset e base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial;
            background: var(--background);
            margin: 0;
            padding: 0;
            /* color: var(--Cinza); */
            /* min-height: 100vh; */
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .av-container {
            width: 90%;
            max-width: 960px;
            background: var(--EfeitoVidro);
            border-radius: 16px;
            padding: 30px 35px;
            margin: 40px auto; /* Centraliza o container */
            box-shadow: 0 8px 24px rgba(86, 145, 207, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(229, 232, 235, 0.6);
        }

        .container p {
            color: white;
            font-size: 2em;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        header h1 {
            color: var(--background);
            font-weight: 700;
            font-size: 28px;
        }

        .welcome-text {
            font-size: 16px;
            color: var(--Cinza);
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 30px;
            justify-content: flex-start; /* Alinhamento corrigido para esquerda */
        }

        form>div {
            display: flex;
            flex-direction: column;
            min-width: 150px;
            flex: 1 1 200px;
        }

        label {
            font-weight: 600;
            color: var(--Cinza);
            margin-bottom: 6px;
            user-select: none;
        }

        input[type="date"],
        select {
            padding: 0px 14px;
            border-radius: 8px;
            border: 2px solid var(--input);
            font-weight: 600;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: var(--AzulCarinho);
            color: var(--background);
            outline-offset: 2px;
            outline-color: transparent;
        }

        input[type="date"]:focus,
        select:focus {
            border-color: var(--Botoes);
            box-shadow: 0 0 8px var(--Botoes);
            outline-color: var(--Botoes);
        }

        .btn-pesquisar-vendas {
            background: var(--Laranja);
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.3s ease;
            box-shadow: 0 4px 10px rgba(163, 98, 24, 0.6);
            margin-top: 23px;
            height: 33px;
            width: 120px;
        }

        .btn-pesquisar-vendas:hover {
            background: var(--LaranjaHover);
            box-shadow: 0 6px 15px rgba(168, 81, 9, 0.8);
        }

        table.av-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            color: var(--Cinza);
            border-radius: 10px;
            overflow: hidden;
            box-shadow:
                0 2px 8px rgba(90, 121, 154, 0.15),
                inset 0 0 0 1px rgba(229, 232, 235, 0.8);
            background-color: rgba(255 255 255 / 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        thead {
            background: var(--Botoes);
            user-select: none;
            color: white;
        }

        thead th {
            padding: 14px 16px;
            text-align: left;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
        }

        thead th:last-child {
            border-right: none;
        }

        tbody tr {
            transition: background-color 0.18s ease, color 0.18s ease;
        }

        tbody tr:nth-child(even) {
            background: var(--gradiente2);
            color: #6C6865;
        }

        tbody td {
            padding: 14px 16px;
            border-right: 1px solid var(--CinzaClarinho);
        }

        tbody td:last-child {
            border-right: none;
        }

        .no-data {
            text-align: center;
            padding: 40px 0;
            font-style: italic;
            color: var(--Cinza);
        }
    </style>
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
                    <li><a href="Adm.php">Menu</a></li>
                    <li><a href="AdmFuncionarios.php">Funcionários</a></li>
                    <li><a href="AdmNovoFuncionario.php">Cadastrar Funcionário</a></li>
                    <li><a href="AdmEditarFuncionario.php">Editar Funcionário</a></li>
                    <li><a href="AdmVendas.php">Vendas</a></li>
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