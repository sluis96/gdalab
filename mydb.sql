-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 13-01-2022 a las 06:14:18
-- Versión del servidor: 5.7.19
-- Versión de PHP: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mydb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `communes`
--

DROP TABLE IF EXISTS `communes`;
CREATE TABLE IF NOT EXISTS `communes` (
  `id_com` int(11) NOT NULL AUTO_INCREMENT,
  `id_reg` int(11) NOT NULL,
  `description` varchar(90) NOT NULL,
  `status` enum('A','I','trash') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id_com`,`id_reg`),
  KEY `fk_communes_region_idx` (`id_reg`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `communes`
--

INSERT INTO `communes` (`id_com`, `id_reg`, `description`, `status`) VALUES
(1, 1, 'San Luis', 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `dni` varchar(45) NOT NULL COMMENT 'Documento de Identidad',
  `id_reg` int(11) NOT NULL,
  `id_com` int(11) NOT NULL,
  `email` varchar(120) NOT NULL COMMENT 'Correo Electrónico',
  `name` varchar(45) NOT NULL COMMENT 'Nombre',
  `last_name` varchar(45) NOT NULL COMMENT 'Apellido',
  `address` varchar(255) DEFAULT NULL COMMENT 'Dirección',
  `date_reg` datetime NOT NULL COMMENT 'Fecha y hora del registro',
  `status` enum('A','I','trash') NOT NULL DEFAULT 'A' COMMENT 'estado del registro:\nA\r\n: Activo\nI : Desactivo\ntrash : Registro eliminado',
  PRIMARY KEY (`dni`,`id_reg`,`id_com`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `fk_customers_communes1_idx` (`id_com`,`id_reg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regions`
--

DROP TABLE IF EXISTS `regions`;
CREATE TABLE IF NOT EXISTS `regions` (
  `id_reg` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(90) NOT NULL,
  `status` enum('A','I','trash') NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id_reg`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `regions`
--

INSERT INTO `regions` (`id_reg`, `description`, `status`) VALUES
(1, 'Lima', 'A'),
(2, 'Arequipa', 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
