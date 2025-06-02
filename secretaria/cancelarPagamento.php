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
        $sql = "SELECT * FROM adm WHERE cpf = ? AND senha = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $cpfAdm, $senhaAdm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Credenciais válidas, cancela o pagamento
            if (isset($_SESSION['carrinho'])) {
                unset($_SESSION['carrinho']); // Limpa o carrinho de produtos
            }
            unset($_SESSION['dados_pagamento']); // Limpa os dados de pagamento
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
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />    <link rel="stylesheet" href="../css/principal.css" />
    <link rel="stylesheet" href="../css/caixa.css" />
    <link rel="stylesheet" href="../css/caixaCadastro.css" />
    <link rel="stylesheet" href="../css/AdmFuncionarios.css" />
    <script src="../js/mascara.js" defer></script>
    <style>
        .desabilitado{
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="" />
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><img src="../img/sair.svg" alt="imagem de sair"></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="#" class="desabilitado">Menu</a></li>
                    <li><a href="#" class="desabilitado">Caixa</a></li>
                    <li><a href="#" class="desabilitado">Serviço</a></li>
                </ul>
            </nav>
        </div>
        <div class="cadastrar">
            <div class="cadastro">
                
                <?php if ($pagamentoCancelado): ?>
                    <script>
                        alert("Pagamento cancelado.");
                        window.location.href = "Secretaria.php";
                    </script>
                <?php else: ?>

                <form method="POST" action="">
                    <div class="campo">
                        <p for="cpf">CPF do Administrador:</p>
                        <input type="text" 
                        name="cpf" 
                        id="cpf" 
                        maxlength=14 
                        autocomplete=off
                        required>
                    </div>
                    <div class="campo">
                        <p for="senha">Senha do Administrador:</p>
                        <input type="password" 
                        name="senha" 
                        id="senha" 
                        required>
                    </div>
                    <?php if (!empty($erro)): ?>
                        <p style="color:red;"><?php echo $erro; ?></p>
                    <?php endif; ?>
                    <div class="botoes">
                        <button type="button" onclick="window.history.back();" class="voltar" id="volt">Voltar</button>
                        <button type="submit" id="cade">Cancelar Pagamento</button>
                    </div>
                </form>

                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>