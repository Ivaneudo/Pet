function buscarCliente() {
    const cpf = document.getElementById('cpf').value;
    if (cpf.length === 14) { // Verifica se o CPF tem o tamanho correto
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'buscar_cliente.php?cpf=' + encodeURIComponent(cpf), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const cliente = JSON.parse(xhr.responseText);
                if (cliente) {
                    document.getElementById('resultado-nome').innerText = cliente.nome;
                } else {
                    document.getElementById('resultado-nome').innerText = 'Cliente não encontrado.';
                }
            }
        };
        xhr.send();
    } else {
        document.getElementById('resultado-nome').innerText = '';
    }
}