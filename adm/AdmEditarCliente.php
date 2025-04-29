<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caixa Cadastro de Clientes</title>
    <!-- TODO: link do icon -->
     <link rel="shortcut icon" href="../img/Logo-Pethop-250px .ico" type="image/x-icon">
    <!-- TODO: link do css -->
     <link rel="stylesheet" href="../css/principal.css">
     <link rel="stylesheet" href="../css/caixa.css">
     <link rel="stylesheet" href="../css/caixaCadastro.css">
     <link rel="stylesheet" href="../css/AdmFuncionarios.css">
    <!-- TODO: link da mascara -->
     <script src="../js/mascara.js" defer></script>
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
                    <li><a href="AdmNovoFuncionario.php">Novo funcionario</a></li>
                    <li><a href="AdmFuncionarios.php">Funcionarios</a></li>
                    <li><a href="AdmClientes.php">Clientes</a></li>
                </ul>
            </nav>
        </div>

        <div class="cadastrar">
            <div class="cadastro">

                
                <form action="">

                    <div class="cliente">
                        <p>Cliente:</p>
                        <div class="colunas">
                            <div class="coluna">
                                <input
                                type="text"
                                name="nome"
                                class="NomeCliente"
                                placeholder="Nome do cliente: ">
                                <input
                                type="text"
                                id="cpf"
                                maxlength="14"
                                placeholder="Digite o cpf do cliente: ">
                            </div>
                        
                            <div class="coluna">
                                <input
                                type="text"
                                name="Telefone"
                                class="Telefone"
                                maxlength="14"
                                placeholder="Digite o telefone do cliente">
                                <input
                                type="email"
                                name="email"
                                class="Email"
                                placeholder="Digite o e-mail do cliente: ">
                            </div>
                        </div>
                    </div>

                    <p>Dados do pet</p>
                    <div class="animais">

                        
                        <div class="coluna">
                            
                            <div class="AnimalTipo">
                                <input 
                                type="radio"
                                class="tipo"
                                name="animal"
                                value="gato"
                                id="gato">
                                <label for="gato">Gato</label>
                                <input 
                                type="radio"
                                class="tipo"
                                name="animal"
                                value="cachorro"
                                id="cachorro">
                                <label for="cachorro">Cachorro</label>
                            </div>

                            <input
                            type="text"
                            name="nomePet"
                            class="nomePet"
                            placeholder="Digite o nome do animal: ">

                            <input
                            type="text"
                            name="idade"
                            class="idade"
                            placeholder="Digite a idade do animal: ">

                        </div>

                        <div class="coluna">

                            <div class="AnimalTipo">
                                <input 
                                type="radio"
                                class="tipo"
                                name="sexo"
                                value="macho"
                                id="sexo">
                                <label for="Macho">M</label>
                                <input 
                                type="radio"
                                class="tipo"
                                name="sexo"
                                value="femea"
                                id="sexo">
                                <label for="Femea">F</label>
                                <input 
                                type="radio"
                                class="tipo"
                                name="sexo"
                                value="intersexo"
                                id="sexo">
                                <label for="Intersexo">I</label>
                            </div>

                            <input
                            type="text"
                            name="peso"
                            class="peso"
                            placeholder="Digite o peso: ">

                            <input
                            type="text"
                            name="cor"
                            class="cor"
                            placeholder="Digite a cor do animal: ">

                            <select name="raça" id="raça">
                                <!-- Raças de Cachorro -->
                                <option value="">Escolha uma raça</option>
                                <option value="sem-raca-definida">Sem raça definida</option>
                                <option value="labrador">Labrador Retriever</option>
                                <option value="pastor-alemao">Pastor Alemão</option>
                                <option value="poodle">Poodle</option>
                                <option value="bulldog-ingles">Bulldog Inglês</option>
                                <option value="golden-retriever">Golden Retriever</option>
                                <option value="rottweiler">Rottweiler</option>
                                <option value="chihuahua">Chihuahua</option>
                                <option value="beagle">Beagle</option>
                                <option value="shihtzu">Shih Tzu</option>
                                <option value="husky-siberiano">Husky Siberiano</option>

                                <!-- Raças de Gato -->
                                <option value="siames">Siamês</option>
                                <option value="persa">Persa</option>
                                <option value="maine-coon">Maine Coon</option>
                                <option value="ragdoll">Ragdoll</option>
                                <option value="sphynx">Sphynx</option>
                                <option value="bengal">Bengal</option>
                                <option value="britanico-pelo-curto">Britânico de Pelo Curto</option>
                                <option value="abissinio">Abissínio</option>
                                <option value="noruegues-da-floresta">Norueguês da Floresta</option>
                                <option value="azul-russo">Azul Russo</option>
                            </select>
                        </div>
                    </div>

                    <div class="botoes">
                        <div>
                            <button class="voltar" id="volt">Voltar</button>
                        </div>
                        <div>
                            <button id="novo">Proxímo Pet</button>
                            <button id="cade">Modificar</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</body>
</html>