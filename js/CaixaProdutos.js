const cancelar = document.querySelector('#cancel');
const pontuar = document.querySelector('#pont');

// ! Quando o caixa cancelar a compra, ele irá retornar para a tela incial sem nenhuma mensagem de alerta.
cancelar.addEventListener('click', (e) => {
    e.preventDefault();

    location.href = 'Secretaria.php';
});

// ! Faz o campo do CPF aparecer.
pontuar.addEventListener('click', (e) => {
    e.preventDefault();

    let aparecer = document.querySelector('.Escondido');

     aparecer.classList.remove("Escondido");
     aparecer.classList.add("Aparece");

});