<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário é uma secretaria
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
    header("Location: ../entrada/Entrar.php");
    exit();
}

// Verifica se existem dados de pagamento na sessão
if (!isset($_SESSION['dados_pagamento'])) {
    header("Location: SecretariaServiços.php");
    exit();
}

// Captura o nome do funcionário da sessão
$nomeFuncionario = $_SESSION['usuario'];

$pagamentoCancelado = false;
$erro = '';

// Processa envio do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpfAdm = trim($_POST['cpf']);
    $senhaAdm = trim($_POST['senha']);

    // Verifica as credenciais do administrador
    $sql = "SELECT * FROM administradores WHERE cpf = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $cpfAdm, $senhaAdm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Credenciais válidas, cancela o pagamento
        unset($_SESSION['dados_pagamento']);
        $pagamentoCancelado = true;
    } else {
        $erro = "CPF ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cancelar Pagamento</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, por favor, insira as credenciais do administrador para cancelar o pagamento.</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>

        <?php if ($pagamentoCancelado): ?>
            <script>
                alert("Pagamento cancelado.");
                window.location.href = "Secretaria.php";
            </script>
        <?php else: ?>

            <form method="POST" action="">
                <div class="campo">
                    <label for="cpf">CPF do Administrador:</label>
                    <input type="text" name="cpf" id="cpf" required>
                </div>
                <div class="campo">
                    <label for="senha">Senha do Administrador:</label>
                    <input type="password" name="senha" id="senha" required>
                </div>
                <?php if (!empty($erro)): ?>
                    <p style="color:red;"><?php echo $erro; ?></p>
                <?php endif; ?>
                <div class="botoes">
                    <button type="submit">Cancelar Pagamento</button>
                    <button type="button" onclick="window.history.back();">Voltar</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>
