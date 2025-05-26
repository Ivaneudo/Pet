function confirmarExclusao(cpf, petId, petNome) {
    if (confirm("Tem certeza que deseja remover o pet '" + petNome + "' do dono com CPF " + cpf + "?")) {
        window.location.href = "../funcoes/excluirPet.php?cpf=" + encodeURIComponent(cpf) + "&petId=" + encodeURIComponent(petId);
    }
}
