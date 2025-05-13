document.addEventListener('DOMContentLoaded', function () {
    const valorTotal = parseFloat(document.getElementById('valor').dataset.valor);
    const inputValorPago = document.getElementById('valor_pago');
    const inputTroco = document.getElementById('troco');

    inputValorPago.addEventListener('input', function () {
        let valorPago = parseFloat(this.value);
        if (isNaN(valorPago)) {
            inputTroco.value = '';
            return;
        }
        let troco = valorPago - valorTotal;
        if (troco < 0) {
            inputTroco.value = 'Valor insuficiente';
        } else {
            // Formata em R$ 0,00
            inputTroco.value = 'R$ ' + troco.toFixed(2).replace('.', ',');
        }
    });
});