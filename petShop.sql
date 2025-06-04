CREATE DATABASE petShop;
USE petShop;

CREATE TABLE adm (
    nome VARCHAR(100),
    cpf CHAR(14) NOT NULL UNIQUE,
    telefone CHAR(14), 
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(50) NOT NULL
);

CREATE TABLE cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    cpf CHAR(14) NOT NULL UNIQUE,
    telefone CHAR(14),
    email VARCHAR(100) UNIQUE
);

CREATE TABLE repositor (
    nome VARCHAR(100),
    cpf CHAR(14) NOT NULL UNIQUE,
    telefone CHAR(14),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(50) NOT NULL
);

CREATE TABLE secretaria (
    secretaria_id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    cpf CHAR(14) NOT NULL UNIQUE,
    telefone CHAR(14),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(50) NOT NULL
);

CREATE TABLE produto (
    id_produto INT PRIMARY KEY,
    nome_produto VARCHAR(150),
    estoque INT,
    preco DECIMAL(10, 2) NOT NULL,
    tamanho VARCHAR(50)
);

CREATE TABLE pet (
    id_pet INT PRIMARY KEY AUTO_INCREMENT,
    nome_pet VARCHAR(100) NOT NULL,
    idade INT,
    especie ENUM('Gato', 'Cachorro') NOT NULL,
    sexo ENUM('macho', 'femea', 'intersexo'),
    peso DECIMAL(10, 2),
    raca VARCHAR(100),
    cpf_dono CHAR(14),
    FOREIGN KEY (cpf_dono) REFERENCES cliente(cpf)
);

CREATE TABLE vendas (
    secretaria_id INT,
    id_produto INT,
    id_cliente INT,
    valor_compra DECIMAL(10,2),
    forma_de_pagamento ENUM('Crédito', 'Débito', 'Dinheiro'),
    data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (secretaria_id) REFERENCES secretaria(secretaria_id),
    FOREIGN KEY (id_produto) REFERENCES produto(id_produto),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);

CREATE TABLE servico(
    secretaria_id INT,
    id_pet INT,
    servico ENUM('Banho', 'Tosa', 'Banho e Tosa'),
    valor_servico DECIMAL(10,2),
    data_servico TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    forma_de_pagamento ENUM('Crédito', 'Débito', 'Dinheiro'),
    FOREIGN KEY (secretaria_id) REFERENCES secretaria(secretaria_id),
    FOREIGN KEY (id_pet) REFERENCES pet(id_pet)
);

INSERT INTO adm(nome, cpf, telefone, email, senha) VALUES
('Maria', '123.456.789-10', '(85) 4002-8922', 'maria@gmail.com', '123');

INSERT INTO repositor(nome, cpf, telefone, email, senha) VALUES
('Joao', '111.222.333-44', '(85) 1111-2222', 'joao@gmail.com', '1234');

INSERT INTO secretaria(nome, cpf, telefone, email, senha) VALUES
('Pedro', '555.666.777-88', '(85) 3333-4444', 'pedro@gmail.com', '12345');

INSERT INTO cliente(nome, cpf, email) VALUES
('Ivaneudo', '123.321.132-12', 'ivaneudo@gmail.com'),
('Ananda', '124.421.142-12', 'ananda@gmail.com'),
('Geraldo', '999.999.999-99', 'geraldo@gmail.com');

INSERT INTO pet(nome_pet, idade, especie, cpf_dono, sexo, peso, raca) VALUES
("Feioso", 3, "Gato", "124.421.142-12", "macho", 4.5, "Siamês"),
("Kelly", 2, "Cachorro", "124.421.142-12", "femea", 8.2, "Poodle"),
('Careca', 7, 'Gato', '999.999.999-99', 'intersexo', '6', 'Sphynx');

INSERT INTO produto (id_produto, nome_produto, estoque, preco, tamanho) VALUES
(001, 'Ração Premium Adulto', 50, 30.00, '10kg'),
(002, 'Ração Sabor Frango Filhote', 30, 35.00, '3kg'),
(003, 'Coleira Ajustável Colorida', 30, 10.00, 'M'),
(004, 'Areia Higiênica', 20, 55.00, '5kg'),
(005, 'Brinquedo Mordedor Osso', 15, 20.00, 'p'),
(006, 'Caminha Pelúcia Luxo', 10, 60.00, 'G'),
(007, 'Shampoo Neutro', 15, 25.00, '500ml'),
(008, 'Pote Duplo para Ração e Água', 68, 27.90, 'Único'),
(009, 'Ração Grain Free', 28, 29.90, '7kg'),
(010, 'Arranhador com Sisal', 23, 69.90, '90cm');

select * from servico;

SHOW CREATE TABLE vendas;

ALTER TABLE vendas DROP FOREIGN KEY vendas_ibfk_3;

ALTER TABLE vendas CHANGE id_cliente cpf_cliente CHAR(14);

ALTER TABLE vendas ADD FOREIGN KEY (cpf_cliente) REFERENCES cliente(cpf);

ALTER TABLE vendas DROP FOREIGN KEY vendas_ibfk_2;

ALTER TABLE vendas DROP COLUMN id_produto;

select * from vendas;

ALTER TABLE vendas ADD COLUMN id_venda INT PRIMARY KEY AUTO_INCREMENT;

ALTER TABLE vendas ADD COLUMN id_produto INT;

ALTER TABLE vendas ADD FOREIGN KEY (id_produto) REFERENCES produto(id_produto);

ALTER TABLE vendas ADD COLUMN quant_produto INT;