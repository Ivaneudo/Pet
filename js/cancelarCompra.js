function confirmarCancelamento(event) {
    event.preventDefault();
    if (confirm('Você tem certeza que deseja cancelar esta compra?')) {
        // Cria um input hidden para simular o clique no botão cancelar
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cancelar';
        input.value = '1';
        
        // Adiciona ao formulário e submete
        var form = document.getElementById('formCarrinho');
        form.appendChild(input);
        form.submit();
    }
}