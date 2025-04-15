-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 14 avr. 2025 à 16:40
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `forum_ivan`
--
CREATE DATABASE IF NOT EXISTS `forum_ivan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `forum_ivan`;

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id_category` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id_category`, `name`) VALUES
(1, 'Technologie'),
(2, 'Science'),
(3, 'Art');

-- --------------------------------------------------------
--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nickName` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'ROLE_USER',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registrationDate` datetime NOT NULL,
  `isBan` tinyint(1) DEFAULT '0',
  `dateBan` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `nickName`, `role`, `email`, `password`, `registrationDate`, `isBan`, `dateBan`) VALUES
(1, 'Alice', 'ROLE_USER', 'alice@example.com', 'hashed_password_1', '2025-04-09 10:43:04', 1, '2025-04-12 14:11:25'),
(2, 'Bob', 'ROLE_USER', 'bob@example.com', 'hashed_password_2', '2025-04-09 10:43:04', 0, NULL),
(3, 'Charlie', 'ROLE_USER', 'charlie@example.com', 'hashed_password_3', '2025-04-09 10:43:04', 0, NULL),
(4, 'Dave', 'ROLE_USER', 'dave@example.com', 'hashed_password_4', '2025-04-09 10:43:04', 0, NULL),
(5, 'Eve', 'ROLE_USER', 'eve@example.com', 'hashed_password_5', '2025-04-09 10:43:04', 0, NULL),
(7, 'IvanPinot', 'ROLE_ADMIN', 'ivan.veltz@live.fr', '$2y$10$r0qr35OpQuR6V4ckSNPcdONnxGhuAV6TtaIEUlqbWZv1L1nkdYIsG', '2025-04-10 12:21:34', 0, NULL),
(8, 'Ivan', 'ROLE_USER', 'ivanpinot68@gmail.com', '$2y$10$GFuuNryZs9OeLf46BK0fgOiIDA0FcxTeoKTt3WOb7H0jOlSytOJai', '2025-04-10 14:49:24', 0, '2025-04-12 11:11:11');



--
-- Structure de la table `topic`
--

DROP TABLE IF EXISTS `topic`;
CREATE TABLE IF NOT EXISTS `topic` (
  `id_topic` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `creationDate` datetime NOT NULL,
  `closed` tinyint(1) NOT NULL,
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`id_topic`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `topic`
--

INSERT INTO `topic` (`id_topic`, `title`, `creationDate`, `closed`, `user_id`, `category_id`) VALUES
(1, 'L\'avenir de l\'IA', '2025-04-09 10:43:04', 0, 1, 1),
(3, 'Découvertes récentes en astrophysique', '2025-04-09 10:43:04', 0, 3, 2),
(4, 'Biologie moléculaire et innovations', '2025-04-09 10:45:04', 0, 4, 2),
(6, 'Photographie et composition', '2025-04-09 10:44:04', 0, 1, 3),
(15, 'Test2', '2025-04-11 09:25:55', 1, 7, 1);

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id_post` int NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `creationDate` datetime NOT NULL,
  `user_id` int NOT NULL,
  `topic_id` int NOT NULL,
  PRIMARY KEY (`id_post`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id_post`, `text`, `creationDate`, `user_id`, `topic_id`) VALUES
(1, 'L\'IA va transformer notre monde dans les 10 prochaines années.', '2025-04-09 10:43:04', 2, 1),
(3, 'Les exoplanètes sont fascinantes, les dernières découvertes sont incroyables.', '2025-04-09 10:43:04', 4, 3),
(4, 'CRISPR est une révolution, mais quelles sont les implications éthiques ?', '2025-04-09 10:43:04', 5, 4),
(6, 'Une bonne composition en photographie fait toute la différence.', '2025-04-09 10:43:04', 2, 6),
(7, 'L’IA commence à surpasser les humains dans certaines tâches spécifiques.', '2025-04-08 14:23:00', 3, 1),
(8, 'D’accord, mais l’intuition humaine reste irremplaçable, non ?', '2025-04-09 09:45:00', 5, 1),
(11, 'Les récentes images du télescope James Webb sont fascinantes !', '2025-04-07 16:15:00', 1, 3),
(12, 'Oui ! Ça change notre compréhension des galaxies.', '2025-04-08 10:05:00', 3, 3),
(13, 'CRISPR pourrait révolutionner la médecine.', '2025-04-07 22:40:00', 5, 4),
(14, 'Mais les questions éthiques sont délicates, il faut un cadre strict.', '2025-04-08 08:55:00', 2, 4),
(38, 'Salut2', '2025-04-11 09:24:17', 7, 15);

-- --------------------------------------------------------



--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`id_topic`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `topic`
--
ALTER TABLE `topic`
  ADD CONSTRAINT `topic_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id_category`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `topic_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
