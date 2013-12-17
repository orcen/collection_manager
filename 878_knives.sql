-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Počítač: sql.cybers.cz
-- Vygenerováno: Úte 17. pro 2013, 22:27
-- Verze serveru: 5.1.49-3-log
-- Verze PHP: 5.3.3-7+squeeze17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `878_knives`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`uid`, `pid`, `name`, `description`) VALUES
(1, 0, 'Pevné nože', ''),
(2, 0, 'Zavírací nože', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `text` text NOT NULL,
  `creator` varchar(64) NOT NULL,
  `crstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `comments`
--

INSERT INTO `comments` (`uid`, `pid`, `title`, `text`, `creator`, `crstamp`) VALUES
(1, 4, 'Super nůž', 'Mám tenhle nůž už přes rok a jsem s ním naprosto spokojený. Kvalitní zpracování, super padne do ruky a řeže jak čert.', 'orcen', '2011-03-14 12:42:12');

-- --------------------------------------------------------

--
-- Struktura tabulky `details`
--

CREATE TABLE IF NOT EXISTS `details` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `param` varchar(16) NOT NULL,
  `value` tinytext NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

--
-- Vypisuji data pro tabulku `details`
--

INSERT INTO `details` (`uid`, `pid`, `param`, `value`) VALUES
(1, 1, 'manufactory', 'Wenger'),
(2, 1, 'overall-length', '150'),
(3, 1, 'blade-length', '65'),
(4, 1, 'thickness', ''),
(5, 1, 'weight', '53'),
(6, 1, 'hardness', ''),
(7, 1, 'steel', '440'),
(8, 1, 'handle-length', '85'),
(9, 1, 'handle-material', 'ořechové dřevo'),
(10, 1, 'images', '_vyr_135evowood-17.jpg\r\n_vyrp11_135evowood1.jpg\r\n_vyrp12_135evowood2.jpg'),
(14, 2, 'overall-length', '150'),
(13, 2, 'manufactory', 'Wenger'),
(15, 2, 'blade-length', '65'),
(16, 2, 'thickness', ''),
(17, 2, 'weight', '72'),
(18, 2, 'hardness', ''),
(19, 2, 'steel', '440'),
(20, 2, 'handle-length', '85'),
(21, 2, 'handle-material', 'ořechové dřevo'),
(22, 2, 'images', '_vyr_134evowood-14.jpg'),
(23, 3, 'manufactory', 'Esee Knives'),
(24, 3, 'overall-length', '160'),
(25, 3, 'blade-length', '64'),
(26, 3, 'thickness', '16'),
(27, 3, 'weight', ''),
(28, 3, 'hardness', '57'),
(29, 3, 'steel', '1095 Carbon'),
(30, 3, 'handle-length', '96'),
(31, 3, 'handle-material', 'volitelně paracord, micarta'),
(32, 3, 'images', 'izula-black-desert.jpg'),
(33, 4, 'manufactory', 'ESEE'),
(34, 4, 'overall-length', '210'),
(35, 4, 'blade-length', '80'),
(36, 4, 'thickness', '3'),
(37, 4, 'weight', ''),
(38, 4, 'hardness', ''),
(39, 4, 'steel', '1095 Carbon'),
(40, 4, 'handle-length', '130'),
(41, 4, 'handle-material', 'různé (micarta, G10)'),
(42, 4, 'images', 'ESEE-3.jpg'),
(43, 5, 'manufactory', 'Ka-Bar'),
(44, 5, 'overall-length', '364'),
(45, 5, 'blade-length', '230'),
(46, 5, 'thickness', '5.6'),
(47, 5, 'weight', '750'),
(48, 5, 'hardness', '56'),
(49, 5, 'steel', 'ocel'),
(50, 5, 'handle-length', '134'),
(51, 5, 'handle-material', 'drevo'),
(52, 5, 'images', 'Bowie3.jpg'),
(53, 5, 'images', 'kabar-large-heavy.jpg'),
(54, 6, 'manufactory', 'Victorinox'),
(55, 6, 'overall-length', '222'),
(56, 6, 'blade-length', '111'),
(57, 6, 'thickness', '1.5'),
(58, 6, 'weight', '125'),
(59, 6, 'hardness', ''),
(60, 6, 'steel', ''),
(61, 6, 'handle-length', '111'),
(62, 6, 'handle-material', 'drevo'),
(63, 6, 'images', 'small_0_8463_3_Trailmaster_kopie(1).jpg');

-- --------------------------------------------------------

--
-- Struktura tabulky `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `cruser` int(11) NOT NULL DEFAULT '1',
  `hidden` tinyint(1) NOT NULL DEFAULT '1',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `crstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Vypisuji data pro tabulku `items`
--

INSERT INTO `items` (`uid`, `cid`, `name`, `description`, `cruser`, `hidden`, `public`, `crstamp`) VALUES
(1, 2, 'EvoWood 17', '-legendární švýcarské nože v nové řadě Wenger EvoWood\r\n-střenka z tmavého ořechového dřeva s logem Wenger\r\n-přilnavý, na dotek příjemný povrch\r\n-ergonomický tvar pro lepší uchopení nože\r\n-každý nůž je originální díky nezaměnitelné kresbě dřeva\r\n-nůžky\r\n-pilka na dřevo\r\n-pilník na nehty\r\n-otvírák na konzervy\r\n-otvírák na lahve s plochým šroubovákem\r\n-vývrtka\r\n-bodec\r\n-kroužek pro zavěšení', 0, 0, 1, '2011-03-10 11:03:32'),
(2, 2, 'EvoWood 14', '-legendární švýcarské nože v nové řadě Wenger EvoWood\r\n-střenka z tmavého ořechového dřeva s logem Wenger\r\n-přilnavý, na dotek příjemný povrch\r\n-ergonomický tvar pro lepší uchopení nože\r\n-každý nůž je originální díky nezaměnitelné kresbě dřeva\r\n-nůžky\r\n-pilník na nehty\r\n-otvírák na konzervy\r\n-otvírák na lahve s plochým šroubovákem\r\n-vývrtka\r\n-bodec\r\n-kroužek pro zavěšení', 0, 0, 1, '2011-03-10 11:03:32'),
(3, 1, 'Izula', 'Malý pevný nůž, nazývaný Neck-Knife\r\nKonstrukce full-tang, dodávaný bez příložek, které lze ale u výrobce objednat.\r\nJe možné rukojeť omotat paracordem.', 0, 0, 1, '2011-03-10 11:04:20'),
(4, 1, 'RC3', 'Po Izule a Izule II je RC3 nejmenší z nožů rodiny ESEE. Je to velmi bytelný nůž ve fulltang konstrukci. Jde objednat v nekolika barevných variantách, jako je černá, zelená, oranžová, písková atd. A dále také s různými materiály a barvami rukojetí(micarta, G10; černá, čedá, oranžová).', 0, 0, 1, '2011-03-10 11:03:32'),
(5, 1, 'Large Heavy Bowie', 'Masivní pevný nůž s délkou čepele 230mm. Vhodný pro sekání, kopání a nepřesné krájení. Síla čepele není vhodná pro precizní řezání, tudíž cibuli nenakrájíte, ale prase s ním porazíte vždy.', 1, 0, 1, '2011-03-14 07:27:18'),
(6, 2, 'Trailmaster', 'velký zavírací nůž \r\nkřížový šroubovák \r\notvírák na konzervy s \r\n- malým šroubovákem \r\notvírák na láhve se \r\n- šroubovákem \r\n- odstraňovačem izolace z drátů \r\nvýstružník, průbojník \r\nnerezový kroužek na klíče \r\npinzeta \r\npárátko \r\npilka na dřevo', 1, 0, 1, '2011-03-14 08:16:43');

-- --------------------------------------------------------

--
-- Struktura tabulky `materials`
--

CREATE TABLE IF NOT EXISTS `materials` (
  `name` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `part` varchar(16) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `materials`
--

INSERT INTO `materials` (`name`, `description`, `part`) VALUES
('1095 Carbon', '', 'blade'),
('Micarta', '', 'handle'),
('440C', '', 'blade'),
('440A', '', 'blade'),
('snakewood', '', 'handle'),
('rosewood', '', 'handle'),
('G10', '', 'handle'),
('VG10', '', 'blade');

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `passwd` varchar(35) NOT NULL,
  `email` varchar(64) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`uid`, `name`, `passwd`, `email`) VALUES
(1, 'orcen', '', 'orcener@gmail.com');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
