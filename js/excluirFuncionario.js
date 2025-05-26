function excluirFuncionario(cpf) {
    if (confirm("Tem certeza que deseja demitir este funcionário?")) {
        window.location.href = "../funcoes/excluirFuncionario.php?cpf=" + encodeURIComponent(cpf);
    }
}
