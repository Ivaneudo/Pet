<?php
session_start();
include('../entrada/conexao.php');

// Verifica se o CPF foi passado
if (isset($_GET['cpf'])) {
    $cpf = $_GET['cpf'];

    // Prepara a consulta para buscar o cliente
    $sql = "SELECT nome FROM cliente WHERE cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o cliente foi encontrado
    if ($result->num_rows > 0) {
        $cliente = $result->fetch_assoc();
        echo json_encode($cliente); // Retorna os dados do cliente em formato JSON
    } else {
        echo json_encode(null); // Retorna null se não encontrar o cliente
    }

    $stmt->close();
}
$conn->close();
?>