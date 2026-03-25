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
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `exam_attempt_id` bigint(20) unsigned NOT NULL,
  `evaluated_by` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL COMMENT 'pending, completed, evaluated, manual_review',
  `exam_name` varchar(255) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `achieved_score` int(11) NOT NULL,
  `total_score` int(11) NOT NULL,
  `score_percentage` decimal(5,2) DEFAULT NULL,
  `band_score` decimal(3,1) DEFAULT NULL,
  `time_taken_seconds` int(10) unsigned NOT NULL,
  `evaluator_feedback` text DEFAULT NULL,
  `admin_feedback` text DEFAULT NULL,
  `breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`breakdown`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `results_exam_attempt_id_foreign` (`exam_attempt_id`),
  KEY `results_evaluated_by_foreign` (`evaluated_by`),
  KEY `results_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `results_evaluated_by_foreign` FOREIGN KEY (`evaluated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `results_exam_attempt_id_foreign` FOREIGN KEY (`exam_attempt_id`) REFERENCES `exam_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `results_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results`
--

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;
INSERT INTO `results` VALUES (1,1,115,NULL,'completed','IELTS Reading','Reading',0,3,0.00,0.0,0,NULL,NULL,NULL,'2026-03-19 19:27:20','2026-03-19 19:27:20'),(2,1,116,NULL,'completed','IELTS Reading','Reading',1,3,33.33,3.0,0,NULL,NULL,NULL,'2026-03-19 20:28:02','2026-03-19 20:28:02'),(3,1,117,NULL,'completed','IELTS Reading','Reading',1,3,33.33,3.0,0,NULL,NULL,NULL,'2026-03-19 20:28:02','2026-03-19 20:28:02'),(4,1,120,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:10:40','2026-03-22 06:10:40'),(5,1,121,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:20:58','2026-03-22 06:20:58'),(6,1,122,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:24:20','2026-03-22 06:24:20'),(7,1,123,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:27:43','2026-03-22 06:27:43'),(8,1,124,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:38:32','2026-03-22 06:38:32'),(9,1,125,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:38:41','2026-03-22 06:38:41'),(10,1,126,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:39:26','2026-03-22 06:39:26'),(11,1,127,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:39:39','2026-03-22 06:39:39'),(12,1,128,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:39:45','2026-03-22 06:39:45'),(13,1,129,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:39:48','2026-03-22 06:39:48'),(14,1,130,NULL,'completed','IELTS Reading','Reading',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-22 06:39:54','2026-03-22 06:39:54'),(15,1,131,NULL,'completed','Academic Listening','listening',3,3,100.00,9.0,0,NULL,NULL,NULL,'2026-03-23 06:44:21','2026-03-23 06:44:21'),(16,1,132,NULL,'completed','Academic Listening','Listening',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-23 06:47:26','2026-03-23 06:47:26'),(17,1,133,NULL,'completed','Academic Listening','Listening',2,3,66.67,6.0,0,NULL,NULL,NULL,'2026-03-23 07:05:57','2026-03-23 07:05:57');
/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-26  1:41:38
