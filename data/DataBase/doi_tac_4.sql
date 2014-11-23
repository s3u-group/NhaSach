-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Dim 23 Novembre 2014 à 15:28
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `nha_sach`
--

-- --------------------------------------------------------

--
-- Structure de la table `doi_tac`
--

CREATE TABLE IF NOT EXISTS `doi_tac` (
  `id_doi_tac` int(11) NOT NULL AUTO_INCREMENT,
  `ho_ten` varchar(100) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mo_ta` longtext,
  `dien_thoai_co_dinh` int(11) DEFAULT NULL,
  `di_dong` int(11) DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `loai_doi_tac` bigint(20) DEFAULT NULL,
  `id_kenh_phan_phoi` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_doi_tac`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `doi_tac`
--

INSERT INTO `doi_tac` (`id_doi_tac`, `ho_ten`, `dia_chi`, `email`, `mo_ta`, `dien_thoai_co_dinh`, `di_dong`, `hinh_anh`, `website`, `twitter`, `loai_doi_tac`, `id_kenh_phan_phoi`) VALUES
(1, 'Phan van thanh', 'Cau ke', 'phanvanthanhda10tt@gmail.com', 'Phan van thanh Phan van thanh', 0, 1699580585, NULL, NULL, NULL, 45, 39),
(2, 'Luu Kim Loan', 'Tra cu', 'luukimloan@gmail.com', 'Luu Kim Loan', NULL, 1699580585, NULL, NULL, NULL, 45, 40);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
