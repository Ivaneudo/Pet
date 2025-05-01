document.addEventListener('DOMContentLoaded', function () {
    const especieRadios = document.querySelectorAll('input[name="especie"]')
    const racaSelect = document.getElementById('raca')

    const racasCachorro = [
        {value: 'sem-raca-definida', text: 'Sem raça definida'},
        {value: 'labrador', text: 'Labrador Retriever'},
        {value: 'pastor-alemao', text: 'Pastor Alemão'},
        {value: 'poodle', text: 'Poodle'},
        {value: 'bulldog-ingles', text: 'Bulldog Inglês'},
        {value: 'golden-retriever', text: 'Golden Retriever'},
        {value: 'rottweiler', text: 'Rottweiler'},
        {value: 'chihuahua', text: 'Chihuahua'},
        {value: 'beagle', text: 'Beagle'},
        {value: 'shihtzu', text: 'Shih Tzu'},
        {value: 'husky-siberiano', text: 'Husky Siberiano'}
    ]

    const racasGato = [
        {value: 'siames', text: 'Siamês'},
        {value: 'persa', text: 'Persa'},
        {value: 'maine-coon', text: 'Maine Coon'},
        {value: 'ragdoll', text: 'Ragdoll'},
        {value: 'sphynx', text: 'Sphynx'},
        {value: 'bengal', text: 'Bengal'},
        {value: 'britanico-pelo-curto', text: 'Britânico de Pelo Curto'},
        {value: 'abissinio', text: 'Abissínio'},
        {value: 'noruegues-da-floresta', text: 'Norueguês da Floresta'},
        {value: 'azul-russo', text: 'Azul Russo'}
    ]

    function popularRacas(especieSelecionada, racaAtual) {
        // Limpa as opções existentes
        racaSelect.innerHTML = ''

        // Cria opção padrão
        const optionPadrao = document.createElement('option')
        optionPadrao.value = ''
        optionPadrao.textContent = 'Escolha uma raça'
        optionPadrao.disabled = true
        optionPadrao.selected = true
        racaSelect.appendChild(optionPadrao)

        let racasParaUsar = []
        if (especieSelecionada === 'cachorro') {
            racasParaUsar = racasCachorro
        } else if (especieSelecionada === 'gato') {
            racasParaUsar = racasGato
        } 

        racasParaUsar.forEach(function (r) {
            const option = document.createElement('option')
            option.value = r.value
            option.textContent = r.text
            if (r.value === racaAtual) {
                option.selected = true;
            }
            racaSelect.appendChild(option)
        });
    }

    function getEspecieSelecionada() {
        for (const radio of especieRadios) {
            if (radio.checked) {
                return radio.value
            }
        }
        return null;
    }

    // Inicializa o select com base na espécie atual do pet carregado
    const especieInicial = getEspecieSelecionada();
    const racaAtual = window.racaAtual || ''
    if (especieInicial) {
        popularRacas(especieInicial, racaAtual)
    }

    // Adiciona evento para quando mudar a espécie
    especieRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            popularRacas(this.value, '')
        })
    })
})