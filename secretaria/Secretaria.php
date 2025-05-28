<?php
session_start();

// Verifica se o usuário é uma secretaria
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
    <title>Secretaria</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/caixa.css">
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
                    <li>
                        <a href="SecretariaClientes.php">
                            <div class="icone">
                                <img src="../img/cliente.png" alt="cliente"> <!-- Alterado -->
                                <p>Clientes</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="SecretariaPet.php">
                            <div class="icone">
                                <img src="../img/patinha.png" alt="patinha"> <!-- Alterado -->
                                <p>Pets</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="SecretariaProdutos.php">
                            <div class="icone">
                                <img src="../img/estoque.png" alt="estoque"> <!-- Alterado -->
                                <p>Produtos</p>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="SecretariaServicos.php">
                            <div class="icone">
                                <img src="../img/caixa.png" alt="caixa"> <!-- Alterado -->
                                <p>Serviços</p>
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <script>
        // Mapeia as imagens originais e as imagens de hover
        const icons = {
            "cliente": {
                original: "../img/cliente.png",
                hover: "../img/cliente-azul.png"
            },
            "patinha": {
                original: "../img/patinha.png",
                hover: "../img/patinha-azul.png"
            },
            "estoque": {
                original: "../img/estoque.png",
                hover: "../img/estoque-azul.png"
            },
            "caixa": {
                original: "../img/caixa.png",
                hover: "../img/caixa-azul.png"
            }
        };

        // Função para mudar a imagem ao passar o mouse
        function changeImage(icon, isHover) {
            const img = icon.querySelector('img');
            const iconName = img.alt.toLowerCase(); // Obtém o nome do ícone
            img.src = isHover ? icons[iconName].hover : icons[iconName].original; // Troca a imagem
        }

        // Seleciona todos os ícones
        const iconElements = document.querySelectorAll('.navbar ul a .icone');

        // Adiciona eventos de mouse
        iconElements.forEach(icon => {
            icon.addEventListener('mouseenter', () => changeImage(icon, true)); // Muda para a imagem de hover
            icon.addEventListener('mouseleave', () => changeImage(icon, false)); // Retorna para a imagem original
        });
    </script>
</body>
</html>
