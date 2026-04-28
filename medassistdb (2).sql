-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/04/2026 às 03:13
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
-- Banco de dados: `medassistdb`
--
CREATE DATABASE IF NOT EXISTS `medassistdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `medassistdb`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `diagnostico`
--

CREATE TABLE `diagnostico` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL COMMENT 'FK para Paciente (agora usuarios)',
  `medico_id` int(11) NOT NULL COMMENT 'FK para usuarios (Médico)',
  `data` datetime NOT NULL COMMENT 'Data e hora do diagnóstico',
  `cid_10` varchar(10) DEFAULT NULL COMMENT 'Código CID-10',
  `descricao` text NOT NULL COMMENT 'Descrição detalhada do diagnóstico',
  `resultadoPrevisto` varchar(50) DEFAULT NULL COMMENT 'Ex: Curado, Crônico, Em Tratamento',
  `probabilidade` decimal(5,2) DEFAULT NULL COMMENT 'Probabilidade de cura/sucesso (em %)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `diagnostico`
--

INSERT INTO `diagnostico` (`id`, `paciente_id`, `medico_id`, `data`, `cid_10`, `descricao`, `resultadoPrevisto`, `probabilidade`) VALUES
(1, 11, 9, '2025-11-02 11:12:25', 'D50', 'anemia ferropriva leve, necessita de suplemento', 'Em Tratamento', 95.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `exame`
--

CREATE TABLE `exame` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL COMMENT 'FK para Paciente (agora usuarios)',
  `medico_id` int(11) NOT NULL COMMENT 'FK para usuarios (Médico)',
  `data` datetime NOT NULL COMMENT 'Data e hora do exame/solicitação',
  `tipo` varchar(100) NOT NULL COMMENT 'Ex: Hemograma Completo, Raio-X Toráxico',
  `resultado` text DEFAULT NULL COMMENT 'Descrição do resultado do exame',
  `link_laudo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `exame`
--

