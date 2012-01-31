-- phpMyAdmin SQL Dump
-- version 2.9.0
-- http://www.phpmyadmin.net
-- 
-- Servidor: hl57.dinaserver.com
-- Tiempo de generación: 22-12-2011 a las 14:20:54
-- Versión del servidor: 5.1.32
-- Versión de PHP: 5.2.11
-- 
-- Base de datos: `bdt`
-- 

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_admin_activity`
-- 

CREATE TABLE `bdt_admin_activity` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `admin_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `category` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `action` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ref_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `note` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- 
-- Volcar la base de datos para la tabla `bdt_admin_activity`
-- 

INSERT INTO `bdt_admin_activity` (`log_id`, `log_date`, `admin_id`, `category`, `action`, `ref_id`, `note`) VALUES 
(1, '2011-12-22 13:40:56', 'EVENT_SYSTEM', 'M', 'M', 'M', NULL),
(2, '2011-12-22 13:40:56', 'EVENT_SYSTEM', 'W', 'W', 'W', NULL),
(3, '2011-12-22 13:40:56', 'EVENT_SYSTEM', 'D', 'D', 'D', NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_categories`
-- 

CREATE TABLE `bdt_categories` (
  `category_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(4) unsigned DEFAULT NULL,
  `description` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

-- 
-- Volcar la base de datos para la tabla `bdt_categories`
-- 

INSERT INTO `bdt_categories` (`category_id`, `parent_id`, `description`) VALUES 
(1, NULL, 'Arte y artesanía'),
(2, NULL, 'Albañilería y construcción'),
(3, NULL, 'Servicios de empresa'),
(4, NULL, 'Cuidado de niños/as'),
(5, NULL, 'InformÃ¡tica'),
(6, NULL, 'Psicopedagogía y terapia'),
(7, NULL, 'Cocina'),
(8, NULL, 'Trabajos de jardinería'),
(9, NULL, 'Servicio técnico'),
(10, NULL, 'Salud y cuidado de personas'),
(11, NULL, 'Trabajos del hogar'),
(12, NULL, 'Otros'),
(13, NULL, 'Música y entretenimiento'),
(14, NULL, 'Cuidado de animales'),
(15, NULL, 'Deportes y animación'),
(16, NULL, 'Enseñanza'),
(17, NULL, 'Transporte'),
(18, NULL, 'Compras y comercio');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_feedback`
-- 

CREATE TABLE `bdt_feedback` (
  `feedback_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_id_author` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_id_about` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `trade_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rating` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`feedback_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- 
-- Volcar la base de datos para la tabla `bdt_feedback`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_feedback_rebuttal`
-- 

CREATE TABLE `bdt_feedback_rebuttal` (
  `rebuttal_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `rebuttal_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feedback_id` mediumint(8) unsigned DEFAULT NULL,
  `member_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`rebuttal_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

-- 
-- Volcar la base de datos para la tabla `bdt_feedback_rebuttal`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_listings`
-- 

CREATE TABLE `bdt_listings` (
  `title` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci,
  `category_code` smallint(4) unsigned NOT NULL DEFAULT '0',
  `member_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rate` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `posting_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire_date` date DEFAULT NULL,
  `reactivate_date` date DEFAULT NULL,
  `type` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`title`,`member_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Volcar la base de datos para la tabla `bdt_listings`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_logins`
-- 

CREATE TABLE `bdt_logins` (
  `member_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `total_failed` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `consecutive_failures` mediumint(3) unsigned NOT NULL DEFAULT '0',
  `last_failed_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_success_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Volcar la base de datos para la tabla `bdt_logins`
-- 

INSERT INTO `bdt_logins` (`member_id`, `total_failed`, `consecutive_failures`, `last_failed_date`, `last_success_date`) VALUES 
('admin', 5, 0, '2011-11-05 00:46:19', '0000-00-00 00:00:00'),
('MaiteBravo', 2, 2, '2011-11-14 12:40:07', '0000-00-00 00:00:00'),
('elmalaka', 1, 0, '2011-11-01 21:15:07', '2011-11-03 00:14:03'),
('alles', 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
('eduMag2000', 1, 0, '2011-11-03 16:13:46', '0000-00-00 00:00:00'),
('julioblanc', 1, 0, '2011-11-05 01:02:29', '2011-11-05 01:02:44'),
('icorney', 3, 0, '2011-11-05 21:11:45', '2011-11-19 21:11:56'),
('Soniahi8', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
('Walter', 1, 0, '2011-11-15 19:21:28', '2011-11-27 23:09:50');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_member`
-- 

CREATE TABLE `bdt_member` (
  `member_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_role` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `security_q` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `security_a` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_note` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_note` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `join_date` date NOT NULL DEFAULT '0000-00-00',
  `expire_date` date DEFAULT NULL,
  `away_date` date DEFAULT NULL,
  `account_type` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_updates` int(3) unsigned NOT NULL DEFAULT '0',
  `balance` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Volcar la base de datos para la tabla `bdt_member`
-- 

INSERT INTO `bdt_member` (`member_id`, `password`, `member_role`, `security_q`, `security_a`, `status`, `member_note`, `admin_note`, `join_date`, `expire_date`, `away_date`, `account_type`, `email_updates`, `balance`) VALUES 
('admin', 'password', '9', NULL, NULL, 'A', NULL, NULL, '2009-07-07', NULL, NULL, '', 1, 0.00);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_news`
-- 

CREATE TABLE `bdt_news` (
  `news_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `sequence` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `expire_date` date DEFAULT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- 
-- Volcar la base de datos para la tabla `bdt_news`
-- 

INSERT INTO `bdt_news` (`news_id`, `title`, `description`, `sequence`, `expire_date`) VALUES 
(6, 'Entrada en servei', 'totbisbal.com és un projecte cooperatiu que aporta un valor de suma a la societat. El banc de temps de la Bisbal d''Empordà i pobles veÃ¯ns, concebut sota aquesta mateixa òptica, vol facilitar l''intercanvi de temps entre els habitants d''aquesta àrea.', 99.0000, '2011-12-12');

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_person`
-- 

CREATE TABLE `bdt_person` (
  `person_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `primary_member` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `directory_list` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mid_name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mother_mn` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone1_area` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone1_number` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone1_ext` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone2_area` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone2_number` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone2_ext` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax_area` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax_number` varchar(7) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax_ext` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street1` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street2` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_city` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_state_code` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_post_code` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_country` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `imagen` varchar(21) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`person_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=150 ;

-- 
-- Volcar la base de datos para la tabla `bdt_person`
-- 

INSERT INTO `bdt_person` (`person_id`, `member_id`, `primary_member`, `directory_list`, `first_name`, `last_name`, `mid_name`, `dob`, `mother_mn`, `email`, `phone1_area`, `phone1_number`, `phone1_ext`, `phone2_area`, `phone2_number`, `phone2_ext`, `fax_area`, `fax_number`, `fax_ext`, `address_street1`, `address_street2`, `address_city`, `address_state_code`, `address_post_code`, `address_country`, `imagen`) VALUES 
(1, 'admin', 'Y', 'Y', 'Administrador', 'del Sistema', 'Jaume', NULL, NULL, 'bdt@totbisbal.com', '972', '640103', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'La Bisbal d''Empordà', '', '', '', NULL);

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_trades`
-- 

CREATE TABLE `bdt_trades` (
  `trade_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `trade_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `member_id_from` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_id_to` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `category` smallint(4) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`trade_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- 
-- Volcar la base de datos para la tabla `bdt_trades`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bdt_uploads`
-- 

CREATE TABLE `bdt_uploads` (
  `upload_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `filename` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`upload_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- 
-- Volcar la base de datos para la tabla `bdt_uploads`
-- 

