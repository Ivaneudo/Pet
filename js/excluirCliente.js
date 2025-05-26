function confirmarExclusao(cpf, redirectPage) {
    if (confirm("Tem certeza que deseja excluir este cliente?")) {
        let basePath = '../funcoes/excluirCliente.php';
        
        window.location.href = basePath + "?cpf=" + encodeURIComponent(cpf) + "&redirect=" + encodeURIComponent(redirectPage);
    }
}
