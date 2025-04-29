<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa</title>
    <!-- TODO: link do ico -->
     <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <!-- TODO: link do css -->
     <link rel="stylesheet" href="../css/principal.css">
     <link rel="stylesheet" href="../css/caixa.css">
     <link rel="stylesheet" href="../css/CaixaPagamento.css">
</head>
<body>
    <div class="container">
        <div class="funcionario">
            <div class="funci">
                <img src="../img/Logo-Pethop-250px.png" alt="">
                <p>Olá <span id="colaborador">[nome do funcionário]</span>, bem vindo a mais um dia de trabalho!</p>
            </div>
            <div class="sair">
                <p>sair</p>
            </div>
        </div>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="CaixaCadastrar.php">Cadastrar</a></li>
                    <li><a href="CaixaProdutos.php">Produtos</a></li>
                    <li><a href="CaixaServiços.php">Serviço</a></li>
                </ul>
            </nav>
        </div>
        <div class="pagamento">
            <div class="pag">
                <div class="CardDin">
                    <nav>
                        <a href="#" id="selec">Cartão</a>
                        <a href="CaixaPagamentoDinheiro.php">Dinheiro</a>
                    </nav>
                </div>
                <form action="">
                    <div class="CredDeb">
                        <input type="radio" name="cartao" id="credito">
                        <label for="credito">Credito</label>
                        <input type="radio" name="cartao" id="debito">
                        <label for="debito">Debito</label>
                    </div>
                    <div class="lin">
                        <div class="PrimLin">
                            <div class="bandeira">
                                <select >
                                    <option value="" disabled selected>Bandeira do Cartão</option>
                                    <option value="Mastercard">Mastercard</option>
                                    <option value="Visa">Visa</option>
                                    <option value="Elo">Elo</option>
                                    <option value="AmericanExpress">American Express</option>
                                    <option value="Hipercard">Hipercard</option>
                                </select>
                            </div>
                            <div class="valor">
                                <input type="text" placeholder="Valor: ">
                            </div>
                            <div class="vezes">
                                <select name="" id="">
                                    <option value="" disabled selected>Vezes</option>
                                    <option value="">1</option>
                                    <option value="">2</option>
                                    <option value="">3</option>
                                    <option value="">5</option>
                                    <option value="">6</option>
                                </select>
                            </div>
                        </div>
                        <div class="SecundLin">
                            <div class="autorizacao">
                                <input type="text" placeholder="Autorização">
                            </div>
                            <div class="DigitorCard">
                                <input type="text" placeholder="4 ultimos digitos do cartão">
                            </div>
                        </div>
                    </div>
                    <div class="botoes">
                        <div>
                            <button class="voltar" id="volt">Cancelar</button>
                        </div>
                        <div>
                            <button id="cade">Finalizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>