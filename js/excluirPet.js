function confirmarExclusao(cpf, petId,) {
    if (confirm("Tem certeza que deseja remover este pet?")) {
        window.location.href = "excluirPet.php?cpf=" + encodeURIComponent(cpf) + "&petId=" + encodeURIComponent(petId)
    }
}