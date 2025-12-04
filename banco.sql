CREATE DATABASE IF NOT EXISTS db_atelie;
USE db_atelie;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `avaliacao` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `estrelas` int(11) NOT NULL,
  `comentario` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `carrinho` (
  `id_carrinho` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nm_categoria` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categoria` (`id_categoria`, `nm_categoria`) VALUES
(1, 'Mochilas'),
(2, 'Lancheiras'),
(3, 'Estojos'),
(4, 'Lembrancinhas'),
(5, 'Maternidade');

CREATE TABLE `endereco` (
  `id_endereco` int(11) NOT NULL,
  `rua` varchar(100) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` char(2) NOT NULL,
  `cep` char(8) NOT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `logradouro` varchar(50) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `endereco` (`id_endereco`, `rua`, `bairro`, `cidade`, `estado`, `cep`, `numero`, `complemento`, `logradouro`, `id_usuario`) VALUES
(1, 'rua ernesto jose guerra', 'Vila sonia', 'praia grande', 'sa', '11722010', '315', NULL, NULL, 2);

CREATE TABLE `favoritos` (
  `id_favorito` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `favoritos` (`id_favorito`, `id_usuario`, `id_produto`) VALUES
(1, 2, 1),
(2, 3, 2),
(3, 4, 3),
(4, 5, 4);

CREATE TABLE `historico_pedidos` (
  `id_historico` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `status_anterior` varchar(50) DEFAULT NULL,
  `status_novo` varchar(50) DEFAULT NULL,
  `dt_alteracao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `item_carrinho` (
  `id_item_carrinho` int(11) NOT NULL,
  `id_carrinho` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT 1,
  `cor_item` varchar(50) DEFAULT NULL,
  `nm_personagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `item_pedido` (
  `id_item` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL COMMENT 'preço no momento do pedido',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'preço_unitário x qtde',
  `qt_item` int(11) DEFAULT 1,
  `cor_item` varchar(50) DEFAULT NULL,
  `nm_personagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `dt_pedido` datetime NOT NULL,
  `vl_total` decimal(10,2) DEFAULT NULL,
  `metodo_pagamento` varchar(120) DEFAULT NULL,
  `status_pedido` enum('Pendente','Em andamento','Concluído','A caminho','Entregue','Cancelado') NOT NULL DEFAULT 'Pendente',
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `produto` (
  `id_produto` int(11) NOT NULL,
  `nm_produto` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `descricao` varchar(500) DEFAULT NULL,
  `foto_produto` varchar(255) DEFAULT NULL,
  `tipo` enum('Pronta entrega','Encomenda') NOT NULL,
  `qt_tamanho` varchar(20) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produto` (`id_produto`, `nm_produto`, `preco`, `descricao`, `foto_produto`, `tipo`, `qt_tamanho`, `id_categoria`) VALUES
(1, 'Mochila lilo P', 21.00, 'Mochila lilo P feita com tecido oxford', 'images/692fc7d115b9c.jpeg', 'Pronta entrega', '30x30', 1),
(2, 'mochila lilo G', 23.00, 'Mochila lilo G feita com tecido oxford', 'images/692fc90bcef934.jpeg', 'Encomenda', '2', 1),
(3, 'Lancheira M', 21.00, 'Lancheira M feita com tecido oxford', 'images/692fc90bcef928.jpeg', 'Pronta entrega', '40x30', 2),
(4, 'Lancheira G', 23.00, 'Lancheira G feita com tecido oxford', 'images/692fc90bceda3.jpeg', 'Encomenda', '2', 2),
(5, 'Estojo Kipiling', 21.00, 'Estojo kipling feito com tecido oxford', 'images/692fc88e28cd5.jpeg', 'Pronta entrega', '30x40', 3),
(6, 'Mochila escolar', 23.00, 'Mochila escolar sem rodinhas feita com tecido oxford', 'images/692fc90bce567.png', 'Encomenda', '35x25', 1),
(7, 'Mochila escolar com rodinhas', 21.00, 'Mochila escolar com rodinhas feita com tecido oxford', 'images/692fc90bce657.png', 'Pronta entrega', '35x25', 1),
(8, 'Estojo escolar', 23.00, 'Estojo normal escolar feito com tecido oxford', 'images/692fc90bcef545.jpeg', 'Encomenda', '10x15', 3),
(9, 'Maletinha', 23.00, 'maletinha para lembrancinhas de festa feita com tecido oxford', 'images/692fc90bced98.png', 'Encomenda', '20x30', 4),
(10, 'Mochilete', 21.00, 'mochilete para lembrancinhas de festa feita com tecido oxford', 'images/692fc90bcegf5.png', 'Pronta entrega', '40x20', 4),
(11, 'Bolsa Maternidade G', 23.00, 'Bolsa G para a maternidade feita com tecido oxford', 'images/692fc8d4ea20f.png', 'Encomenda', '40x50', 5),
(12, 'Bolsa Maternidade M', 23.00, 'Bolsa M para maternidade feita com tecido oxford', 'images/692fc8d4ea20f.png', 'Encomenda', '30x40', 5);

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nm_usuario` varchar(100) NOT NULL,
  `cpf` char(11) NOT NULL,
  `dt_nascimento` date NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tipo` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuario` (`id_usuario`, `nm_usuario`, `cpf`, `dt_nascimento`, `telefone`, `email`, `senha`, `foto`, `tipo`) VALUES
(1, 'adm', '45099878909', '0000-00-00', '13991690634', 'adm@adm.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', NULL, 'admin'),

(2, 'Maria Vitoria Lopes', '50233057839', '2007-07-12', '13991690634', 'lopesmariavitor4a@gmail.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', 'images/users/user_675060f1c2a8b6.14839277.jpg', 'user'),

(3, 'Maria CLara Magalhães', '54423524899', '2006-10-21', '13991690634', 'mariacmagalhaess@hotmail.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', 'images/users/user_675060f1c2b9e4.90311266.png', 'user'),

(4, 'Bianca Agante', '64479856355', '2006-10-21', '13991690634', 'biancagante@gmail.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', 'images/users/user_675060f1c2a8b6.14839255.jpg', 'user'),

(5, 'Guilherme Saltão', '94463548699', '2006-10-21', '13991690634', 'guisaltao@gmail.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', 'images/users/user_675060f1c2a8b6.14839555.jpg', 'user'),

(6, 'Milena Takahashi', '97753684299', '2006-10-21', '13991690634', 'mitakahashi@gmail.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', 'images/users/user_675060f1c2b9e4.90311254.png', 'user');


ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`id_carrinho`),
  ADD KEY `fk_carrinho_usuario` (`id_usuario`);

ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nm_categoria` (`nm_categoria`);

ALTER TABLE `endereco`
  ADD PRIMARY KEY (`id_endereco`),
  ADD KEY `fk_endereco_usuario` (`id_usuario`);

ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id_favorito`),
  ADD KEY `fk_favoritos_usuario` (`id_usuario`),
  ADD KEY `fk_favoritos_produto` (`id_produto`);

ALTER TABLE `historico_pedidos`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `fk_historico_pedido` (`id_pedido`);

ALTER TABLE `item_carrinho`
  ADD PRIMARY KEY (`id_item_carrinho`),
  ADD KEY `fk_item_carrinho_carrinho` (`id_carrinho`),
  ADD KEY `fk_item_carrinho_produto` (`id_produto`);

ALTER TABLE `item_pedido`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `fk_item_pedido_pedido` (`id_pedido`),
  ADD KEY `fk_item_pedido_produto` (`id_produto`);

ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `fk_pedido_usuario` (`id_usuario`);

ALTER TABLE `produto`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `fk_produto_categoria` (`id_categoria`);

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `avaliacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `carrinho`
  MODIFY `id_carrinho` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `endereco`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `favoritos`
  MODIFY `id_favorito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `historico_pedidos`
  MODIFY `id_historico` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `item_carrinho`
  MODIFY `id_item_carrinho` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `item_pedido`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `produto`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3561;


ALTER TABLE `carrinho`
  ADD CONSTRAINT `fk_carrinho_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `endereco`
  ADD CONSTRAINT `fk_endereco_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `favoritos`
  ADD CONSTRAINT `fk_favoritos_produto` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id_produto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favoritos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `historico_pedidos`
  ADD CONSTRAINT `fk_historico_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `item_carrinho`
  ADD CONSTRAINT `fk_item_carrinho_carrinho` FOREIGN KEY (`id_carrinho`) REFERENCES `carrinho` (`id_carrinho`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_carrinho_produto` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id_produto`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `item_pedido`
  ADD CONSTRAINT `fk_item_pedido_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_pedido_produto` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id_produto`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `pedido`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `produto`
  ADD CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;
