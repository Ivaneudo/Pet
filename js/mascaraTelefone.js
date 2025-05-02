let inputTel = document.querySelector(".Telefone")

inputTel.addEventListener('keypress', () => {
    let inputTelLength = inputTel.value.length

    if (inputTelLength === 0){
	    inputTel.value = '(' + inputTel.value
    }

    if(inputTelLength === 3){
	    inputTel.value += ') '
    }
    
    else if(inputTelLength === 9){
	    inputTel.value += '-'
    }
})
