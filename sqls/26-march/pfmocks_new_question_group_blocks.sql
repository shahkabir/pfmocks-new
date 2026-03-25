-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pfmocks_new
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
-- Table structure for table `question_group_blocks`
--

DROP TABLE IF EXISTS `question_group_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_group_blocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_group_id` bigint(20) unsigned NOT NULL,
  `instruction_text` text NOT NULL COMMENT 'Instruction shown before this block of questions',
  `question_option_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`question_option_ids`)),
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `question_group_blocks_question_group_id_index` (`question_group_id`),
  CONSTRAINT `question_group_blocks_question_group_id_foreign` FOREIGN KEY (`question_group_id`) REFERENCES `question_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_group_blocks`
--

LOCK TABLES `question_group_blocks` WRITE;
/*!40000 ALTER TABLE `question_group_blocks` DISABLE KEYS */;
INSERT INTO `question_group_blocks` VALUES (1,1,'Instruction for Q1,2','[1,2,3,4,5,6,7]',1,NULL,NULL),(3,1,'Ins for Q-3,4','[11,12,13,14,15,16,17]',2,NULL,NULL),(4,2,'Ans in Yes/No/NotGiven','[18,19,20]',0,NULL,NULL),(5,2,'Ins for MCQ multiple','[22,23,24]',1,NULL,NULL),(6,3,'Answer the questions below with words taken from Reading Passage.\r\n\r\nUse NO MORE THAN TO WORDS for each answer.','[21,25,27,28]',0,NULL,NULL),(7,3,'Look at the following descriptions (Questions 31–35) and the list of people below.<br/>Match each statement with the correct person, A–G.<br/>Write the correct letter, A–G, in boxes 31–35 on your answer sheet.<br/><table style=\"width:300px; border:1px solid #999; border-collapse:collapse; background:#d9edf7; padding:15px;\">\n    <thead>\n        <tr>\n            <th colspan=\"2\" style=\"text-align:center; font-size:18px; padding:10px; border-bottom:1px solid #999;\">\n                List of People\n            </th>\n        </tr>\n    </thead>\n    <tbody>\n        <tr>\n            <td style=\"font-weight:bold; color:#007bff; width:40px; padding:6px;\">A</td>\n            <td style=\"padding:6px;\">Ctesibius</td>\n        </tr>\n        <tr>\n            <td style=\"font-weight:bold; color:#007bff; padding:6px;\">B</td>\n            <td style=\"padding:6px;\">Arab engineers</td>\n        </tr>\n        <tr>\n            <td style=\"font-weight:bold; color:#007bff; padding:6px;\">C</td>\n            <td style=\"padding:6px;\">da Vinci</td>\n        </tr>\n        <tr>\n            <td style=\"font-weight:bold; color:#007bff; padding:6px;\">D</td>\n            <td style=\"padding:6px;\">Maillardet</td>\n        </tr>\n        <tr>\n            <td style=\"font-weight:bold; color:#007bff; padding:6px;\">E</td>\n            <td style=\"padding:6px;\">Vaucanson</td>\n        </tr>\n        <tr>\n            <td style=\"font-weight:bold; color:#007bff; padding:6px;\">F</td>\n            <td style=\"padding:6px;\">Merlin</td>\n        </tr>\n        <tr>\n            <td style=\"font-weight:bold; color:#007bff; padding:6px;\">G</td>\n            <td style=\"padding:6px;\">Jaquet-Droz</td>\n        </tr>\n    </tbody>\n</table>','[29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63]',0,NULL,NULL),(8,4,'This is part-1','[64,65,66]',1,NULL,NULL),(9,5,'This is part-2','[67,69,70,71,72,73,74]',1,NULL,NULL),(10,6,'Part 1 – Introduction & Interview','[75,76,77]',1,NULL,NULL),(11,7,'Part 2 -','[78,79]',1,NULL,NULL);
/*!40000 ALTER TABLE `question_group_blocks` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-26  1:41:37
