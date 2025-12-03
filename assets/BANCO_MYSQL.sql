CREATE DATABASE IF NOT EXISTS db_atelie;
USE db_atelie;

CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nm_usuario VARCHAR(100) NOT NULL,
    cpf CHAR(11) NOT NULL UNIQUE,
    dt_nascimento DATE NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    tipo ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS endereco (
    id_endereco INT AUTO_INCREMENT PRIMARY KEY,
    rua VARCHAR(100) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    cep CHAR(8) NOT NULL,
    numero VARCHAR(10),
    complemento VARCHAR(50),
    logradouro VARCHAR(50),
    id_usuario INT NOT NULL,

    CONSTRAINT fk_endereco_usuario
        FOREIGN KEY (id_usuario)
            REFERENCES usuario(id_usuario)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nm_categoria VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS produto (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nm_produto VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao VARCHAR(500),
    tipo ENUM('Pronta entrega', 'Encomenda') NOT NULL,
    qt_tamanho VARCHAR(20),
    id_categoria INT NOT NULL,

    CONSTRAINT fk_produto_categoria
        FOREIGN KEY (id_categoria)
            REFERENCES categoria(id_categoria)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS foto_produto (
    id_foto INT AUTO_INCREMENT PRIMARY KEY,
    id_produto INT NOT NULL,
    caminho_foto VARCHAR(255) NOT NULL,

    CONSTRAINT fk_foto_produto
        FOREIGN KEY (id_produto)
        REFERENCES produto(id_produto)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS carrinho (
    id_carrinho INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    dt_criacao DATE,
    dt_atualizacao DATE,

    CONSTRAINT fk_carrinho_usuario
        FOREIGN KEY (id_usuario)
            REFERENCES usuario(id_usuario)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS item_carrinho (
    id_item_carrinho INT AUTO_INCREMENT PRIMARY KEY,
    id_carrinho INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT DEFAULT 1,
    cor_item VARCHAR(50),
    nm_personagem VARCHAR(255),
    preco_unitario DECIMAL(10,2),
    preco_total DECIMAL(10,2),

    CONSTRAINT fk_item_carrinho_carrinho
        FOREIGN KEY (id_carrinho)
            REFERENCES carrinho(id_carrinho)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

    CONSTRAINT fk_item_carrinho_produto
        FOREIGN KEY (id_produto)
            REFERENCES produto(id_produto)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_produto INT NOT NULL,

    CONSTRAINT fk_favoritos_usuario
        FOREIGN KEY (id_usuario)
            REFERENCES usuario(id_usuario)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

    CONSTRAINT fk_favoritos_produto
        FOREIGN KEY (id_produto)
            REFERENCES produto(id_produto)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    dt_pedido DATETIME NOT NULL,
    vl_total DECIMAL(10,2),
    metodo_pagamento VARCHAR(120),
    status_pedido ENUM('Pendente', 'Em andamento', 'Concluído', 'A caminho', 'Entregue', 'Cancelado') NOT NULL DEFAULT 'Pendente',
    id_usuario INT NOT NULL,

    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (id_usuario)
            REFERENCES usuario(id_usuario)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS item_pedido (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    qt_item INT DEFAULT 1,
    cor_item VARCHAR(50),
    nm_personagem VARCHAR(255),

    CONSTRAINT fk_item_pedido_pedido
        FOREIGN KEY (id_pedido)
            REFERENCES pedido(id_pedido)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

    CONSTRAINT fk_item_pedido_produto
        FOREIGN KEY (id_produto)
            REFERENCES produto(id_produto)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS historico_pedidos (
    id_historico INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    status_anterior VARCHAR(50),
    status_novo VARCHAR(50),
    dt_alteracao DATETIME NOT NULL,

    CONSTRAINT fk_historico_pedido
        FOREIGN KEY (id_pedido)
            REFERENCES pedido(id_pedido)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS avaliacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    estrelas INT NOT NULL,
    comentario TEXT NOT NULL
);

-- ============================
-- USUÁRIOS
-- ============================
INSERT INTO usuario (nm_usuario, cpf, dt_nascimento, telefone, email, senha, tipo, foto)
VALUES
('Egina', '11111111111', '2000-12-29', '11999990000', 'admin@admin.com', '123', 'admin', 'https://images.unsplash.com/photo-1595152772835-219674b2a8a6'),
('Bianca', '22222222222', '2006-05-18', '11988887777', 'bianca@example.com', '123', 'user', 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe'),
('Maria', '33333333333', '1990-08-10', '11977776666', 'maria@example.com', '123', 'user', 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e');

-- ============================
-- ENDEREÇOS
-- ============================
INSERT INTO endereco (rua, bairro, cidade, estado, cep, numero, complemento, logradouro, id_usuario)
VALUES
('Rua Alfa', 'Centro', 'São Paulo', 'SP', '01001000', '100', 'Casa 1', 'Residencial', 1),
('Rua Beta', 'Jardins', 'São Paulo', 'SP', '01415000', '200', NULL, 'Residencial', 2),
('Rua das Flores', 'Vila Nova', 'Santos', 'SP', '11035000', '50', 'Apto 34', 'Residencial', 3);

-- ============================
-- CATEGORIAS
-- ============================
INSERT INTO categoria (nm_categoria)
VALUES
('Crochê'),
('Costura'),
('Bordado'),
('Acessórios Infantis');

-- ============================
-- PRODUTOS
-- ============================
INSERT INTO produto (nm_produto, preco, descricao, tipo, qt_tamanho, id_categoria)
VALUES
('Chaveiro de Crochê – Cacto', 25.00, 'Chaveiro artesanal de crochê em formato de cacto.', 'Pronta entrega', 'Único', 1),
('Boneca Amigurumi – Coelha', 120.00, 'Boneca feita à mão em crochê.', 'Encomenda', '30cm', 1),
('Laço Infantil Rosa', 15.00, 'Laço delicado para crianças.', 'Pronta entrega', 'Único', 4),
('Pano de Prato Bordado – Flores', 18.50, 'Pano de prato com bordado artesanal.', 'Pronta entrega', 'Único', 3),
('Necessaire Costurada Floral', 35.00, 'Necessaire com tecido floral.', 'Pronta entrega', '20x15cm', 2);

-- ============================
-- FOTOS DOS PRODUTOS
-- ============================
INSERT INTO foto_produto (id_produto, caminho_foto)
VALUES
(1, 'https://images.unsplash.com/photo-1503602642458-232111445657'),
(2, 'https://images.unsplash.com/photo-1618518274730-df96c779bd33'),
(3, 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b'),
(4, 'https://images.unsplash.com/photo-1601042874075-7113f63d9c48'),
(5, 'https://images.unsplash.com/photo-1585386959984-a4155223f9a4');

-- ============================
-- CARRINHOS
-- ============================
INSERT INTO carrinho (id_usuario, dt_criacao, dt_atualizacao)
VALUES
(2, '2025-01-10', '2025-01-11'),
(3, '2025-01-12', '2025-01-12');

-- ============================
-- ITENS DO CARRINHO
-- ============================
INSERT INTO item_carrinho (id_carrinho, id_produto, quantidade, cor_item, nm_personagem, preco_unitario, preco_total)
VALUES
(1, 1, 2, 'Verde', NULL, 25.00, 50.00),
(1, 3, 1, 'Rosa', NULL, 15.00, 15.00),
(2, 2, 1, 'Branco', 'Coelha', 120.00, 120.00);

-- ============================
-- FAVORITOS
-- ============================
INSERT INTO favoritos (id_usuario, id_produto)
VALUES
(2, 2),
(2, 5),
(3, 1);

-- ============================
-- PEDIDOS
-- ============================
INSERT INTO pedido (dt_pedido, vl_total, metodo_pagamento, status_pedido, id_usuario)
VALUES
('2025-01-15 14:30:00', 65.00, 'Cartão', 'Concluído', 2),
('2025-01-20 10:10:00', 120.00, 'Pix', 'Em andamento', 3);

-- ============================
-- ITENS DO PEDIDO
-- ============================
INSERT INTO item_pedido (id_pedido, id_produto, qt_item, cor_item, nm_personagem)
VALUES
(1, 1, 2, 'Verde', NULL),
(1, 3, 1, 'Rosa', NULL),
(2, 2, 1, 'Branco', 'Coelha');

-- ============================
-- HISTÓRICO DE PEDIDOS
-- ============================
INSERT INTO historico_pedidos (id_pedido, status_anterior, status_novo, dt_alteracao)
VALUES
(1, 'Pendente', 'Concluído', '2025-01-15 15:00:00'),
(2, 'Pendente', 'Em andamento', '2025-01-20 11:00:00');

-- ============================
-- AVALIAÇÕES
-- ============================
INSERT INTO avaliacao (titulo, estrelas, comentario)
VALUES
('Ótimo produto', 5, 'Amei a qualidade do amigurumi.'),
('Entrega rápida', 4, 'Chegou antes do prazo.'),
('Bom custo-benefício', 4, 'Produto bonito e bem feito.');
