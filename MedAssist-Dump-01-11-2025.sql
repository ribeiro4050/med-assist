CREATE DATABASE  IF NOT EXISTS `medassistdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `medassistdb`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: medassistdb
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
  PRIMARY KEY (`id`),
  KEY `receita_id` (`receita_id`),
  CONSTRAINT `itens_receita_ibfk_1` FOREIGN KEY (`receita_id`) REFERENCES `receitas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itens_receita`
--

LOCK TABLES `itens_receita` WRITE;
/*!40000 ALTER TABLE `itens_receita` DISABLE KEYS */;
INSERT INTO `itens_receita` VALUES (1,5,'morfina','30 mg','30 comprimidos','tomar 1 comprimido a cada 4 horas');
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
  PRIMARY KEY (`id`),
  KEY `medico_id` (`medico_id`),
  KEY `paciente_id` (`paciente_id`),
  CONSTRAINT `receitas_ibfk_1` FOREIGN KEY (`medico_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `receitas_ibfk_2` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receitas`
--

LOCK TABLES `receitas` WRITE;
/*!40000 ALTER TABLE `receitas` DISABLE KEYS */;
INSERT INTO `receitas` VALUES (1,8,3,'2025-11-01 12:30:20','Simples','ste medicamento não deve ser usado em caso de hipersensibilidade ao paracetamol ou a qualquer outro componente da fórmula'),(2,8,3,'2025-11-01 12:30:58','Simples','ste medicamento não deve ser usado em caso de hipersensibilidade ao paracetamol ou a qualquer outro componente da fórmula'),(3,8,4,'2025-11-01 12:33:34','Amarela',NULL),(5,9,10,'2025-11-01 13:35:45','Amarela','contraindicada em diversas situações, principalmente em casos de depressão respiratória pré-existente e obstruções gastrointestinais, como o íleo paralítico.');
/*!40000 ALTER TABLE `receitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'paciente' COMMENT 'Papel do usuário: medico, enfermeiro, assistente, paciente, admin.',
  `crm_registro` varchar(50) DEFAULT NULL COMMENT 'Registro CRM do Médico. Deve ser preenchido se o papel role for medico.',
  `coren_registro` varchar(50) DEFAULT NULL COMMENT 'Registro COREN do Enfermeiro/Técnico. Deve ser preenchido se o role for enfermeiro.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_registro` (`crm_registro`),
  UNIQUE KEY `coren_registro` (`coren_registro`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (3,'Gustavo de novo','gustavo@gmail.com','2025-09-03','$2y$10$w59RUSoH0QKTl1OP6gjb7.oRj.oRvcCC/RQQBQF3BuH6qs3zuhBMG','paciente',NULL,NULL),(4,'teste','email@email.com','2012-12-12','$2y$10$LC5TTxybTRk53q7UujJm4eymmqEMqqyFPAbwDp/4AHN9.d04r/JrW','paciente',NULL,NULL),(8,'teste','123@123','2006-12-12','$2y$10$voF2I2LHe.ic6BkX9KlMWe.1dU9lqUFCRkEORDuTf0j1HkdQcD1vO','paciente',NULL,NULL),(9,'medico teste','medico@medico','2012-12-12','$2y$10$KoF4rakY2UEe8pWD/LJFkOGlE/4BkS9Zb3FgkjTYJON3cbj83TCEe','medico','123456/SP',NULL),(10,'paciente teste','paciente@teste','2002-02-21','$2y$10$L0bXZfYKsc78rgg.ktMolujhR6eCcy6CkFs5vOqwjpA0kPNBW.qcq','paciente',NULL,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-01  9:59:53
