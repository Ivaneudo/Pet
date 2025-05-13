document.addEventListener('DOMContentLoaded', function() {
    const creditoRadio = document.getElementById('credito')
    const debitoRadio = document.getElementById('debito')
    const parcelasContainer = document.getElementById('parcelasContainer')
    const parcelasSelect = document.getElementById('parcelas')

    function atualizarParcelas() {
        if (creditoRadio.checked) {
            parcelasContainer.style.display = 'block'
            parcelasSelect.setAttribute('required', 'required')
        } else {
            parcelasContainer.style.display = 'none';
            parcelasSelect.removeAttribute('required')
            parcelasSelect.value = ""
        }
    }

    creditoRadio.addEventListener('change', atualizarParcelas)
    debitoRadio.addEventListener('change', atualizarParcelas)

    // Inicializa o estado correto ao carregar a página
    atualizarParcelas()
})