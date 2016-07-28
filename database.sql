-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `engines`;
CREATE TABLE `engines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `use` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `elo` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `engines` (`id`, `use`, `name`, `elo`, `path`, `created_at`, `updated_at`) VALUES
(1,	'y',	'Stockfish 09-06-13 64bit',	'3337',	'/usr/games/stockfish',	'2016-04-30 19:32:16',	'2016-04-30 19:32:16'),
(2,	'y',	'Gull 3 x64',	'3199',	'/home/ryan/Documents/research/thesis/CAI-ITB/UCI-Engine/Gull/src/Gull',	'2016-04-30 19:32:16',	'2016-04-30 19:32:16'),
(3,	'y',	'Critter v1.6a 64-bit',	'3111',	'/home/ryan/Documents/research/thesis/CAI-ITB/UCI-Engine/critter_1.6a_linux/critter-16a-64bit',	'2016-04-30 19:32:16',	'2016-04-30 19:32:16'),
(4,	'y',	'Toga II 3.0 UCI based on Fruit 2',	'2854',	'/usr/games/toga2',	'2016-04-30 19:32:16',	'2016-04-30 19:32:16'),
(5,	'y',	'Glaurung 2.2',	'2785',	'/usr/games/glaurung',	'2016-04-30 19:32:16',	'2016-04-30 19:32:16'),
(6,	'y',	'Spike 1.2 Turin (Build 69)',	'2745',	'/home/ryan/Documents/research/thesis/CAI-ITB/UCI-Engine/Spike_12_linux/spike',	'2016-04-30 19:32:16',	'2016-04-30 19:32:16'),
(7,	'y',	'Fruit 2.1 UCI',	'2693',	'/usr/games/fruit',	'2016-04-30 19:32:16',	'2016-04-30 19:32:16'),
(8,	'y',	'Lozza',	'-',	'node /home/ryan/Documents/research/thesis/CAI-ITB/UCI-Engine/lozza.js',	'2016-05-11 11:28:28',	'2016-05-11 11:28:28'),
(9,	'y',	'GNU Chess 6.1.1',	'2513',	'/home/ryan/Documents/research/learn/chess/gnuchess-6.2.2/src/gnuchessu',	'2016-07-17 06:39:20',	'2016-07-17 06:39:20'),
(11,	'n',	'donna-4.0-linux-64',	'2653',	'/home/ryan/Documents/research/learn/chess/donna-4.0-linux-64',	'2016-07-27 13:24:27',	'2016-07-27 13:24:27');

DROP TABLE IF EXISTS `engine_puzzle`;
CREATE TABLE `engine_puzzle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `engine` int(10) unsigned NOT NULL,
  `puzzle` int(11) NOT NULL,
  `answer_depth_plus_0` varchar(64) NOT NULL,
  `answer_depth_plus_1` varchar(64) NOT NULL,
  `answer_depth_plus_2` varchar(64) NOT NULL,
  `answer_depth_plus_3` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `engine` (`engine`),
  KEY `puzzle` (`puzzle`),
  CONSTRAINT `engine_puzzle_ibfk_1` FOREIGN KEY (`engine`) REFERENCES `engines` (`id`),
  CONSTRAINT `engine_puzzle_ibfk_2` FOREIGN KEY (`puzzle`) REFERENCES `puzzles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `puzzles`;
CREATE TABLE `puzzles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `fen` varchar(128) NOT NULL,
  `answer` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2016-07-28 06:22:07