INSERT INTO `exame` (`id`, `paciente_id`, `medico_id`, `data`, `tipo`, `resultado`, `link_laudo`) VALUES
(1, 11, 9, '2025-10-15 08:32:49', 'Glicemia de Jejum', 'Resultado: 95 mg/dL (Normal). ', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_receita`
--

CREATE TABLE `itens_receita` (
  `id` int(11) NOT NULL,
  `receita_id` int(11) NOT NULL COMMENT 'ID da Receita (FK para receitas)',
  `medicamento_nome` varchar(255) NOT NULL COMMENT 'Nome do medicamento',
  `concentracao` varchar(100) NOT NULL COMMENT 'Ex: 500mg Comprimido, 30 Gotas',
  `quantidade_total` varchar(50) NOT NULL COMMENT 'Ex: 30 comprimidos, 1 frasco',
  `posologia` text NOT NULL COMMENT 'Instruções de uso: 1 comp a cada 8h por 7 dias'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_receita`
--

INSERT INTO `itens_receita` (`id`, `receita_id`, `medicamento_nome`, `concentracao`, `quantidade_total`, `posologia`) VALUES
(13, 6, 'Clonazepam', '2mg', '30 comprimidos', '1 comprimido ao deitar'),
(14, 6, 'fenobarbital', '40mg/mL', '1 frasco de 20 ml', '2 a 3 mg/kg/dia em dose única ou fracionada. Em caso de dúvidas, consulte a bula'),
(16, 7, 'Puran T4', '100 mg', '30 comprimidos', 'Tomar um comprimido de manha em jejum por no mínimo 4h, e ficar mais 20 min de jejum até tomar café'),
(29, 12, 'Alprazolam', '0,5 mg – Comprimidos', '30 comprimidos (1 caixa)', 'Uso)	Tomar 1 comprimido via oral à noite, antes de deitar.'),
(30, 13, 'Canabidiol', '10/mg Gotas', 'Frasco de 200ml', 'pingar sob a lingua, 20 gotas'),
(31, 14, 'cimegripe', '0,5 mg – Comprimidos', '30', '2 por dia'),
(32, 15, 'Doril', '500mg', '30 comprimidos (1 caixa)', 'aa'),
(33, 16, 'Alprazolam', '0,5 mg – Comprimidos', '30', '3 por dia'),
(34, 17, 'Alprazolam', '500mg', '30', 'aa'),
(35, 18, 'Doril', '0,5 mg – Comprimidos', '30 comprimidos (1 caixa)', '1/dia'),
(36, 19, 'Doril', '500mg', '1', '1');

-- --------------------------------------------------------

--
-- Estrutura para tabela `receitas`
--

CREATE TABLE `receitas` (
  `id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL COMMENT 'ID do Médico (FK para usuarios)',
  `paciente_id` int(11) NOT NULL COMMENT 'ID do Paciente (FK para usuarios)',
  `data_prescricao` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Data e hora da emissão',
  `tipo_receita` varchar(50) NOT NULL DEFAULT 'Simples' COMMENT 'Tipo: Branco Simples, Branco Especial,  azul, e amarelo.',
  `observacoes` text DEFAULT NULL COMMENT 'Observações gerais do médico',
  `token_assinatura` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `receitas`
--

INSERT INTO `receitas` (`id`, `medico_id`, `paciente_id`, `data_prescricao`, `tipo_receita`, `observacoes`, `token_assinatura`) VALUES
(6, 9, 11, '2025-11-02 01:24:22', 'Azul', 'contraindicado para pacientes com hipersensibilidade (alergia) conhecida aos benzodiazepínicos, com comprometimento hepático (fígado) grave, ou com insuficiência respiratória grave. Também não deve ser usado em conjunto com álcool e outros depressores do sistema nervoso central.', NULL),
(7, 9, 11, '2025-11-03 11:35:52', 'Simples', NULL, NULL),
(12, 9, 18, '2026-03-30 21:50:48', 'Azul', 'Uso contínuo', NULL),
(13, 9, 12, '2026-04-07 18:21:33', 'Amarela', 'na falta do medicamento, pode acender um prensado', NULL),
(14, 9, 15, '2026-04-23 22:01:57', 'Simples', 'gripe', '470b3f7e4eafd8dbfe0e2145844de264dd22dc12b888c27a4a6e91ed8618c534'),
(15, 9, 18, '2026-04-23 22:02:56', 'Simples', 'dor no joelho', '8118018fb2d456a2e0367cca522fb364341d424cec3ffe47c9b8d06f476a570e'),
(16, 9, 18, '2026-04-23 22:04:29', 'Simples', 'dor na canela', '233bf431df8ee3528bcf4c223c146271671bf44eff61def1fae0b18f7d0f4e62'),
(17, 9, 11, '2026-04-23 22:11:34', 'Simples', 'da um ligue ae', '429dd6ccc3aa75792b370b3cc065fa3398ef7448995d34bb1c919d000d39969a'),
(18, 9, 17, '2026-04-23 22:12:29', 'Simples', 'caxumba', 'a28a7da6f9b6282f74622ccea923d8fc5342052a270f17ca30421bc1f54d6266'),
(19, 16, 22, '2026-04-28 02:40:15', 'Simples', 'dor no ombro', 'c8086af5a9a155d9549e85f2900779ffb1da68f3e55b65d684d9958e5db76b4d');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `codigo` char(4) NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `email`, `codigo`, `data_expiracao`, `usado`) VALUES
(1, 'med.assist3501@gmail.com', '7183', '2026-04-28 02:40:39', 1),
(2, 'med.assist3501@gmail.com', '5968', '2026-04-28 02:44:55', 1),
(3, 'med.assist3501@gmail.com', '9543', '2026-04-28 02:45:55', 1),
(4, 'med.assist3501@gmail.com', '6189', '2026-04-28 03:11:00', 1),
(5, 'med.assist3501@gmail.com', '8727', '2026-04-28 03:13:07', 1),
(6, 'med.assist3501@gmail.com', '0497', '2026-04-28 03:17:11', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `triagens`
--

CREATE TABLE `triagens` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `enfermeiro_id` int(11) NOT NULL,
  `queixa_principal` text NOT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL COMMENT 'Ex: 36.5',
  `peso` decimal(5,2) DEFAULT NULL COMMENT 'Ex: 70.50',
  `altura` decimal(3,2) DEFAULT NULL COMMENT 'Ex: 1.75',
  `frequencia_cardiaca` int(11) DEFAULT NULL COMMENT 'Ex: 80',
  `saturacao` int(11) DEFAULT NULL COMMENT 'Ex: 98',
  `pressao_sistolica` int(11) DEFAULT NULL,
  `pressao_diastolica` int(11) DEFAULT NULL,
  `classificacao_risco` enum('Azul','Verde','Amarelo','Laranja','Vermelho') DEFAULT 'Verde',
  `data_hora` datetime DEFAULT current_timestamp(),
  `status` enum('Aguardando Médico','Atendido','Cancelado') DEFAULT 'Aguardando Médico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `triagens`
--

INSERT INTO `triagens` (`id`, `paciente_id`, `enfermeiro_id`, `queixa_principal`, `temperatura`, `peso`, `altura`, `frequencia_cardiaca`, `saturacao`, `pressao_sistolica`, `pressao_diastolica`, `classificacao_risco`, `data_hora`, `status`) VALUES
(5, 12, 19, 'Colica', 37.6, 42.00, 1.60, 82, 98, 120, 80, 'Verde', '2026-04-07 12:08:55', 'Aguardando Médico');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `role` enum('admin','medico','enfermeiro','paciente') DEFAULT 'paciente' COMMENT 'Papéis de acesso ao sistema',
  `crm_registro` varchar(50) DEFAULT NULL COMMENT 'Registro CRM do Médico. Deve ser preenchido se o papel role for medico.',
  `coren_registro` varchar(50) DEFAULT NULL COMMENT 'Registro COREN do Enfermeiro/Técnico. Deve ser preenchido se o role for enfermeiro.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `cpf`, `nome`, `email`, `data_nascimento`, `senha`, `role`, `crm_registro`, `coren_registro`) VALUES
(9, NULL, 'Auzio Varella', 'medico@medico', '1943-05-03', '$2y$10$KoF4rakY2UEe8pWD/LJFkOGlE/4BkS9Zb3FgkjTYJON3cbj83TCEe', 'medico', '123456/SP', NULL),
(11, NULL, 'Gustavo Ribeiro', 'ribeirinho4050@gmail.com', '2006-03-11', '$2y$10$I/eqa7/eIy1Mm7GerB5KoeLfrlIb.PCNJnFPuaLNBOCL13PJVv.ri', 'paciente', NULL, NULL),
(12, NULL, 'Natalia Torres', 'loonaily@gmail.com', '2006-07-25', '$2y$10$JdhO0daWlWjaGF5O13f7IuAxtwheVhsd7aBQlQ2gnI0JmOOCGNgoK', 'paciente', NULL, NULL),
(13, NULL, 'Renato Augusto ramos', 'Renato@augusto.2015', '1991-07-02', '$2y$10$SIQOc0tr6Kjl7Ha7usJuJeqF9cVwqYe9xtdPYZirX0HCXlkc3eJMG', 'paciente', NULL, NULL),
(15, '123.456.789', 'Luiz Otávio de Oliveira', 'luiz@otavio.com.br', '2005-09-20', '$2y$10$3vpHez3Z8CPeVK89vIl9T.4hdaOQAk70S5YugBOjh2NQ7aTdpClFS', 'paciente', NULL, NULL),
(16, NULL, 'Carlos Chagaz', 'chagaz@gmail.com', '1961-11-08', '$2y$10$yydVHZAemPyKCuuILhT4y.S1eRc3rvh8.cJlwV8A3qicX7RbiGnrG', 'medico', '909090/SP', NULL),
(17, NULL, 'Gustavo Ribeiro', 'teste\'injec-tion@gmail.com', '2000-11-12', '$2y$10$sO31okz2bnAQv1w3VSmj8eDa8ncU1wqVwYyLQwaSRRq0waqqIgh52', 'paciente', NULL, NULL),
(18, NULL, 'Marcos Verrati', 'med.assist3501@gmail.com', '1989-05-01', '$2y$10$z6h1dFglQ2ph4S8q/k40yuBrrX15U9vF/oZtIWLpKRH0DKRVLYJDC', 'paciente', NULL, NULL),
(19, NULL, 'Florencia Ortega', 'flor@gmail', '1970-01-24', '$2y$10$KAekwGRgurX78BfH9lvfDuuGZOFb7hfPOm66sV1UwHP/YJLbIDB96', 'enfermeiro', NULL, '121212/SP'),
(20, NULL, 'rodrigo', 'rodrigo@email', '1998-12-02', '$2y$10$tQVr/TsLTRY0i/JsFXdjT./YP8eE4OnPxIg00UMyRFzZEo.RzDqYW', 'paciente', NULL, NULL),
(21, NULL, 'rodrigo', 'rodrigo@email', '1998-12-02', '$2y$10$2ipN3QKbbcLR/ZvQ9l1yQug/1xjQ9cgmGrzzH1jOLi5gjoHdBRwAy', 'paciente', NULL, NULL),
(22, '132.465.798', 'roger machado', 'roger@gmail.com', '1998-06-17', '$2y$10$C55jeTqoMfIRv.5Q58hIo.ujgwT62hNRm7yAAxBXPb2Yh/ledL6hW', 'paciente', NULL, NULL),
(26, '456.897.456-11', 'vinicius ramos', 'vinicius.rm@gmail.com', '2026-04-08', '$2y$10$iorBkLIgbpKvTP1Jff1GxOYrt5ZGW9W.CVP7mF5zwW8smhDrTgqGy', 'paciente', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id_diag` (`paciente_id`),
  ADD KEY `medico_id_diag` (`medico_id`);

--
-- Índices de tabela `exame`
--
ALTER TABLE `exame`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `medico_id_exame` (`medico_id`);

--
-- Índices de tabela `itens_receita`
--
ALTER TABLE `itens_receita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receita_id` (`receita_id`);

--
-- Índices de tabela `receitas`
--
ALTER TABLE `receitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medico_id` (`medico_id`),
  ADD KEY `paciente_id` (`paciente_id`);

--
-- Índices de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `triagens`
--
ALTER TABLE `triagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_triagem_paciente` (`paciente_id`),
  ADD KEY `fk_triagem_enfermeiro` (`enfermeiro_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_registro` (`crm_registro`),
  ADD UNIQUE KEY `coren_registro` (`coren_registro`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `cpf_2` (`cpf`),
  ADD UNIQUE KEY `cpf_3` (`cpf`),
  ADD UNIQUE KEY `cpf_4` (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `diagnostico`
--
ALTER TABLE `diagnostico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `exame`
--
ALTER TABLE `exame`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `itens_receita`
--
ALTER TABLE `itens_receita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `receitas`
--
ALTER TABLE `receitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `triagens`
--
ALTER TABLE `triagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `diagnostico`
--
ALTER TABLE `diagnostico`
  ADD CONSTRAINT `diagnostico_ibfk_medico` FOREIGN KEY (`medico_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `diagnostico_ibfk_paciente_consolidado` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `exame`
--
ALTER TABLE `exame`
  ADD CONSTRAINT `exame_ibfk_medico` FOREIGN KEY (`medico_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exame_ibfk_paciente_consolidado` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `itens_receita`
--
ALTER TABLE `itens_receita`
  ADD CONSTRAINT `itens_receita_ibfk_1` FOREIGN KEY (`receita_id`) REFERENCES `receitas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `receitas`
--
ALTER TABLE `receitas`
  ADD CONSTRAINT `receitas_ibfk_1` FOREIGN KEY (`medico_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `receitas_ibfk_2` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `triagens`
--
ALTER TABLE `triagens`
  ADD CONSTRAINT `fk_triagem_enfermeiro` FOREIGN KEY (`enfermeiro_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_triagem_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
