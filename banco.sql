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
(1, 'seila');

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
(1, 'rua ernesto jose guerra', 'Vila sonia', 'praia grande', 'sa', '11722010', '315', NULL, NULL, 3549);

CREATE TABLE `favoritos` (
  `id_favorito` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `favoritos` (`id_favorito`, `id_usuario`, `id_produto`) VALUES
(1, 3556, 1),
(2, 3556, 2);

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
(1, 'mochila', 21.00, 'seila', NULL, 'Pronta entrega', '21', 1),
(2, 'chaveiro', 23.00, 'wedewrf', NULL, 'Encomenda', '2', 1);

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
(3, 'adm', '12345678909', '2007-07-12', '13991690634', 'adm@adm.com', 'adm', NULL, 'admin'),
(20, 'maryadm', '45099878909', '0000-00-00', '13991690634', 'adm@adm2.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', NULL, 'admin'),
(3548, 'Mariavitroia lopes luduvico', '50233057839', '2007-07-12', '13991690634', 'lopesmariavitor4a@gmail.com', '$2y$10$rlBVpPj3Gmn5JmjclyYbC.rHiPW4oBORAOK6LJKtJ4InIgvMzZCP.', NULL, 'user'),
(3549, 'mavi', '12334412323', '2007-07-12', '13991690634', 'lopesmariavitoria@gmail.com', '$2y$10$/YF5iElIb3pTW4Iqq92SXu5BMUNZEnrEZ6vUtznUPbVCHQtHwlpQW', NULL, 'user'),
(3551, 'maria', '09876543212', '2007-07-12', '13991690634', 'lopesmariavitoria458@gmail.com', '$2y$10$JJcrkr3DQN0INgePbs0PW.mVPe.e7pHs.3mnTmVUnvg33kjEHzI3K', NULL, 'user'),
(3555, 'adm', '12345678990', '2007-07-12', '13991690634', 'adm@gmail.com', '$2y$10$/YF5iElIb3pTW4Iqq92SXu5BMUNZEnrEZ6vUtznUPbVCHQtHwlpQW', NULL, 'admin'),
(3556, 'gustavo', '56092314323', '3455-12-31', '13991690634', 'guga@dev.com', '$2y$10$nqdFa5Uu.LpYZjim.qtq3eu75Qt1d1cd4kQ7yLZ96vmF7M1EGishu', NULL, 'user'),
(3557, 'guga', '56092314328', '3455-12-31', '1234554567', 'guga@devi.com', '$2y$10$6.Ex.D.Drky3759bTEKqaeSkZgqbFZcjEO7wmtr2RImWmmE4Xe7Nm', NULL, 'user'),
(3558, 'guga', '64389542213', '2345-09-12', '524556789', 'guga@desenv.com', '$2y$10$xzQUe5jtAYpHWU1I6Zp6Y.0vLvr9VIOkgOF2Rk/4zrqhnEyjyhKju', NULL, 'user'),
(3559, 'guga', '53289834687', '4556-03-12', '13991690634', 'lopesmariavitoria000@gmail.com', '$2y$10$bvqrXWJCmppXxO54dHyfX.qNnxeThbPQ0ADgEsDWflEuwyCry61li', NULL, 'user'),
(3560, 'Maria vitoria', '22345664243', '4556-03-12', '12334556677', '123@123.com', '$2y$10$f8SHs46ydh91Q7tztjzsf.0jsdWtg.B3KkgkrWKVwI121rb30E4qG', 'images/users/user_692f822eef4fa1.51761394.jpg', 'user');

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
