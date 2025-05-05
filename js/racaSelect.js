document.addEventListener('DOMContentLoaded', function () {
    const especieRadios = document.querySelectorAll('input[name="animal"]')
    const racaSelect = document.getElementById('raca')

    const racasCachorro = [
        {value: 'Sem raça definida', text: 'Sem raça definida'},
        {value: 'Vira lata', text: 'Vira Lata'},
        {value: 'Chow-Chow', text: 'Chow-Chow'},
        {value: 'Bulldog', text: 'Bulldog'},
        {value: 'Golden Retriever', text: 'Golden Retriever'},
        {value: 'Husky Siberiano', text: 'Husky Siberiano'},
        {value: 'Labrador', text: 'Labrador'},
        {value: 'Maltês', text: 'Maltês'},
        {value: 'Pastor Alemão', text: 'Pastor Alemão'},
        {value: 'Pincher', text: 'Pincher'},
        {value: 'Pit-Bull', text: 'Pit-Bull'},
        {value: 'Poodle', text: 'Poodle'},
        {value: 'Rottweiler', text: 'Rottweiler'},
        {value: 'Salsicha', text: 'Salsicha'},
        {value: 'Shihtzu', text: 'Shih Tzu'},
        {value: 'Outra', text: 'Outra'}
    ]
    
    const racasGato = [
        {value: 'Sem raça definida', text: 'Sem raça definida'},
        {value: 'Siamês', text: 'Siamês'},
        {value: 'Persa', text: 'Persa'},
        {value: 'Sphynx', text: 'Sphynx'},
        {value: 'Tricolor', text: 'Tricolor'},
        {value: 'Tigrado', text: 'Tigrado'},
        {value: 'Listrado', text: 'Listrado'},
        {value: 'Frajola', text: 'Frajola'},
        {value: 'Preto', text: 'Preto'},
        {value: 'Branco', text: 'Branco'},
        {value: 'Laranja', text: 'Laranja'},
        {value: 'Cinza', text: 'Cinza'},
        {value: 'Marrom', text: 'Marrom'},
        {value: 'Outra', text: 'Outra'}
    ]

    function popularRacas(especieSelecionada) {
        // Limpa as opções existentes
        racaSelect.innerHTML = ''

        // Cria opção padrão
        const optionPadrao = document.createElement('option')
        optionPadrao.value = ''
        optionPadrao.textContent = 'Escolha uma raça'
        optionPadrao.disabled = true
        optionPadrao.selected = true
        racaSelect.appendChild(optionPadrao)

        let racasParaUsar = [];
        if (especieSelecionada == 'Cachorro') {
            racasParaUsar = racasCachorro
        } else if (especieSelecionada == 'Gato') {
            racasParaUsar = racasGato
        } 

        racasParaUsar.forEach(function (r) {
            const option = document.createElement('option')
            option.value = r.value
            option.textContent = r.text
            racaSelect.appendChild(option)
        });
    }

    // Adiciona evento para quando mudar a espécie
    especieRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            popularRacas(this.value)
        })
    })
})