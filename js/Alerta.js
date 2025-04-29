const enviar = document.querySelector('.enviar')

enviar.addEventListener('click', (e) => {
    e.preventDefault()

    let cpf = document.querySelector('#cpf').value
    let senha = document.querySelector('.senha').value
    let p = document.querySelector('#p')

    if (cpf == "" || senha == "") {
        p.classList.add('alert')
        p.textContent = 'Por Favor, preencha os campos corretamente.'
    } else if (cpf != '123.456.789-12' || senha != '123') {
        p.classList.add('alert')
        p.textContent = 'CPF ou SENHA incorretos.'
    } else {
        location.href = '../caixa/Caixa.php'
    }
})