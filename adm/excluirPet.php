<?php
session_start();
include('../funcoes/conexao.php');

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../entrada/Entrar.php");
    exit();
}

// Captura os parâmetros e sanitiza
$cpf = isset($_GET['cpf']) ? trim($_GET['cpf']) : '';
$petId = isset($_GET['petId']) ? intval($_GET['petId']) : 0;

if ($cpf !== '' && $petId > 0) {
    $sql = "DELETE FROM pet WHERE id_pet = ? AND cpf_dono = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Erro na preparação
        header("Location: AdmPet.php?error=" . urlencode("Erro ao preparar a exclusão: " . $conn->error));
        exit();
    }
    $stmt->bind_param("is", $petId, $cpf);
    if ($stmt->execute()) {
        header("Location: AdmPet.php?success=" . urlencode("Pet removido com sucesso."));
    } else {
        header("Location: AdmPet.php?error=" . urlencode("Erro ao executar a exclusão: " . $stmt->error));
    }
    $stmt->close();
} else {
    header("Location: AdmPet.php?error=" . urlencode("Parâmetros inválidos para exclusão."));
}
exit;
