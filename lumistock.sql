-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/10/2025 às 03:02
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `lumistock`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `comentario` varchar(500) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacoes`
--

CREATE TABLE `movimentacoes` (
  `id` int(11) NOT NULL,
  `data_movimentacao` date NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `usuario_email` varchar(100) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `imagem` blob NOT NULL,
  `sku` varchar(50) NOT NULL,
  `descricao` varchar(500) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `preco` float NOT NULL,
  `data_cadastro` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorios`
--

CREATE TABLE `relatorios` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `categoria` varchar(100) DEFAULT 'todas',
  `data_inicial` date DEFAULT NULL,
  `data_final` date DEFAULT NULL,
  `total_produtos` int(11) DEFAULT 0,
  `baixo_estoque` int(11) DEFAULT 0,
  `valor_total` decimal(12,2) DEFAULT 0.00,
  `unidades_totais` int(11) DEFAULT 0,
  `data_geracao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relatorios`
--

INSERT INTO `relatorios` (`id`, `tipo`, `categoria`, `data_inicial`, `data_final`, `total_produtos`, `baixo_estoque`, `valor_total`, `unidades_totais`, `data_geracao`) VALUES
(0, 'produtos', 'todas', '0000-00-00', '0000-00-00', 1, 1, 2689.00, 1, '2025-10-13 15:43:56'),
(0, 'produtos', 'todas', '0000-00-00', '0000-00-00', 1, 1, 2689.00, 1, '2025-10-13 15:45:46'),
(0, 'produtos', 'todas', '0000-00-00', '0000-00-00', 1, 1, 2689.00, 1, '2025-10-13 15:46:14'),
(0, 'produtos', 'todas', '0000-00-00', '0000-00-00', 1, 1, 2689.00, 1, '2025-10-13 15:54:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `imagem_perfil` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`) VALUES
(0, 'Jacquyys', 'teste@gmail.com', '$2y$10$vYPYqWgmg7PULcGkW.uOQ.PK0EOJjGkVlw43zti90AHTMHhiGTPF.');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
