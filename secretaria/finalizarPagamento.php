<?php
session_start();
include('../funcoes/conexao.php'); // Inclua a conexão com o banco

// Verifica se o usuário é uma secretaria
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: ../entrada/Entrar.php"); // Redireciona se não for secretaria
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valorCompra = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;
    $cpfCliente = isset($_POST['cpf']) ? $_POST['cpf'] : '';
    $servico = isset($_POST['servico']) ? $_POST['servico'] : '';
    $petsSelecionados = isset($_POST['pets']) ? explode(",", $_POST['pets']) : [];

    // Captura o id do caixa (secretaria) a partir da sessão
    // Certifique-se que este dado está salvo na sessão com chave 'secretaria_id' ou similar
    if (!isset($_SESSION['secretaria_id'])) {
        die("Erro: Usuário caixa não identificado.");
    }
    $caixaId = $_SESSION['secretaria_id'];

    if (empty($petsSelecionados)) {
        die("Erro: Nenhum pet selecionado.");
    }

    // Buscar id_cliente a partir do CPF
    $sqlCliente = "SELECT id_cliente FROM cliente WHERE cpf = ?";
    $stmtCliente = $conn->prepare($sqlCliente);
    $stmtCliente->bind_param("s", $cpfCliente);
    $stmtCliente->execute();
    $resultCliente = $stmtCliente->get_result();
    if ($resultCliente->num_rows === 0) {
        die("Erro: Cliente não encontrado.");
    }
    $clienteData = $resultCliente->fetch_assoc();
    $idCliente = $clienteData['id_cliente'];
    $stmtCliente->close();

    // Inserir uma venda para cada pet selecionado com o serviço e valores
    $sqlInsertVenda = "INSERT INTO vendas (valor_compra, forma_de_pagamento, secretaria_id, id_cliente, id_pet, servico)
                        VALUES (?, ?, ?, ?, ?, ?)";

    $stmtVenda = $conn->prepare($sqlInsertVenda);

    // O valor por pet depende do serviço selecionado
    $valorPorPet = 0;
    switch ($servico) {
        case 'banho':
            $valorPorPet = 90;
            break;
        case 'tosa':
            $valorPorPet = 60;
            break;
        case 'banho e tosa':
            $valorPorPet = 135;
            break;
        default:
            $valorPorPet = 0;
    }

    // Forma de pagamento
    $formaPagamento = isset($_POST['cartao']) ? 'cartao' : 'dinheiro';

    // Inserir um registro de venda para cada pet individualmente
    foreach ($petsSelecionados as $idPet) {
        $stmtVenda->bind_param("dsiiis", $valorPorPet, $formaPagamento, $caixaId, $idCliente, $idPet, $servico);
        $stmtVenda->execute();
    }

    $stmtVenda->close();

    // Redirecionar para página de sucesso ou menu
    header("Location: Secretaria.php?sucesso=Venda realizada com sucesso");
    exit();
} else {
    header("Location: Secretaria.php");
    exit();
}
