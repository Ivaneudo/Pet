<?php
session_start();
include('../funcoes/conexao.php'); // Adicionei esta linha - faltava incluir a conexão

// Captura o CPF da URL e sanitiza
$cpf = isset($_GET['cpf']) ? trim($_GET['cpf']) : '';

if (!empty($cpf)) {
    try {
        // Prepara a consulta com parâmetros seguros
        $sql = "DELETE FROM cliente WHERE cpf = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta: " . $conn->error);
        }
        
        $stmt->bind_param("s", $cpf);
        
        if ($stmt->execute()) {
            // Redireciona com mensagem de sucesso
            header("Location: AdmClientes.php");
        } else {
            throw new Exception("Erro ao executar a exclusão: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Redireciona com mensagem de erro
        header("Location: AdmClientes.php?error=" . urlencode($e->getMessage()));
    } finally {
        // Garante que os recursos sejam liberados
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    header("Location: AdmClientes.php?error=CPF não fornecido");
}
exit();