function confirmarExclusao(cpf) {
    if (confirm("Tem certeza que deseja excluir este cliente?")) {
        window.location.href = "excluirCliente.php?cpf=" + cpf;
    }
}