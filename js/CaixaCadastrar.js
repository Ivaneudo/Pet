const voltar = document.querySelector('#volt');
const novo = document.querySelector('#novo');
const cadastrar = document.querySelector('#cade');
const voltarAdm = document.querySelector('#voltarAdm');

voltar.addEventListener('click', (e) => {
    e.preventDefault();

    location.href = 'Caixa.html';
});

cadastrar.addEventListener('click', (e) => {
    e.preventDefault();

    location.href = 'Caixa.php';
});

novo.addEventListener('click', (e) => {
    e.preventDefault();

    // ! não sei o que fazer, coloquei só para evitar o envio do fomr mesmo. Acho que esse trabalho é com o back 
});

voltarAdm.addEventListener("click", () => {
    // e.preventDefault()
    console.log('voltar adm')
    window.location.href = 'Adm.php'
});