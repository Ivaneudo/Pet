<?php
    session_start();

    // Opcional: verificar se o usuário está logado como secretaria
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
        header("Location: ../entrada/Entrar.php");
        exit();
    }

    $nomeFuncionario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Operação Concluída</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/sucesso.css">
</head>
<body>
    <div class="container">
        <div class="welcome">
            <img src="../img/Logo-Pethop-250px.png" alt="" />
        </div>

        <h1>Serviço finalizado com sucesso!</h1>
        <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, o pagamento do cliente foi concluído.</p>

        <a href="Secretaria.php" class="btn">Voltar ao Menu</a>
    </div>
</body>
</html>
