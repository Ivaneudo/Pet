<?php
session_start();
include('../funcoes/conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['tipo_usuario'])) {
    header("Location: ../entrada/Entrar.php");
    exit();
}

$cpf = isset($_GET['cpf']) ? trim($_GET['cpf']) : '';
$petId = isset($_GET['petId']) ? intval($_GET['petId']) : 0;

// Verifica se o usuário é admin ou secretaria
$isAdmin = $_SESSION['tipo_usuario'] === 'admin';
$isSecretaria = $_SESSION['tipo_usuario'] === 'secretaria';

if ($cpf !== '' && $petId > 0) {
    // Inicia a transação
    $conn->begin_transaction();

    try {
        // Deleta da tabela servico usando a coluna correta
        $sqlServico = "DELETE FROM servico WHERE id_pet = ? AND secretaria_id IN (SELECT secretaria_id FROM secretaria WHERE cpf = ?)";
        $stmtServico = $conn->prepare($sqlServico);
        if (!$stmtServico) {
            throw new Exception("Erro ao preparar a consulta (servico): " . $conn->error);
        }
        $stmtServico->bind_param("is", $petId, $cpf);
        $stmtServico->execute();
        $stmtServico->close();

        // Deleta da tabela pet
        $sqlPet = "DELETE FROM pet WHERE id_pet = ? AND cpf_dono = ?";
        $stmtPet = $conn->prepare($sqlPet);
        if (!$stmtPet) {
            throw new Exception("Erro ao preparar a consulta (pet): " . $conn->error);
        }
        $stmtPet->bind_param("is", $petId, $cpf);
        if ($stmtPet->execute()) {
            // Commit da transação
            $conn->commit();
            // Redireciona para a página correta com base no tipo de usuário
            if ($isAdmin) {
                header("Location: ../adm/AdmPet.php?success=" . urlencode("Pet removido com sucesso."));
            } elseif ($isSecretaria) {
                header("Location: ../secretaria/SecretariaPet.php?success=" . urlencode("Pet removido com sucesso."));
            }
        } else {
            throw new Exception("Erro ao executar a exclusão do pet: " . $stmtPet->error);
        }
        $stmtPet->close();
    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        $conn->rollback();
        // Redireciona para a página correta com base no tipo de usuário
        if ($isAdmin) {
            header("Location: ../adm/AdmPet.php?error=" . urlencode($e->getMessage()));
        } elseif ($isSecretaria) {
            header("Location: ../secretaria/SecretariaPet.php?error=" . urlencode($e->getMessage()));
        }
    } finally {
        if (isset($conn)) $conn->close();
    }
} else {
    // Redireciona para a página correta com base no tipo de usuário
    if ($isAdmin) {
        header("Location: ../adm/AdmPet.php?error=" . urlencode("Parâmetros inválidos para exclusão."));
    } elseif ($isSecretaria) {
        header("Location: ../secretaria/SecretariaPet.php?error=" . urlencode("Parâmetros inválidos para exclusão."));
    }
}
exit;