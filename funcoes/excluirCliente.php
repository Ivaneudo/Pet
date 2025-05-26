<?php
session_start();
include('../funcoes/conexao.php');

// Captura o CPF da URL e sanitiza
$cpf = isset($_GET['cpf']) ? trim($_GET['cpf']) : '';

// Verifica o tipo de usuário na sessão
$tipoUsuario = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'admin'; // Padrão para admin

if (!empty($cpf)) {
    try {
        // Inicia a transação
        $conn->begin_transaction();

        // Exclui registros do cliente na tabela vendas
        $sqlDeleteVendas = "DELETE FROM vendas WHERE cpf_cliente = ?";
        $stmtDeleteVendas = $conn->prepare($sqlDeleteVendas);
        $stmtDeleteVendas->bind_param("s", $cpf);
        $stmtDeleteVendas->execute();
        $stmtDeleteVendas->close();

        // Exclui registros do cliente na tabela pet
        $sqlDeletePets = "DELETE FROM pet WHERE cpf_dono = ?";
        $stmtDeletePets = $conn->prepare($sqlDeletePets);
        $stmtDeletePets->bind_param("s", $cpf);
        $stmtDeletePets->execute();
        $stmtDeletePets->close();

        // Exclui o cliente
        $sql = "DELETE FROM cliente WHERE cpf = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cpf);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao executar a exclusão: " . $stmt->error);
        }

        // Commit da transação
        $conn->commit();

        // Redireciona com mensagem de sucesso
        if ($tipoUsuario === 'admin') {
            header("Location: ../adm/AdmClientes.php"); // Redireciona para a página de administração
        } elseif ($tipoUsuario === 'secretaria') {
            header("Location: ../secretaria/SecretariaClientes.php"); // Redireciona para a página da secretaria
        } else {
            header("Location: ../adm/AdmClientes.php"); // Redireciona para a página padrão
        }
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $conn->rollback();
        // Redireciona com mensagem de erro
        if ($tipoUsuario === 'admin') {
            header("Location: ../adm/AdmClientes.php?error=" . urlencode($e->getMessage())); // Redireciona para a página de administração
        } elseif ($tipoUsuario === 'secretaria') {
            header("Location: ../secretaria/SecretariaClientes.php?error=" . urlencode($e->getMessage())); // Redireciona para a página da secretaria
        } else {
            header("Location: ../adm/AdmClientes.php?error=" . urlencode($e->getMessage())); // Redireciona para a página padrão
        }
    } finally {
        // Garante que os recursos sejam liberados
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
} else {
    if ($tipoUsuario === 'admin') {
        header("Location: ../adm/AdmClientes.php?error=CPF não fornecido"); // Redireciona para a página de administração
    } elseif ($tipoUsuario === 'secretaria') {
        header("Location: ../secretaria/SecretariaClientes.php?error=CPF não fornecido"); // Redireciona para a página da secretaria
    } else {
        header("Location: ../adm/AdmClientes.php?error=CPF não fornecido"); // Redireciona para a página padrão
    }
}
exit();