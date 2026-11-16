-- EPI Control: estrutura compatível com a versão TCC2 enviada pelo usuário.
-- IMPORTAR SOMENTE EM BANCO VAZIO DE TESTES/DEMONSTRAÇÃO.
-- Dados pessoais foram substituídos por registros FICTÍCIOS.
-- Não contém os 20 registros exigidos por tabela pelo SENAI.
-- NUNCA importar sobre o banco já existente no InfinityFree.
-- Contas de teste existem para compatibilidade com tests/integration.php;
-- não reutilizá-las em ambiente público.

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/10/2026 às 15:54
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tcc`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `if_solicitacao`
--

CREATE TABLE `if_solicitacao` (
  `id_solicitacao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `expira_em` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_entrada`
--

CREATE TABLE `tb_entrada` (
  `id_entrada` int(11) NOT NULL,
  `id_epi` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data` date DEFAULT NULL,
  `motivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_epis`
--

CREATE TABLE `tb_epis` (
  `id_epi` int(11) NOT NULL,
  `nome_epi` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `quantidade_estoque` int(11) NOT NULL DEFAULT 0,
  `estoque_minimo` int(11) NOT NULL DEFAULT 0,
  `validade` int(11) DEFAULT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `ca` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_epis`
--

INSERT INTO `tb_epis` (`id_epi`, `nome_epi`, `descricao`, `quantidade_estoque`, `estoque_minimo`, `validade`, `codigo`, `ca`) VALUES
(1, 'Protetor facial', 'Proteção para o rosto (exemplo)', 10, 2, 12, 'EPI001', NULL),
(2, 'Óculos de proteção', 'Proteção para os olhos (exemplo)', 10, 2, 12, 'EPI002', NULL),
(3, 'Protetor auricular', 'Proteção auditiva (exemplo)', 10, 2, 12, 'EPI003', NULL),
(4, 'Luvas de proteção', 'Proteção para as mãos (exemplo)', 10, 2, 6, 'EPI004', NULL);

-- --------------------------------------------------------
