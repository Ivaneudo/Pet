function confirmDelete(estoqueAtual) {
    var subtrairInput = document.getElementById('subtrairInput').value;
    if (subtrairInput == estoqueAtual) {
        return confirm("Você está prestes a excluir este produto permanentemente. Deseja continuar?");
    }
    return true;
}