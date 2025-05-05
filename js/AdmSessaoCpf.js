function SessaoCpf(cpf) {
    let xmlh = new XMLHttpRequest();
    xmlh.open("POST", "../funcoes/SessaoCpf.php", true);
    xmlh.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlh.onreadystatechange = function () {
        if (xmlh.readyState === 4 && xmlh.status === 200) {
            window.location.href = 'AdmEditarCliente.php'
        }
    };
    xmlh.send("cpf=" + encodeURIComponent(cpf))
}