CREATE DATABASE  IF NOT EXISTS `medassistdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `medassistdb`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: medassistdb
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `administracao_medicamentos`
--

DROP TABLE IF EXISTS `administracao_medicamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administracao_medicamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_receita_id` int(11) NOT NULL COMMENT 'FK para itens_receita',
  `enfermeiro_id` int(11) NOT NULL COMMENT 'FK para usuarios (quem administrou)',
  `paciente_id` int(11) NOT NULL COMMENT 'FK para usuarios (paciente)',
  `data_administracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Administrado','Recusado','Adiado') DEFAULT 'Administrado',
  `observacao` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_admin_item` (`item_receita_id`),
  KEY `fk_admin_enfermeiro` (`enfermeiro_id`),
  KEY `fk_admin_paciente` (`paciente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administracao_medicamentos`
--

LOCK TABLES `administracao_medicamentos` WRITE;
/*!40000 ALTER TABLE `administracao_medicamentos` DISABLE KEYS */;
INSERT INTO `administracao_medicamentos` VALUES (1,37,18,18,'2026-05-09 02:59:30','Administrado','paciente apresentou reação alérgica\r\n'),(2,33,18,18,'2026-05-09 03:15:12','Administrado',''),(3,29,19,18,'2026-05-09 05:01:51','Administrado',''),(4,29,19,18,'2026-05-09 05:01:58','Administrado',''),(5,13,19,11,'2026-05-09 23:31:42','Administrado',''),(6,13,19,11,'2026-05-10 14:04:39','Administrado',''),(7,39,19,18,'2026-05-10 16:37:13','Administrado','Paciente relatou melhora da dor após 20min.'),(8,40,19,18,'2026-05-10 16:37:13','Recusado','Paciente apresentou náusea súbita, medicação suspensa momentaneamente pela equipe.'),(9,46,19,18,'2026-05-11 04:48:31','Administrado','paciente apresentou ótimo resultado'),(10,47,19,18,'2026-05-11 06:12:36','Administrado','sim teste hoje'),(11,48,19,22,'2026-06-19 13:20:20','Administrado',NULL);
/*!40000 ALTER TABLE `administracao_medicamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diagnostico`
--

DROP TABLE IF EXISTS `diagnostico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `triagem_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `cid_10` varchar(10) DEFAULT NULL,
  `descricao` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_diag_triagem` (`triagem_id`),
  KEY `fk_diag_medico` (`medico_id`),
  KEY `fk_diag_paciente` (`paciente_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico`
--

LOCK TABLES `diagnostico` WRITE;
/*!40000 ALTER TABLE `diagnostico` DISABLE KEYS */;
INSERT INTO `diagnostico` VALUES (2,13,9,26,'T78.0','Reação alérgica sistêmica grave após ingestão alimentar, evoluindo com angioedema labial e sinais de obstrução de vias aéreas superiores (edema de glote). Apresenta hipotensão arterial e queda na saturação de oxigênio (88%), caracterizando anafilaxia com comprometimento respiratório e circulatório.','2026-05-07 16:48:35'),(4,14,9,18,'K35','Apendicite aguda confirmada via exame físico e sinais vitais. Necessário internação para observação e analgesia venosa.','2026-05-10 16:37:13'),(8,7,9,11,'E03.9','Hipotireoidismo não especificado','2026-06-19 14:22:27');
/*!40000 ALTER TABLE `diagnostico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guia_exames`
--

DROP TABLE IF EXISTS `guia_exames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guia_exames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `triagem_id` int(11) NOT NULL,
  `carater_solicitacao` enum('Eletiva','Urgencia') DEFAULT 'Eletiva',
  `cid_10` varchar(10) DEFAULT NULL,
  `indicacao_clinica` text DEFAULT NULL,
  `descricao_exames` text NOT NULL,
  `data_solicitacao` datetime NOT NULL,
  `token_assinatura` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `medico_id` (`medico_id`),
  KEY `triagem_id` (`triagem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guia_exames`
--

LOCK TABLES `guia_exames` WRITE;
/*!40000 ALTER TABLE `guia_exames` DISABLE KEYS */;
INSERT INTO `guia_exames` VALUES (2,22,9,9,'Eletiva','T78.0','','hemograma','2026-05-20 11:46:39','c6bb70d1ba1a1800c35a935c13d39a6c790871799bd63650cb5bc0d25dbc1970'),(5,11,9,7,'Eletiva','E03.9','','T4 Livre, TSH','2026-06-19 11:19:45','8658151b6e2836ccb3b368509787aecb6a2c561c72f88c8dd1e11ef63a58d98b');
/*!40000 ALTER TABLE `guia_exames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itens_receita`
--

DROP TABLE IF EXISTS `itens_receita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens_receita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receita_id` int(11) NOT NULL COMMENT 'ID da Receita (FK para receitas)',
  `medicamento_nome` varchar(255) NOT NULL COMMENT 'Nome do medicamento',
  `concentracao` varchar(100) NOT NULL COMMENT 'Ex: 500mg Comprimido, 30 Gotas',
  `quantidade_total` varchar(50) NOT NULL COMMENT 'Ex: 30 comprimidos, 1 frasco',
  `posologia` text NOT NULL COMMENT 'Instruções de uso: 1 comp a cada 8h por 7 dias',
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `justificativa_cancelamento` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receita_id` (`receita_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itens_receita`
--

LOCK TABLES `itens_receita` WRITE;
/*!40000 ALTER TABLE `itens_receita` DISABLE KEYS */;
INSERT INTO `itens_receita` VALUES (13,6,'Clonazepam','2mg','30 comprimidos','1 comprimido ao deitar',NULL,NULL,NULL),(14,6,'fenobarbital','40mg/mL','1 frasco de 20 ml','2 a 3 mg/kg/dia em dose única ou fracionada. Em caso de dúvidas, consulte a bula',NULL,NULL,NULL),(16,7,'Puran T4','100 mg','30 comprimidos','Tomar um comprimido de manha em jejum por no mínimo 4h, e ficar mais 20 min de jejum até tomar café',NULL,NULL,NULL),(29,12,'Alprazolam','0,5 mg – Comprimidos','30 comprimidos (1 caixa)','Uso)	Tomar 1 comprimido via oral à noite, antes de deitar.',NULL,NULL,NULL),(31,14,'cimegripe','0,5 mg – Comprimidos','30','2 por dia',NULL,NULL,NULL),(32,15,'Doril','500mg','30 comprimidos (1 caixa)','aa',NULL,NULL,NULL),(33,16,'Alprazolam','0,5 mg – Comprimidos','30','3 por dia',NULL,NULL,NULL),(35,18,'Doril','0,5 mg – Comprimidos','30 comprimidos (1 caixa)','1/dia',NULL,NULL,NULL),(36,19,'Doril','500mg','1','1',NULL,NULL,NULL),(37,20,'Doril','0,5 mg – Comprimidos','30 comprimidos (1 caixa)','3/ dia',NULL,NULL,NULL),(38,21,'Doril','0,5 mg – Comprimidos','30 comprimidos (1 caixa)','a',NULL,NULL,NULL),(39,100,'Morfina','10mg/mL','5 ampolas','Administrar 2mg via EV se dor escala > 7',NULL,NULL,NULL),(40,100,'Dipirona','500mg/mL','10 ampolas','1 ampola via EV a cada 6 horas',NULL,NULL,NULL),(41,200,'Amoxicilina','500mg','21 comprimidos','1 comprimido de 8/8h por 7 dias',NULL,NULL,NULL),(42,201,'Ibuprofeno','600mg','10 comprimidos','1 comprimido se houver dor muscular',NULL,NULL,NULL),(43,300,'Amoxicilina','500mg','21 comprimidos','1 comprimido de 8/8h por 7 dias','2026-03-01','2026-03-08',NULL),(44,301,'Ibuprofeno','600mg','10 comprimidos','1 comprimido se houver dor a cada 12h','2026-05-05','2026-05-11','teste\r\n'),(45,302,'Losartana','50mg','90 comprimidos','1 comprimido pela manhã (Uso contínuo)','2026-05-10','2026-05-11','pq eu fiz uma prescrição errada'),(46,305,'Ceftriaxone (Rocefin)','1g - Frasco Ampola','7 ampolas','Administrar 1g via IV, uma vez ao dia, por 7 dias.','2026-05-11','2026-05-11','testando cancelamento'),(47,306,'Alprazolam','0,5 mg – Comprimidos','30 comprimidos (1 caixa)','10/dia','2026-05-11','2026-05-13',NULL),(48,0,'paracetamol','400mg','30 comprimidos','a cada 8 horas','2026-05-20','2026-06-20',NULL),(49,0,'Puran T4','75mg','3 cartelas de 30 comprimidos','Tomar 30 minutos antes do café da manhã e 4 horas depois de qualquer refeição anterior','2026-06-19','2026-09-19',NULL),(50,0,'Puran T4','75mg','3 cartelas de 30 comprimidos','Tomar de manha no mínimo 20 minutos antes do café da manhã, e esperar no mínimo 4h depois de qualquer outra refeição','2026-06-19','2026-09-19',NULL),(51,323,'Puran T4','75mg','3 cartelas de 30 comprimidos','Tomar no mínimo 4h após qualquer refeição e no mínimo 30min antes do café da manhã','2026-06-19','2026-09-19',NULL);
/*!40000 ALTER TABLE `itens_receita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receitas`
--

DROP TABLE IF EXISTS `receitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medico_id` int(11) NOT NULL COMMENT 'ID do Médico (FK para usuarios)',
  `paciente_id` int(11) NOT NULL COMMENT 'ID do Paciente (FK para usuarios)',
  `data_prescricao` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Data e hora da emissão',
  `tipo_receita` varchar(50) NOT NULL DEFAULT 'Simples' COMMENT 'Tipo: Branco Simples, Branco Especial,  azul, e amarelo.',
  `observacoes` text DEFAULT NULL COMMENT 'Observações gerais do médico',
  `token_assinatura` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=324 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receitas`
--

LOCK TABLES `receitas` WRITE;
/*!40000 ALTER TABLE `receitas` DISABLE KEYS */;
INSERT INTO `receitas` VALUES (12,9,18,'2026-03-30 21:50:48','Azul','Uso contínuo',NULL),(14,9,15,'2026-04-23 22:01:57','Simples','gripe','470b3f7e4eafd8dbfe0e2145844de264dd22dc12b888c27a4a6e91ed8618c534'),(15,9,18,'2026-04-23 22:02:56','Simples','dor no joelho','8118018fb2d456a2e0367cca522fb364341d424cec3ffe47c9b8d06f476a570e'),(16,9,18,'2026-04-23 22:04:29','Simples','dor na canela','233bf431df8ee3528bcf4c223c146271671bf44eff61def1fae0b18f7d0f4e62'),(18,9,17,'2026-04-23 22:12:29','Simples','caxumba','a28a7da6f9b6282f74622ccea923d8fc5342052a270f17ca30421bc1f54d6266'),(19,16,22,'2026-04-28 02:40:15','Simples','dor no ombro','c8086af5a9a155d9549e85f2900779ffb1da68f3e55b65d684d9958e5db76b4d'),(20,31,18,'2026-04-29 22:05:31','Controle Especial','dor no joelho','ba6911328ef1618d89a87167bffd7affaf56927d3d488a9b1a79bc7df1a76dbe'),(21,16,15,'2026-04-30 06:20:34','Simples','madmom','35a74121cd8ba14b335c4d75a001d73e0f189e2d917aeed2dc470fe2ada7b55c'),(100,9,18,'2026-05-10 13:37:13','Controle Especial','Paciente em observação rigorosa. Jejum absoluto.',NULL),(200,9,18,'2025-05-10 10:00:00','Simples','Tratamento de amigdalite concluído.',NULL),(201,16,18,'2026-03-15 14:20:00','Simples','Recuperação pós-treino',NULL),(300,9,18,'2026-03-01 10:00:00','Simples',NULL,'token_teste_historico'),(301,9,18,'2026-05-05 09:00:00','Simples',NULL,'token_teste_ativo'),(302,9,18,'2026-05-10 08:00:00','Controle Especial',NULL,'token_teste_continuo'),(305,9,18,'2026-05-11 01:37:33','Simples','Tratamento iniciado hoje para teste de enfermagem.',NULL),(306,9,18,'2026-03-30 21:50:48','Azul','Uso contínuo',NULL),(307,9,18,'2026-04-23 22:02:56','Simples','dor no joelho','8118018fb2d456a2e0367cca522fb364341d424cec3ffe47c9b8d06f476a570e'),(308,9,15,'2026-04-23 22:01:57','Simples','gripe','470b3f7e4eafd8dbfe0e2145844de264dd22dc12b888c27a4a6e91ed8618c534'),(309,9,18,'2026-04-23 22:04:29','Simples','dor na canela','233bf431df8ee3528bcf4c223c146271671bf44eff61def1fae0b18f7d0f4e62'),(310,16,22,'2026-04-28 02:40:15','Simples','dor no ombro','c8086af5a9a155d9549e85f2900779ffb1da68f3e55b65d684d9958e5db76b4d'),(311,9,17,'2026-04-23 22:12:29','Simples','caxumba','a28a7da6f9b6282f74622ccea923d8fc5342052a270f17ca30421bc1f54d6266'),(312,31,18,'2026-04-29 22:05:31','Controle Especial','dor no joelho','ba6911328ef1618d89a87167bffd7affaf56927d3d488a9b1a79bc7df1a76dbe'),(313,16,15,'2026-04-30 06:20:34','Simples','madmom','35a74121cd8ba14b335c4d75a001d73e0f189e2d917aeed2dc470fe2ada7b55c'),(315,9,18,'2026-05-10 13:37:13','Controle Especial','Paciente em observação rigorosa. Jejum absoluto.',NULL),(317,9,18,'2025-05-10 10:00:00','Simples','Tratamento de amigdalite concluído.',NULL),(318,16,18,'2026-03-15 14:20:00','Simples','Recuperação pós-treino',NULL),(319,9,18,'2026-03-01 10:00:00','Simples',NULL,'token_teste_historico'),(320,9,18,'2026-05-05 09:00:00','Simples',NULL,'token_teste_ativo'),(321,9,18,'2026-05-10 08:00:00','Controle Especial',NULL,'token_teste_continuo'),(322,9,18,'2026-05-11 01:37:33','Simples','Tratamento iniciado hoje para teste de enfermagem.',NULL),(323,9,11,'2026-06-19 12:11:06','Simples','Esse medicamento pode ter seus efeitos reduzidos devido a ingestão de alcool ou subsâncias, sucos de toranja e também pode entrar em conflito com medicamentos para azia','9d28eed81e2db636eee3cb8ed226477f0a1e5b69f138bc1c514ef58c7d9d2be1');
/*!40000 ALTER TABLE `receitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recuperacao_senha`
--

DROP TABLE IF EXISTS `recuperacao_senha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `codigo` char(4) NOT NULL,
  `data_expiracao` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recuperacao_senha`
--

LOCK TABLES `recuperacao_senha` WRITE;
/*!40000 ALTER TABLE `recuperacao_senha` DISABLE KEYS */;
INSERT INTO `recuperacao_senha` VALUES (1,'med.assist3501@gmail.com','7183','2026-04-28 02:40:39',1),(2,'med.assist3501@gmail.com','5968','2026-04-28 02:44:55',1),(3,'med.assist3501@gmail.com','9543','2026-04-28 02:45:55',1),(4,'med.assist3501@gmail.com','6189','2026-04-28 03:11:00',1),(5,'med.assist3501@gmail.com','8727','2026-04-28 03:13:07',1),(6,'med.assist3501@gmail.com','0497','2026-04-28 03:17:11',1),(7,'med.assist3501@gmail.com','9813','2026-04-29 22:24:37',1),(8,'med.assist3501@gmail.com','5341','2026-04-29 22:24:45',0);
/*!40000 ALTER TABLE `recuperacao_senha` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `triagens`
--

DROP TABLE IF EXISTS `triagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `triagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `status` enum('Aguardando Médico','Atendido','Cancelado') DEFAULT 'Aguardando Médico',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `triagens`
--

LOCK TABLES `triagens` WRITE;
/*!40000 ALTER TABLE `triagens` DISABLE KEYS */;
INSERT INTO `triagens` VALUES (5,12,19,'Colica',37.6,42.00,1.60,82,98,120,80,'Verde','2026-04-07 12:08:55','Atendido'),(6,18,19,'Dor torácica intensa em aperto com irradiação para membro superior esquerdo e mandíbula. Paciente pálido e sudoreico.',36.4,85.00,1.80,115,92,190,110,'Vermelho','2026-05-07 12:26:46','Aguardando Médico'),(7,11,19,'Febre alta iniciada há 6 horas, acompanhada de calafrios intensos (tremedeira), cefaleia e dor no corpo.',39.2,70.50,1.75,108,96,110,70,'Amarelo','2026-05-07 12:27:48','Aguardando Médico'),(8,12,19,'Disúria e polaciúria iniciadas ontem. Ausência de febre ou dor lombar.',36.8,62.00,1.65,78,99,120,80,'Verde','2026-05-07 12:28:08','Aguardando Médico'),(9,22,19,'Coriza, espirros e leve dor de garganta iniciados há 3 dias. Ausência de febre, tosse ou falta de ar.',36.6,90.00,1.85,72,98,115,75,'Azul','2026-05-07 12:51:15','Aguardando Médico'),(10,20,19,'Entorse em tornozelo direito após queda de nível. Apresenta edema leve e dificuldade para deambular. Sem sinais de fratura exposta.',36.2,82.50,1.82,80,99,120,80,'Verde','2026-05-07 12:51:50','Aguardando Médico'),(11,13,19,'Paciente com histórico de asma relatando forte falta de ar, cansaço ao falar e chiado no peito há 2 horas. Fez uso de bombinha em casa sem melhora.',36.5,78.00,1.78,102,91,130,85,'Amarelo','2026-05-07 12:51:55','Aguardando Médico'),(12,12,19,'Disúria e polaciúria iniciadas ontem. Ausência de febre ou dor lombar.',36.8,62.00,1.65,78,99,120,80,'Verde','2026-05-07 12:52:00','Atendido'),(13,26,19,'Reação alérgica após ingestão de frutos do mar. Apresenta placas vermelhas pelo corpo, inchaço nos lábios e sensação de garganta fechando.',36.7,75.00,1.75,125,88,90,60,'Vermelho','2026-05-07 12:53:28','Aguardando Médico'),(14,18,19,'Dor abdominal aguda no quadrante inferior direito, náuseas e vômitos há 4 horas. Suspeita de apendicite.',38.5,78.00,1.75,112,97,140,90,'Laranja','2026-05-10 13:37:13','Aguardando Médico'),(15,32,19,'teste',34.0,62.00,1.15,78,91,45,76,'Amarelo','2026-06-19 10:38:58','Aguardando Médico');
/*!40000 ALTER TABLE `triagens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpf` varchar(14) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `role` enum('admin','medico','enfermeiro','paciente') DEFAULT 'paciente' COMMENT 'Papéis de acesso ao sistema',
  `crm_registro` varchar(50) DEFAULT NULL COMMENT 'Registro CRM do Médico. Deve ser preenchido se o papel role for medico.',
  `coren_registro` varchar(50) DEFAULT NULL COMMENT 'Registro COREN do Enfermeiro/Técnico. Deve ser preenchido se o role for enfermeiro.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_registro` (`crm_registro`),
  UNIQUE KEY `coren_registro` (`coren_registro`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `cpf_2` (`cpf`),
  UNIQUE KEY `cpf_3` (`cpf`),
  UNIQUE KEY `cpf_4` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (9,'143.346.585.45','Auzio Varella','medico@medico','1943-05-03','$2y$10$KoF4rakY2UEe8pWD/LJFkOGlE/4BkS9Zb3FgkjTYJON3cbj83TCEe','medico','123456/SP',NULL),(11,'345.782.567.64','Gustavo Ribeiro','ribeirinho4050@gmail.com','2006-03-11','$2y$10$I/eqa7/eIy1Mm7GerB5KoeLfrlIb.PCNJnFPuaLNBOCL13PJVv.ri','paciente',NULL,NULL),(12,'438.923.965.37','Natalia Torres','loonaily@gmail.com','2006-07-25','$2y$10$JdhO0daWlWjaGF5O13f7IuAxtwheVhsd7aBQlQ2gnI0JmOOCGNgoK','paciente',NULL,NULL),(13,'845.195.289.18','Renato Augusto ramos','Renato@augusto.2015','1991-07-02','$2y$10$SIQOc0tr6Kjl7Ha7usJuJeqF9cVwqYe9xtdPYZirX0HCXlkc3eJMG','paciente',NULL,NULL),(15,'123.456.789.85','Luiz Otávio de Oliveira','luiz@otavio.com.br','2005-09-20','$2y$10$3vpHez3Z8CPeVK89vIl9T.4hdaOQAk70S5YugBOjh2NQ7aTdpClFS','paciente',NULL,NULL),(16,'239.546.211.68','Carlos Chagaz','chagaz@gmail.com','1961-11-08','$2y$10$yydVHZAemPyKCuuILhT4y.S1eRc3rvh8.cJlwV8A3qicX7RbiGnrG','medico','909090/SP',NULL),(18,'439.923.123.54','Marcos Verrati','med.assist3501@gmail.com','1989-05-01','$2y$10$z6h1dFglQ2ph4S8q/k40yuBrrX15U9vF/oZtIWLpKRH0DKRVLYJDC','paciente',NULL,NULL),(19,'853.204.021.45','Florencia Ortega','flor@gmail','1970-01-24','$2y$10$KAekwGRgurX78BfH9lvfDuuGZOFb7hfPOm66sV1UwHP/YJLbIDB96','enfermeiro',NULL,'121212/SP'),(20,'837.237.162.31','Rodrigo Almeida','rodrigo@email','1998-12-02','$2y$10$tQVr/TsLTRY0i/JsFXdjT./YP8eE4OnPxIg00UMyRFzZEo.RzDqYW','paciente',NULL,NULL),(21,'234.012.945.56','rodrigo','rodrigo@email','1998-12-02','$2y$10$2ipN3QKbbcLR/ZvQ9l1yQug/1xjQ9cgmGrzzH1jOLi5gjoHdBRwAy','paciente',NULL,NULL),(22,'132.465.798.10','roger machado','roger@gmail.com','1998-06-17','$2y$10$C55jeTqoMfIRv.5Q58hIo.ujgwT62hNRm7yAAxBXPb2Yh/ledL6hW','paciente',NULL,NULL),(26,'456.897.456.11','vinicius ramos','vinicius.rm@gmail.com','2026-04-08','$2y$10$iorBkLIgbpKvTP1Jff1GxOYrt5ZGW9W.CVP7mF5zwW8smhDrTgqGy','paciente',NULL,NULL),(30,'456.183.456.78','thiago silva pereira','thiago@gerente.com','1988-02-20','$2y$10$hEJnKJR5AZggeSNhcKVadu/rzY5uEHeND.g5pkI58O85l3DzrTrLC','admin',NULL,NULL),(31,'123.466.797.27','kalil mamed','kalil.mamed@medassist.com','0000-00-00','$2y$10$W5I71pDtyUx8aX.M8SVYEOABdjZPTXPurVGbWbMMAdvDTvgbv4v4u','medico','465789-SP',NULL),(32,'412.373.132.54','vinitobias','vini@gmail.com','1984-05-04','$2y$10$0cQQD2nXcugPwEDN95U3HuDVGgqEb5YGRk41GbR3Qk1Ov3UVYCeou','paciente',NULL,NULL),(33,'031.715.394.22','Rafaella cruz','rafa@gmail.com','0000-00-00','$2y$10$Be7NXvtoV/gsPKXOyQqymOJ0vEqOY.UjJfOHeunR.HCxxs8qopcZW','enfermeiro',NULL,'4561231-SP');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'medassistdb'
--

--
-- Dumping routines for database 'medassistdb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-19 12:56:43
