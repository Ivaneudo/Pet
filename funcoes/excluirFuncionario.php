<?php
session_start();
include('../funcoes/conexao.php');

$cpf = isset($_GET['cpf']) ? trim($_GET['cpf']) : '';

if (!empty($cpf)) {
    try {
        // Deleta da tabela adm
        $sql1 = "DELETE FROM adm WHERE cpf = ?";
        $stmt1 = $conn->prepare($sql1);
        if (!$stmt1) {
            throw new Exception("Erro ao preparar a consulta (adm): " . $conn->error);
        }
        $stmt1->bind_param("s", $cpf);
        $stmt1->execute();
        $stmt1->close();

        // Deleta da tabela repositor
        $sql2 = "DELETE FROM repositor WHERE cpf = ?";
        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) {
            throw new Exception("Erro ao preparar a consulta (repositor): " . $conn->error);
        }
        $stmt2->bind_param("s", $cpf);
        $stmt2->execute();
        $stmt2->close();

        // Deleta da tabela secretaria
        $sql3 = "DELETE FROM secretaria WHERE cpf = ?";
        $stmt3 = $conn->prepare($sql3);
        if (!$stmt3) {
            throw new Exception("Erro ao preparar a consulta (secretaria): " . $conn->error);
        }
        $stmt3->bind_param("s", $cpf);
        $stmt3->execute();
        $stmt3->close();

        // Redireciona com mensagem de sucesso
        header("Location: ../adm/AdmFuncionarios.php?success=Funcionário excluído com sucesso.");
        exit();

    } catch (Exception $e) {
        // Redireciona com mensagem de erro
        header("Location: ../adm/AdmFuncionarios.php?error=" . urlencode($e->getMessage()));
        exit();
    } finally {
        if (isset($conn)) $conn->close();
    }
} else {
    header("Location: ../adm/AdmFuncionarios.php?error=CPF não fornecido");
    exit();
}