<?php 
require_once('../funcoes/conexao.php');
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    
    if (!$conn){
        die("Conexão falhou: " . mysqli_connect_error());
    }
    
    // TODO: Verifica se o CPF e a senha pertencem ao administrador
    $stmt = $conn->prepare("SELECT * FROM adm WHERE cpf = ? AND senha = ?");
    $stmt->bind_param("ss", $cpf, $senha);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0){
        $usuario = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = 'admin';
        header("Location: ../adm/Adm.php");
        exit();
    }
    
    // TODO: Verifica se o CPF e a senha pertencem ao repositor
    $stmt = $conn->prepare("SELECT * FROM repositor WHERE cpf = ? AND senha = ?");
    $stmt->bind_param("ss", $cpf, $senha);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0){
        $usuario = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = 'repositor';
        header("Location: ../repositor/repositor.php");
        exit();
    } 
    
    // TODO: Verifica se o CPF e a senha pertencem ao caixa
    $stmt = $conn->prepare("SELECT * FROM caixa WHERE cpf = ? AND senha = ?");
    $stmt->bind_param("ss", $cpf, $senha);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0){
        $usuario = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = 'caixa';
        header("Location: ../caixa/Caixa.php");
        exit();
    } 
    if ($result->num_rows == 0){
        $message = "Usuário não encontrado.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faça seu Login</title>
    <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/principal.css">
    <link rel="stylesheet" href="../css/entrar.css">
    <script src="../js/mascara.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="entrar">
            <div class="instrucoes">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Adicione seu <strong>CPF</strong> no campo de usuário e sua <strong>senha</strong> no campo de senha. Tenha um bom dia de trabalho.</p>
            </div>
            <div class="form">
                <form method="POST" action="">
                    <?php if (!empty($message)): ?>
                        <div class="message" style="display: flex; font-size: .8em; color: red; margin-left: 4.2rem">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    <input 
                        type="text"
                        id="cpf"
                        name="cpf"
                        maxlength="14"
                        placeholder="Digite seu usuário: " required>
                    <input 
                        type="password"
                        class="senha"
                        name="senha"
                        placeholder="Digite sua senha: " required>
                    <button class="enviar">Entrar</button>                 
                </form>
            </div>
        </div>
    </div>
</body>
</html>