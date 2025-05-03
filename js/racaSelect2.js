document.addEventListener('DOMContentLoaded', function() {
    const gatoRacas = [
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
    ];
    const cachorroRacas = [
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
    ];

    const especieRadios = document.querySelectorAll('input[name="animal"]');
    const racaSelect = document.getElementById('raca');

    function preencherRacas(racas) {
        racaSelect.innerHTML = '<option value="">Escolha uma raça</option>';
        racas.forEach(raca => {
            const option = document.createElement('option');
            option.value = raca.value;
            option.textContent = raca.text;
            racaSelect.appendChild(option);
        });
    }

    especieRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'gato') {
                preencherRacas(gatoRacas);
            } else if (this.value === 'cachorro') {
                preencherRacas(cachorroRacas);
            }
        });
    });

    // Caso queira pré-selecionar uma espécie e atualizar as raças...
    // Exemplo: selecionar gato automaticamente e preencher raças gato:
    // especieRadios[0].checked = true;
    // preencherRacas(gatoRacas);
});