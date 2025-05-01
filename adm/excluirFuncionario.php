<?php
session_start();
include('../funcoes/conexao.php'); // Inclui a conexão com o banco de dados

// Captura o CPF da URL e sanitiza
$cpf = isset($_GET['cpf']) ? trim($_GET['cpf']) : '';

if (!empty($cpf)) {
    try {
        // Deletar da tabela adm
        $sql1 = "DELETE FROM adm WHERE cpf = ?";
        $stmt1 = $conn->prepare($sql1);
        if (!$stmt1) {
            throw new Exception("Erro ao preparar a consulta (adm): " . $conn->error);
        }
        $stmt1->bind_param("s", $cpf);
        $stmt1->execute();
        $stmt1->close();

        // Deletar da tabela repositor
        $sql2 = "DELETE FROM repositor WHERE cpf = ?";
        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) {
            throw new Exception("Erro ao preparar a consulta (repositor): " . $conn->error);
        }
        $stmt2->bind_param("s", $cpf);
        $stmt2->execute();
        $stmt2->close();

        // Deletar da tabela caixa
        $sql3 = "DELETE FROM caixa WHERE cpf = ?";
        $stmt3 = $conn->prepare($sql3);
        if (!$stmt3) {
            throw new Exception("Erro ao preparar a consulta (caixa): " . $conn->error);
        }
        $stmt3->bind_param("s", $cpf);
        $stmt3->execute();
        $stmt3->close();

        // Redireciona com mensagem de sucesso
        header("Location: AdmFuncionarios.php?success=Funcionário excluído com sucesso.");
        exit();

    } catch (Exception $e) {
        // Redireciona com mensagem de erro
        header("Location: AdmFuncionarios.php?error=" . urlencode($e->getMessage()));
        exit();
    } finally {
        if (isset($conn)) $conn->close();
    }
} else {
    header("Location: AdmFuncionarios.php?error=CPF não fornecido");
    exit();
}
