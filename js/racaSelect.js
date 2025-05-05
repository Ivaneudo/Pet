document.addEventListener('DOMContentLoaded', function () {
    const especieRadios = document.querySelectorAll('input[name="animal"]')
    const racaSelect = document.getElementById('raca')

    const racasCachorro = [
        {value: 'sem-raca-definida', text: 'Sem raça definida'},
        {value: 'vira-lata', text: 'Vira Lata'},
        {value: 'chow-chow', text: 'Chow-Chow'},
        {value: 'bulldog', text: 'Bulldog'},
        {value: 'golden-retriever', text: 'Golden Retriever'},
        {value: 'husky-siberiano', text: 'Husky Siberiano'},
        {value: 'labrador', text: 'Labrador'},
        {value: 'maltes', text: 'Maltês'},
        {value: 'pastor-alemao', text: 'Pastor Alemão'},
        {value: 'pincher', text: 'Pincher'},
        {value: 'pit-bull', text: 'Pit-Bull'},
        {value: 'poodle', text: 'Poodle'},
        {value: 'rottweiler', text: 'Rottweiler'},
        {value: 'salsicha', text: 'Salsicha'},
        {value: 'shihtzu', text: 'Shih Tzu'},
        {value: 'outra', text: 'Outra'}
    ]
    
    const racasGato = [
        {value: 'sem-raca-definida', text: 'Sem raça definida'},
        {value: 'siames', text: 'Siamês'},
        {value: 'persa', text: 'Persa'},
        {value: 'sphynx', text: 'Sphynx'},
        {value: 'tricolor', text: 'Tricolor'},
        {value: 'tigrado', text: 'Tigrado'},
        {value: 'listrado', text: 'Listrado'},
        {value: 'frajola', text: 'Frajola'},
        {value: 'preto', text: 'Preto'},
        {value: 'branco', text: 'Branco'},
        {value: 'laranja', text: 'Laranja'},
        {value: 'cinza', text: 'Cinza'},
        {value: 'marrom', text: 'Marrom'},
        {value: 'outra', text: 'Outra'}
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
        if (especieSelecionada === 'cachorro') {
            racasParaUsar = racasCachorro
        } else if (especieSelecionada === 'gato') {
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