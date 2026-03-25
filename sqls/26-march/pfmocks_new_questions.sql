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
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) unsigned NOT NULL,
  `type` enum('mcq_single','mcq_multiple','text','essay','audio','speaking') NOT NULL,
  `question_header` text NOT NULL,
  `passage` longtext DEFAULT NULL,
  `audio_url` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `marks` int(11) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_module_id_foreign` (`module_id`),
  CONSTRAINT `questions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,1,'text','This is Academic Reading Question.\r\nBelow is paragraph','What is lorem ipsum, and when did publishers begin using it?\r\n\r\nThe standard lorem ipsum passage has been a printer\'s friend for centuries. Like stock photos today, it served as a placeholder for actual content. The original text comes from Cicero\'s philosophical work \"De Finibus Bonorum et Malorum,\" written in 45 BC.\r\n\r\nThe use of the lorem ipsum passage dates back to the 1500s. When printing presses required painstaking hand-setting of type, workers needed something to show clients how their pages would look. To save time, they turned to Cicero\'s words, creating sample books filled with preset paragraphs.\r\n\r\nHowever, it wasn\'t until the 1960s that the passage became common when Letraset revolutionized the advertising industry with its transfer sheets. These innovative sheets allowed designers to apply pre-printed lorem ipsum text in various fonts and formats directly onto their mockups and prototypes.',NULL,NULL,13,0,NULL,NULL,NULL),(2,4,'essay','This is GT Writing Question.\r\nBelow is the question','Some people believe that in a city, the best way to travel is by car, while other people argue that bicycles are a better way of travelling in a city.\r\n\r\nDiscuss both views and give your opinion.',NULL,NULL,9,1,NULL,NULL,NULL),(3,4,'essay','This is GT writing Task 2 Question','In Britain, when someone gets old they often go to live in a home with other old people where there are nurses to look after them. Sometimes the government has to pay for this care.\r\n\r\nWho do you think should pay for this care, the government or the family?\r\n\r\nGive reasons for your answer and include any relevant examples from your own knowledge or experience.',NULL,NULL,9,2,NULL,NULL,NULL),(4,5,'essay','This is part-2 question','Write about yourself',NULL,NULL,9,2,NULL,NULL,NULL),(5,1,'text','The Future of fish','The face of the ocean has changed completely since the first commercial fishers cast their nets and hooks over a thousand years ago. Fisheries intensified over the centuries, but even by the nineteenth century it was still felt, justifiably, that the plentiful resources of the sea were for the most part beyond the reach of fishing, and so there was little need to restrict fishing or create protected areas. The twentieth century heralded an escalation in fishing intensity that is unprecedented in the history of the oceans, and modern fishing technologies leave fish no place to hide. Today, the only refuges from fishing are those we deliberately create. Unhappily, the sea trails far behind the land in terms of the area and the quality of protection given.\r\n\r\nFor centuries, as fishing and commerce have expanded, we have held onto the notion that the sea is different from the land. We still view it as a place where people and nations should be free to come and go at will, as well as somewhere that should be free for us to exploit. Perhaps this is why we have been so reluctant to protect the sea. On land, protected areas have proliferated as human populations have grown. Here, compared to the sea, we have made greater headway in our struggle to maintain the richness and variety of wildlife and landscape. Twelve percent of the world’s land is now contained in protected areas, whereas the corresponding figure for the sea is but three-fifths of one percent. Worse still, most marine protected areas allow some fishing to continue. Areas off-limits to all exploitation cover something like one five-thousandth of the total area of the world’s seas.\r\n\r\nToday, we are belatedly coming to realise that ‘natural refuges’ from fishing have played a critical role in sustaining fisheries, and maintaining healthy and diverse marine ecosystems. This does not mean that marine reserves can rebuild fisheries on their own – other management measures are also required for that. However, places that are off-limits to fishing constitute the last and most important part of our package of reform for fisheries management. They underpin and enhance all our other efforts. There are limits to protection though.',NULL,NULL,40,0,NULL,NULL,NULL),(6,1,'text','Coinage in Ancient Greece','A.  There are more than 170 official national currencies currently in circulation around the world and while they may differ greatly in value, most show a high degree of commonality when it comes to their design. Typically, a coin or banknote will feature the effigy of a notable politician, monarch or other personality from the country of origin on one side and a recognisable state symbol (e.g. a building or an animal) on the reverse. This pattern, which has been around for more than 21 centuries, originated in ancient Greece.\r\n\r\nB.  Prior to the invention of legal tender, most transactions in the ancient world took the form of trading a product or service for another. As sea trade grew in the Mediterranean, however, the once-popular barter system became hard to maintain for two reasons: firstly, because it was tricky to calculate the value of each item or service in relation to another, and secondly, because carrying large goods (such as animals) on boats to do trade with neighbouring cities was difficult and inconvenient. Therefore, the need soon arose for a commonly recognised unit that would represent a set value-what is known today as a currency. As Aristotle explains in Politics, metal coins naturally became the most popular option due to the fact that they were easy to carry, and didn’t run the risk of expiring. According to ancient Greek historian Herodotus, the first coins were invented in 620 BC in the town of Lydia, although some theorise that they actually originated in the city of Ionia. (Coins had already existed for nearly 400 years in China, unbeknownst to Europeans.)\r\n\r\nC.  Much like with every other form of ancient Greek art, the history of ancient Greek coins can be divided into three distinct chronological periods: the Archaic (600-480 BC), the Classic (480-330 BC) and the Hellenistic Period (330-1st century BC). As ancient Greece was not a united country like today, but rather comprised of many independent city-states known as poleis, each state produced its own coins. The island of Aegina was the first to mint silver coins, perhaps adopting the new system upon witnessing how successfully it had facilitated trade for the lonians. Aegina being the head of a confederation of seven states, it quickly influenced other city-states in the Mediterranean and the new method of trade soon became widespread. Up until approximately 510 BC, when Athens began producing its own coin, the Aegina coin – which featured a turtle on its surface was the most predominant in the region.',NULL,NULL,40,0,NULL,NULL,NULL),(7,6,'essay','This is IELTS Listening test','N/A','',NULL,40,0,NULL,NULL,NULL),(10,6,'essay','Listening test part-2','N/A',NULL,NULL,40,0,NULL,NULL,NULL),(11,7,'essay','Speaking IELTS','N/A',NULL,NULL,9,1,NULL,NULL,NULL),(13,7,'essay','Speaking IELTS part-2','N/A',NULL,NULL,9,2,NULL,NULL,NULL);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-26  1:41:22
