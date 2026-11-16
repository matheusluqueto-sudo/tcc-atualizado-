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

--
-- Estrutura para tabela `tb_funcionarios`
--

CREATE TABLE `tb_funcionarios` (
  `id_funcionario` int(11) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `setor` varchar(100) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` varchar(30) NOT NULL DEFAULT 'Funcionario',
  `status_funcionario` enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_funcionarios`
--

INSERT INTO `tb_funcionarios` (`id_funcionario`, `nome_completo`, `cpf`, `cargo`, `setor`, `foto_url`, `email`, `senha`, `nivel_acesso`, `status_funcionario`) VALUES
(1, 'Administrador de Teste', NULL, 'Administrador', 'TI', NULL, 'admin@epicontrol.com', '$2y$12$U7.I/PBLUVzE18q20GCiUOD9UlXuyAIUQi4KPC7DsAgagx7onn/jO', 'Administrador', 'Ativo'),
(2, 'Funcionário de Teste', NULL, 'Operador', 'Produção', 'img/boy.png', 'joaoteste@example.test', '$2y$12$U7.I/PBLUVzE18q20GCiUOD9UlXuyAIUQi4KPC7DsAgagx7onn/jO', 'Funcionario', 'Ativo'),
(20, 'Funcionário Demonstração 20', NULL, 'Operador', 'Produção', 'img/woman.png', 'funcionario20@example.test', '$2y$12$U7.I/PBLUVzE18q20GCiUOD9UlXuyAIUQi4KPC7DsAgagx7onn/jO', 'Funcionario', 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_pedidos`
--

CREATE TABLE `tb_pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_epi` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `status_pedido` varchar(30) NOT NULL DEFAULT 'Pendente',
  `data_pedido` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_saida`
--

CREATE TABLE `tb_saida` (
  `id_saida` int(11) NOT NULL,
  `id_funcionario` int(11) NOT NULL,
  `id_epi` int(11) NOT NULL,
  `solicitacao` tinyint(1) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `data` date DEFAULT NULL,
  `motivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_saida`
--

INSERT INTO `tb_saida` (`id_saida`, `id_funcionario`, `id_epi`, `solicitacao`, `quantidade`, `data`, `motivo`) VALUES
(1, 2, 2, 0, 1, '2026-09-08', 'Registro de entrega fictício para demonstração');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_solicitacoes`
--

CREATE TABLE `tb_solicitacoes` (
  `id_solicitacao` int(11) NOT NULL,
  `id_funcionario` int(11) NOT NULL,
  `id_epi` int(11) NOT NULL,
  `justificativa` text DEFAULT NULL,
  `status_solicitacao` varchar(30) NOT NULL DEFAULT 'Pendente',
  `data_solicitacao` datetime NOT NULL DEFAULT current_timestamp(),
  `quantidade` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_solicitacoes`
--

INSERT INTO `tb_solicitacoes` (`id_solicitacao`, `id_funcionario`, `id_epi`, `justificativa`, `status_solicitacao`, `data_solicitacao`, `quantidade`) VALUES
(1, 20, 4, 'Equipamento danificado', 'Pendente', '2026-09-22 15:50:56', 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `if_solicitacao`
--
ALTER TABLE `if_solicitacao`
  ADD PRIMARY KEY (`id_solicitacao`);

--
-- Índices de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_entrada`
--
ALTER TABLE `tb_entrada`
  ADD PRIMARY KEY (`id_entrada`),
  ADD KEY `idx_entrada_epi` (`id_epi`);

--
-- Índices de tabela `tb_epis`
--
ALTER TABLE `tb_epis`
  ADD PRIMARY KEY (`id_epi`),
  ADD UNIQUE KEY `uk_epis_codigo` (`codigo`);

--
-- Índices de tabela `tb_funcionarios`
--
ALTER TABLE `tb_funcionarios`
  ADD PRIMARY KEY (`id_funcionario`),
  ADD UNIQUE KEY `uk_funcionarios_email` (`email`);

--
-- Índices de tabela `tb_pedidos`
--
ALTER TABLE `tb_pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_epi` (`id_epi`);

--
-- Índices de tabela `tb_saida`
--
ALTER TABLE `tb_saida`
  ADD PRIMARY KEY (`id_saida`),
  ADD KEY `idx_saida_funcionario` (`id_funcionario`),
  ADD KEY `idx_saida_epi` (`id_epi`);

--
-- Índices de tabela `tb_solicitacoes`
--
ALTER TABLE `tb_solicitacoes`
  ADD PRIMARY KEY (`id_solicitacao`),
  ADD KEY `idx_solicitacao_funcionario` (`id_funcionario`),
  ADD KEY `idx_solicitacao_epi` (`id_epi`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_entrada`
--
ALTER TABLE `tb_entrada`
  MODIFY `id_entrada` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_epis`
--
ALTER TABLE `tb_epis`
  MODIFY `id_epi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tb_funcionarios`
--
ALTER TABLE `tb_funcionarios`
  MODIFY `id_funcionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `tb_pedidos`
--
ALTER TABLE `tb_pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_saida`
--
ALTER TABLE `tb_saida`
  MODIFY `id_saida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `tb_solicitacoes`
--
ALTER TABLE `tb_solicitacoes`
  MODIFY `id_solicitacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `if_solicitacao`
--
ALTER TABLE `if_solicitacao`
  ADD CONSTRAINT `if_solicitacao_ibfk_1` FOREIGN KEY (`id_solicitacao`) REFERENCES `tb_solicitacoes` (`id_solicitacao`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `tb_entrada`
--
ALTER TABLE `tb_entrada`
  ADD CONSTRAINT `tb_entrada_ibfk_1` FOREIGN KEY (`id_epi`) REFERENCES `tb_epis` (`id_epi`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `tb_pedidos`
--
ALTER TABLE `tb_pedidos`
  ADD CONSTRAINT `tb_pedidos_ibfk_1` FOREIGN KEY (`id_epi`) REFERENCES `tb_epis` (`id_epi`);

--
-- Restrições para tabelas `tb_saida`
--
ALTER TABLE `tb_saida`
  ADD CONSTRAINT `tb_saida_ibfk_1` FOREIGN KEY (`id_funcionario`) REFERENCES `tb_funcionarios` (`id_funcionario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_saida_ibfk_2` FOREIGN KEY (`id_epi`) REFERENCES `tb_epis` (`id_epi`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `tb_solicitacoes`
--
ALTER TABLE `tb_solicitacoes`
  ADD CONSTRAINT `tb_solicitacoes_ibfk_1` FOREIGN KEY (`id_funcionario`) REFERENCES `tb_funcionarios` (`id_funcionario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_solicitacoes_ibfk_2` FOREIGN KEY (`id_epi`) REFERENCES `tb_epis` (`id_epi`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
