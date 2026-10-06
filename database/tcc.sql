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
(1, 'Protetor facial', 'Proteção para o rosto', 10, 2, 12, 'EPI001', '25673'),
(2, 'Óculos de proteção', 'Proteção dos olhos', 10, 2, 12, 'EPI002', '83946'),
(3, 'Protetor auricular', 'Proteção auditiva', 10, 2, 12, 'EPI003', '10983'),
(4, 'Luvas', 'Proteção das mãos', 10, 2, 6, 'EPI004', '63784');

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
  `nivel_acesso` varchar(30) NOT NULL DEFAULT 'Funcionario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_funcionarios`
--

INSERT INTO `tb_funcionarios` (`id_funcionario`, `nome_completo`, `cpf`, `cargo`, `setor`, `foto_url`, `email`, `senha`, `nivel_acesso`) VALUES
(1, 'Administrador', '00000000000', 'Administrador', 'TI', NULL, 'admin@epicontrol.com', '$2y$10$TZlGTFt/lqgFnTtWqI2Q8edUJaugmzaxmRBBfH2cynjw9NUwix3C2', 'Administrador'),
(2, 'João Teste', '12345678900', 'Funcionario', NULL, NULL, 'joaoteste@gmail.com', '$2y$10$sE6SFDMYy01Nq57wBC2nc.1/POcanJvaz3o2prwqj8R3YD6slEBsG', 'Funcionario'),
(3, 'Lucas da Silva Pereira', '11111111101', 'Operador de Máquinas', 'Usinagem', 'img/boy.png', 'lucas.pereira@epicontrol.com', '123456', 'Funcionario'),
(5, 'Carlos Eduardo Souza', '11111111103', 'Auxiliar de Almoxarifado', 'Almoxarifado', 'img/boy.png', 'carlos.souza@epicontrol.com', '123456', 'Funcionario'),
(6, 'Bruno Fagundes Silva', '29028718805', 'Caldeireiro', 'Caldeiraria', 'img/boy.png', 'bruno.silva@epicontrol.com', '123456', 'Funcionario'),
(7, 'Mariana Costa Ribeiro', '11111111105', 'Analista de Qualidade', 'Controle de Qualidade', 'img/woman.png', 'mariana.ribeiro@epicontrol.com', '123456', 'Funcionario'),
(8, 'Gustavo Henrique Borges', '11111111106', 'Operador de Empilhadeira', 'Operação de Empilhadeira', 'img/boy.png', 'gustavo.borges@epicontrol.com', '123456', 'Funcionario'),
(9, 'Rafael Martins Oliveira', '11111111107', 'Mecânico Industrial', 'Manutenção Industrial', 'img/boy.png', 'rafael.oliveira@epicontrol.com', '123456', 'Funcionario'),
(10, 'Fernanda Alves Lima', '11111111108', 'Técnica de Segurança', 'Segurança do Trabalho', 'img/woman.png', 'fernanda.lima@epicontrol.com', '123456', 'Funcionario'),
(11, 'João Pedro Ferreira', '11111111109', 'Operador de Produção', 'Produção', 'img/boy.png', 'joao.ferreira@epicontrol.com', '123456', 'Funcionario'),
(12, 'Camila Rodrigues Santos', '11111111110', 'Assistente Administrativa', 'Administrativo', 'img/woman.png', 'camila.santos@epicontrol.com', '123456', 'Funcionario'),
(13, 'André Luiz Carvalho', '111111111123', 'Eletricista Industrial', 'Manutenção Industrial', 'img/boy.png', 'andre.carvalho@epicontrol.com', '123456', 'Funcionario'),
(14, 'Patrícia Mendes Rocha', '11111111112', 'Inspetora de Qualidade', 'Controle de Qualidade', 'img/woman.png', 'patricia.rocha@epicontrol.com', '123456', 'Funcionario'),
(15, 'Diego Ramos Martins', '11111111113', 'Soldador', 'Caldeiraria', 'img/boy.png', 'diego.martins@epicontrol.com', '123456', 'Funcionario'),
(16, 'Juliana Ferreira Costa', '11111111114', 'Auxiliar de Produção', 'Produção', 'img/woman.png', 'juliana.costa@epicontrol.com', '123456', 'Funcionario'),
(17, 'Marcelo Henrique Dias', '11111111115', 'Almoxarife', 'Almoxarifado', 'img/boy.png', 'marcelo.dias@epicontrol.com', '123456', 'Funcionario'),
(18, 'Larissa Oliveira Mendes', '11111111116', 'Assistente de Segurança', 'Segurança do Trabalho', 'img/woman.png', 'larissa.mendes@epicontrol.com', '123456', 'Funcionario'),
(19, 'Felipe Augusto Ribeiro', '11111111117', 'Técnico de Manutenção', 'Manutenção Industrial', 'img/boy.png', 'felipe.ribeiro@epicontrol.com', '123456', 'Funcionario'),
(20, 'Beatriz Martins Souza', '11111111118', 'Analista Administrativa', 'Administrativo', 'img/woman.png', 'beatriz.souza@epicontrol.com', '123456', 'Funcionario'),
(22, 'Amanda Cristina Lopes', '456.789.012-33', 'Técnica de Qualidade', 'Controle de Qualidade', 'img/woman.png', 'amanda.lopes@epicontrol.com', '123456', 'Funcionario'),
(24, 'Miguel Jarduli', '46056227804', 'Funcionario', 'Caldeiraria', NULL, 'jardulimiguel@gmail.com', '123456', 'Funcionario'),
(25, 'Giovana Della Tonia', '28756227804', 'Funcionario', 'Segurança do Trabalho', NULL, 'giovana.tonia@aluno.senai.br', 'Giovana120609', 'Funcionario');

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
(1, 13, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(2, 13, 3, 0, 1, '2026-09-08', 'Entrega de EPI'),
(3, 13, 1, 0, 1, '2026-09-08', 'Entrega de EPI'),
(4, 6, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(5, 6, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(6, 1, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(7, 1, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(8, 22, 1, 0, 1, '2026-09-08', 'Entrega de EPI'),
(9, 22, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(10, 20, 1, 0, 1, '2026-09-08', 'Entrega de EPI'),
(11, 12, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(12, 5, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(13, 15, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(14, 11, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(15, 10, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(16, 8, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(17, 18, 3, 0, 1, '2026-09-08', 'Entrega de EPI'),
(18, 2, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(19, 3, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(20, 17, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(21, 7, 4, 0, 1, '2026-09-08', 'Entrega de EPI'),
(22, 7, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(23, 14, 2, 0, 1, '2026-09-08', 'Entrega de EPI'),
(24, 14, 1, 0, 1, '2026-09-08', 'Entrega de EPI'),
(25, 9, 3, 0, 1, '2026-09-08', 'Entrega de EPI'),
(28, 6, 1, 0, 1, '2026-09-08', 'Entrega de EPI'),
(29, 1, 4, 0, 1, '2026-09-15', 'Entrega de EPI'),
(30, 1, 3, 0, 1, '2026-09-15', 'Entrega de EPI'),
(31, 1, 2, 0, 1, '2026-09-15', 'Entrega de EPI'),
(32, 1, 1, 0, 1, '2026-09-15', 'Entrega de EPI'),
(33, 13, 2, 0, 2, '2026-09-15', 'Entrega de EPI'),
(34, 13, 3, 0, 2, '2026-09-15', 'Entrega de EPI'),
(35, 22, 3, 0, 1, '2026-09-21', 'Entrega de EPI'),
(36, 5, 1, 0, 1, '2026-09-22', 'Entrega de EPI'),
(37, 5, 2, 0, 1, '2026-09-22', 'Entrega de EPI'),
(38, 5, 3, 0, 1, '2026-09-22', 'Entrega de EPI');

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
(1, 20, 4, 'Equipamento danificado', 'Pendente', '2026-09-22 15:50:56', 2),
(2, 20, 4, 'Equipamento danificado', 'Pendente', '2026-09-22 15:51:58', 2),
(3, 20, 3, 'Equipamento vencido', 'Pendente', '2026-09-22 15:52:39', 1);

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
