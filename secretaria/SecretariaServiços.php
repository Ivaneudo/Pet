<?php
    session_start();

    // Verifica se o usuário é um secretaria
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'secretaria') {
        header("Location: ../entrada/Entrar.php"); // Redireciona se não for secretaria
        exit();
    }

    // Captura o nome do funcionário da sessão
    $nomeFuncionario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços</title>
    <!-- TODO: link do ico -->
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <!-- TODO: link do css -->
     <link rel="stylesheet" href="../css/principal.css">
     <link rel="stylesheet" href="../css/caixa.css">
     <link rel="stylesheet" href="../css/CaixaServico.css">
    <!-- TODO: link da mascara -->
     <script src="../js/mascara.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Olá <span id="colaborador"><?php echo htmlspecialchars($nomeFuncionario); ?></span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <a href="../funcoes/logout.php"><p>sair</p></a>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="Secretaria.php">Menu</a></li>
                    <li><a href="SecretariaVendas.php">Caixa</a></li>
                    <li><a href="SecretariaServiços.php">Serviço</a></li>
                </ul>
            </nav>
        </div>
        <div class="servico">
            <div class="servi">
                <div class="pesquisa">
                    <div class="campo">
                        <input
                        type="text"
                        placeholder="CPF do dono"
                        id="cpf"
                        maxlength="14">
                        <img src="../img/search-svgrepo-com.svg" alt="">
                    </div>
                </div>
                <div class="dadosPet">
                    <table>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Sexo</th>
                            <th>Selecionar</th>
                        </tr>
                        <tr>
                            <td>Skol</td>
                            <td>Gato</td>
                            <td>Macho</td>
                            <td>
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="Servico">
                    <select name="servico" id="" aria-placeholder="Tipo de serviço">
                        <!-- TODO: o tipo de serviço que será feito-->
                        <option value="" disabled selected>Tipo de serviço</option>
                        <option value="b">Banho</option>
                        <option value="t">Tosa</option>
                        <option value="bt">Banho e Tosa</option>
                    </select>
                </div>
                <div class="botoes">
                    <div>
                        <button class="voltar" id="cancel">Cancelar Serviço</button>
                    </div>
                    <div>
                        <button>Finalizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>