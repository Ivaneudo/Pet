<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é um administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../entrada/Entrar.php");
    exit();
}

// Guarda o nome do funcionário
$nomeFuncionario = $_SESSION['usuario'] ?? 'Usuário';

// Inicializa variáveis de filtro
$filtro_data = '';
$filtro_secretaria = '';

$whereClauses = [];
$params = [];
$paramTypes = '';

// Filtro por data_venda (YYYY-MM-DD)
if (!empty($_POST['data_venda'])) {
    $filtro_data = $_POST['data_venda'];
    $whereClauses[] = "DATE(v.data_venda) = ?";
    $paramTypes .= 's';
    $params[] = $filtro_data;
}

// Filtro por secretaria_id
if (!empty($_POST['secretaria_id'])) {
    $filtro_secretaria = $_POST['secretaria_id'];
    $whereClauses[] = "v.secretaria_id = ?";
    $paramTypes .= 'i';
    $params[] = $filtro_secretaria;
}

$whereSQL = '';
if (count($whereClauses) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Consulta SQL para selecionar vendas com produtos, secretaria e cliente
$sql = "
    SELECT 
        v.data_venda,
        s.nome AS secretaria_nome,
        c.nome AS cliente_nome,
        p.nome_produto,
        v.quant_produto,
        v.valor_compra,
        v.forma_de_pagamento
    FROM vendas v
    INNER JOIN secretaria s ON v.secretaria_id = s.secretaria_id
    INNER JOIN produto p ON v.id_produto = p.id_produto
    INNER JOIN cliente c ON v.cpf_cliente = c.cpf
    $whereSQL
    ORDER BY v.data_venda DESC
";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erro na preparação da consulta: " . $conn->error);
}

// Função para fazer bind_param dinâmico com referências
function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

if (count($params) > 0) {
    // Preparar array para bind_param
    array_unshift($params, $paramTypes);
    call_user_func_array([$stmt, 'bind_param'], refValues($params));
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
} else {
    die("Erro na consulta de secretarias: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Relatório de Vendas - PetShop</title>
    <style>
        :root {
            --input: #E5E8EB;
            --AzulCarinho: #E6F2FF;
            --Botoes: #5A799A;
            --Verde: #4CAF50;
            --Cinza: #6c6b6b;
            --CinzaClarinho: #cecece;
            --Laranja: #FF9800;
            --LinksHover: #40005C;
            --gradiente: linear-gradient(#CFE2F8, #5A799A);
            --gradiente2: linear-gradient(150deg, #e5dcdc, #d2caca);
            --background: #5691cf;
        }

        /* Reset e base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background);
            margin: 0;
            padding: 0;
            color: var(--Cinza);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            width: 90%;
            max-width: 960px;
            background: rgba(230, 242, 255, 0.6); /* AzulCarinho com transparência */
            border-radius: 16px;
            padding: 30px 35px;
            margin: 40px 0;
            box-shadow: 0 8px 24px rgba(86, 145, 207, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(229, 232, 235, 0.6); /* --input com transparência */
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
        }

        form > div {
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
            padding: 10px 14px;
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

        button {
            background: var(--Botoes);
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            border: none;
            padding: 13px 25px;
            border-radius: 10px;
            cursor: pointer;
            align-self: flex-end;
            user-select: none;
            transition: background 0.3s ease;
            box-shadow: 0 4px 10px rgba(90, 121, 154, 0.6);
        }

        button:hover {
            background: var(--LinksHover);
            box-shadow: 0 6px 15px rgba(64, 0, 92, 0.8);
        }

        table {
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
            background: var(--gradiente);
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
            color: var(--background);
        }

        tbody tr:hover {
            background-color: var(--background);
            color: white;
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

        /* Responsividade */
        @media (max-width: 670px) {
            form {
                flex-direction: column;
            }

            form > div {
                flex: none;
                width: 100%;
            }

            button {
                width: 100%;
                align-self: center;
            }

            thead {
                display: none;
            }

            tbody td {
                display: block;
                text-align: right;
                position: relative;
                padding-left: 55%;
                border-bottom: 1px solid var(--CinzaClarinho);
            }

            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 16px;
                width: 45%;
                padding-left: 12px;
                font-weight: 700;
                text-align: left;
                color: var(--Cinza);
                white-space: nowrap;
                user-select: none;
            }
        }
    </style>
</head>

<body>
    <main class="container" role="main">
        <header>
            <h1>Relatório de Vendas</h1>
            <div class="welcome-text">Olá, <strong><?php echo htmlspecialchars($nomeFuncionario, ENT_QUOTES, 'UTF-8'); ?></strong></div>
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
                <button type="submit" aria-label="Pesquisar vendas">Pesquisar</button>
            </div>
        </form>

        <table>
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
                        <td data-label="Data da Venda"><?php echo date('d/m/Y H:i', strtotime($row['data_venda'])); ?></td>
                        <td data-label="Secretaria"><?php echo htmlspecialchars($row['secretaria_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label="Cliente"><?php echo htmlspecialchars($row['cliente_nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label="Produto"><?php echo htmlspecialchars($row['nome_produto'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label="Quantidade"><?php echo (int)$row['quant_produto']; ?></td>
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
</body>
</html>