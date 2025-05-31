const icons = {
    "estoque": {
        original: "../img/estoque.png",
        hover: "../img/estoque-azul.png"
    },
    "funcionarios": {
        original: "../img/funcionarios.png",
        hover: "../img/funcionarios-azul.png"
    },
    "cliente": {
        original: "../img/cliente.png",
        hover: "../img/cliente-azul.png"
    },
    "patinha": {
        original: "../img/patinha.png",
        hover: "../img/patinha-azul.png"
    },
    "cadastrar produto": {
        original: "../img/cad-produtos.png",
        hover: "../img/cad-produtos-azul.png"
    },
    "editar produto": {
        original: "../img/editar-produto.png",
        hover: "../img/editar-produto-azul.png"
    },
    "excluir estoque": {
        original: "../img/excluir-estoque.png",
        hover: "../img/excluir-estoque-azul.png"
    },
    "caixa":{
        original: "../img/caixa.png",
        hover: "../img/caixa-azul.png"
    }
}

function changeImage(icon, isHover) {
    const img = icon.querySelector('img')
    const iconName = img.alt.toLowerCase()
    img.src = isHover ? icons[iconName].hover : icons[iconName].original
}

const iconElements = document.querySelectorAll('.navbar ul a .icone')

iconElements.forEach(icon => {
    icon.addEventListener('mouseenter', () => changeImage(icon, true)) 
    icon.addEventListener('mouseleave', () => changeImage(icon, false))
})
