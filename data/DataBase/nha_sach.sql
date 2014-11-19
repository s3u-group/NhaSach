-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 19 Novembre 2014 à 08:31
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
-- Structure de la table `cong_no`
--

CREATE TABLE IF NOT EXISTS `cong_no` (
  `id_cong_no` int(11) NOT NULL AUTO_INCREMENT,
  `id_doi_tac` int(11) NOT NULL,
  `ki` date NOT NULL,
  `no_dau_ki` float NOT NULL,
  `no_phat_sinh` float NOT NULL,
  `cong_no_moi` float NOT NULL,
  PRIMARY KEY (`id_cong_no`),
  KEY `fk_cong_no_doi_tac` (`id_doi_tac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ct_hoa_don`
--

CREATE TABLE IF NOT EXISTS `ct_hoa_don` (
  `id_ct_hoa_don` int(11) NOT NULL AUTO_INCREMENT,
  `id_hoa_don` int(11) NOT NULL,
  `id_san_pham` int(11) NOT NULL,
  `gia` float NOT NULL,
  `so_luong` int(11) NOT NULL,
  PRIMARY KEY (`id_ct_hoa_don`),
  KEY `fk_cthoadon_hoadon` (`id_hoa_don`),
  KEY `fk_cthoadon_sanpham` (`id_san_pham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `ct_phieu_nhap`
--

CREATE TABLE IF NOT EXISTS `ct_phieu_nhap` (
  `id_ct_phieu_nhap` int(11) NOT NULL AUTO_INCREMENT,
  `id_phieu_nhap` int(11) NOT NULL,
  `id_san_pham` int(11) NOT NULL,
  `gia_nhap` float NOT NULL,
  `so_luong` int(11) NOT NULL,
  PRIMARY KEY (`id_ct_phieu_nhap`),
  KEY `fk_ctphieunhap_sanpham` (`id_san_pham`),
  KEY `fk_ctphieunhap_phieunhap` (`id_phieu_nhap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `doi_tac`
--

CREATE TABLE IF NOT EXISTS `doi_tac` (
  `id_doi_tac` int(11) NOT NULL AUTO_INCREMENT,
  `ho_ten` varchar(100) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `state` smallint(6) DEFAULT NULL,
  `mo_ta` longtext,
  `dien_thoai_co_dinh` int(11) DEFAULT NULL,
  `di_dong` int(11) DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `loai_doi_tac` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_doi_tac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gia_xuat`
--

CREATE TABLE IF NOT EXISTS `gia_xuat` (
  `id_gia_xuat` int(11) NOT NULL AUTO_INCREMENT,
  `id_san_pham` int(11) NOT NULL,
  `gia_xuat` float NOT NULL,
  `id_kenh_phan_phoi` int(11) NOT NULL,
  PRIMARY KEY (`id_gia_xuat`),
  KEY `fk_giaxuat_sanpham` (`id_san_pham`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hoa_don`
--

CREATE TABLE IF NOT EXISTS `hoa_don` (
  `id_hoa_don` int(11) NOT NULL AUTO_INCREMENT,
  `ma_hoa_don` char(6) NOT NULL,
  `ngay_xuat` date NOT NULL,
  `id_doi_tac` int(11) NOT NULL,
  `id_user_nv` int(11) NOT NULL,
  PRIMARY KEY (`id_hoa_don`),
  KEY `fk_hoadon_doitac` (`id_doi_tac`),
  KEY `fk_hoadon_user` (`id_user_nv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `phieu_chi`
--

CREATE TABLE IF NOT EXISTS `phieu_chi` (
  `id_phieu_chi` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_nv` int(11) NOT NULL,
  `id_cong_no` int(11) NOT NULL,
  `ly_do` longtext,
  PRIMARY KEY (`id_phieu_chi`),
  KEY `fk_phieuchi_user` (`id_user_nv`),
  KEY `fk_phieuchi_congno` (`id_cong_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `phieu_nhap`
--

CREATE TABLE IF NOT EXISTS `phieu_nhap` (
  `id_phieu_nhap` int(11) NOT NULL AUTO_INCREMENT,
  `ma_phieu_nhap` char(6) NOT NULL,
  `ngay_nhap` date NOT NULL,
  `id_doi_tac` int(11) NOT NULL,
  `id_user_nv` int(11) NOT NULL,
  PRIMARY KEY (`id_phieu_nhap`),
  KEY `fk_phieunhap_user` (`id_user_nv`),
  KEY `fk_phieunhap_doitac` (`id_doi_tac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `phieu_thu`
--

CREATE TABLE IF NOT EXISTS `phieu_thu` (
  `id_phieu_thu` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_nv` int(11) NOT NULL,
  `id_cong_no` int(11) NOT NULL,
  `ly_do` longtext,
  PRIMARY KEY (`id_phieu_thu`),
  KEY `fk_phieuthu_user` (`id_user_nv`),
  KEY `fk_phieuthu_congno` (`id_cong_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `san_pham`
--

CREATE TABLE IF NOT EXISTS `san_pham` (
  `id_san_pham` int(11) NOT NULL AUTO_INCREMENT,
  `ma_san_pham` char(255) NOT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `mo_ta` longtext,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `nhan` varchar(255) DEFAULT NULL,
  `id_don_vi_tinh` bigint(20) NOT NULL,
  `id_loai` bigint(20) NOT NULL,
  `ton_kho` float NOT NULL,
  PRIMARY KEY (`id_san_pham`),
  KEY `fk_sanpham_termtaxonomy` (`id_don_vi_tinh`),
  KEY `fk_sanpham_zftermtaxonomy` (`id_loai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(128) NOT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `hoten` varchar(100) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `state` smallint(6) DEFAULT NULL,
  `mo_ta` longtext,
  `dien_thoai_co_dinh` int(11) DEFAULT NULL,
  `di_dong` int(11) DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `loai_tai_khoan` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleId` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  `parent_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `user_role`
--

INSERT INTO `user_role` (`id`, `roleId`, `is_default`, `parent_id`) VALUES
(1, 'khach', 0, NULL),
(2, 'nguoi-dung', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_role_linker`
--

CREATE TABLE IF NOT EXISTS `user_role_linker` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `zf_term`
--

CREATE TABLE IF NOT EXISTS `zf_term` (
  `term_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `term_group` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`term_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Contenu de la table `zf_term`
--

INSERT INTO `zf_term` (`term_id`, `name`, `slug`, `term_group`) VALUES
(1, 'ÄÆ¡n Vá»‹ TÃ­nh', 'don-vi-tinh', 0),
(3, 'ThÃ¹ng', 'thung', 0),
(4, 'CÃ¢y', 'cay', 0),
(5, 'Chai', 'chai', 0),
(6, 'Há»™p', 'hop', 0),
(7, 'Lá»', 'lo', 0),
(8, 'Danh Má»¥c HÃ ng HÃ³a', 'danh-muc-hang-hoa', 0),
(9, 'Dá»¤NG Cá»¤ Há»ŒC SINH', 'dung-cu-hoc-sinh', 0),
(10, 'Táº­p há»c', 'tap-hoc', 0),
(11, 'Viáº¿t', 'viet', 0),
(12, 'Dá»¥ng cá»¥', 'dung-cu', 0),
(13, 'MÃ¡y tÃ­nh há»c sinh', 'may-tinh-hoc-sinh', 0),
(14, 'VÄ‚N PHÃ’NG PHáº¨M', 'van-phong-pham', 0),
(15, 'Giáº¥y in', 'giay-in', 0),
(16, 'BÄƒng keo', 'bang-keo', 0),
(17, 'File Ä‘á»±ng há»“ sÆ¡', 'file-dung-ho-so', 0),
(18, 'Dá»¥ng cá»¥ vÄƒn phÃ²ng', 'dung-cu-van-phong', 0),
(19, 'THIáº¾T Bá»Š TRÆ¯á»œNG Há»ŒC', 'thiet-bi-truong-hoc', 0),
(20, 'Máº§m non', 'mam-non', 0),
(21, 'Tiá»ƒu há»c', 'tieu-hoc', 0),
(22, 'Cáº¥p 2', 'cap-2', 0),
(23, 'Cáº¥p 3', 'cap-3', 0),
(24, 'SÃCH GIÃO KHOA', 'sach-giao-khoa', 0),
(25, 'Lá»›p 1', 'lop-1', 0),
(26, 'Lá»›p 2', 'lop-2', 0),
(27, 'Lá»›p 3', 'lop-3', 0),
(28, 'Lá»›p 4', 'lop-4', 0),
(29, 'Lá»›p 5', 'lop-5', 0),
(30, 'Lá»›p 6', 'lop-6', 0),
(31, 'Lá»›p 7', 'lop-7', 0),
(32, 'Lá»›p 8', 'lop-8', 0),
(33, 'Lá»›p 9', 'lop-9', 0),
(34, 'Lá»›p 10', 'lop-10', 0),
(35, 'Lá»›p 11', 'lop-11', 0),
(36, 'Lá»›p 12', 'lop-12', 0),
(37, 'SÃCH THAM KHáº¢O', 'sach-tham-khao', 0),
(38, 'STK Máº§m non', 'stk-mam-non', 0),
(39, 'STK Tiáº¿u há»c', 'stk-tieu-hoc', 0),
(40, 'STK Cáº¥p 2', 'stk-cap-2', 0),
(41, 'STK Cáº¥p 3', 'stk-cap-3', 0);

-- --------------------------------------------------------

--
-- Structure de la table `zf_term_taxonomy`
--

CREATE TABLE IF NOT EXISTS `zf_term_taxonomy` (
  `term_taxonomy_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) NOT NULL,
  `taxonomy` varchar(200) NOT NULL,
  `description` longtext,
  `parent` bigint(20) DEFAULT NULL,
  `count` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`term_taxonomy_id`),
  KEY `term_id` (`term_id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Contenu de la table `zf_term_taxonomy`
--

INSERT INTO `zf_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1, 1, 'don-vi-tinh', 'Taxonomy', NULL, NULL),
(3, 3, 'don-vi-tinh', 'Sáº£n pháº©m dáº¡ng thÃ¹ng', 1, NULL),
(4, 4, 'don-vi-tinh', 'Sáº£n pháº©m dáº¡ng cÃ¢y nhÆ°: cÃ¢y viáº¿t, cÃ¢y thÆ°á»›c...', 1, NULL),
(5, 5, 'don-vi-tinh', 'Sáº£n pháº©m dáº¡ng chai nhÆ°: chai C2, chai Sting...', 1, NULL),
(6, 6, 'don-vi-tinh', 'Sáº£n pháº©m dáº¡ng há»™p', 1, NULL),
(7, 7, 'don-vi-tinh', 'Sáº£n pháº©m thuá»™c dáº¡ng lá» nhÆ°: lá» má»±c...', 1, NULL),
(8, 8, 'danh-muc-hang-hoa', 'Taxonomy', NULL, NULL),
(9, 9, 'danh-muc-hang-hoa', 'Dá»¤NG Cá»¤ Há»ŒC SINH', 8, NULL),
(10, 10, 'danh-muc-hang-hoa', 'Táº­p há»c', 9, NULL),
(11, 11, 'danh-muc-hang-hoa', 'CÃ¢y viáº¿t', 9, NULL),
(12, 12, 'danh-muc-hang-hoa', 'Dá»¥ng cá»¥', 9, NULL),
(13, 13, 'danh-muc-hang-hoa', 'MÃ¡y tÃ­nh há»c sinh', 9, NULL),
(14, 14, 'danh-muc-hang-hoa', 'VÄ‚N PHÃ’NG PHáº¨M', 8, NULL),
(15, 15, 'danh-muc-hang-hoa', 'Giáº¥y in', 14, NULL),
(16, 16, 'danh-muc-hang-hoa', 'BÄƒng keo', 14, NULL),
(17, 17, 'danh-muc-hang-hoa', 'File Ä‘á»±ng há»“ sÆ¡', 14, NULL),
(18, 18, 'danh-muc-hang-hoa', 'Dá»¥ng cá»¥ vÄƒn phÃ²ng', 14, NULL),
(19, 19, 'danh-muc-hang-hoa', 'THIáº¾T Bá»Š TRÆ¯á»œNG Há»ŒC', 8, NULL),
(20, 20, 'danh-muc-hang-hoa', 'Máº§n non', 19, NULL),
(21, 21, 'danh-muc-hang-hoa', 'Tiá»ƒu há»c', 19, NULL),
(22, 22, 'danh-muc-hang-hoa', 'Cáº¥p 2', 19, NULL),
(23, 23, 'danh-muc-hang-hoa', 'Cáº¥p 3', 19, NULL),
(24, 24, 'danh-muc-hang-hoa', 'SÃCH GIÃO KHOA', 8, NULL),
(25, 25, 'danh-muc-hang-hoa', 'Lá»›p 1', 24, NULL),
(26, 26, 'danh-muc-hang-hoa', 'Lá»›p 2', 24, NULL),
(27, 27, 'danh-muc-hang-hoa', 'Lá»›p 3', 24, NULL),
(28, 28, 'danh-muc-hang-hoa', 'Lá»›p 4', 24, NULL),
(29, 29, 'danh-muc-hang-hoa', 'Lá»›p 5', 24, NULL),
(30, 30, 'danh-muc-hang-hoa', 'Lá»›p 6', 24, NULL),
(31, 31, 'danh-muc-hang-hoa', 'Lá»›p 7', 24, NULL),
(32, 32, 'danh-muc-hang-hoa', 'Lá»›p 8', 24, NULL),
(33, 33, 'danh-muc-hang-hoa', 'Lá»›p 9', 24, NULL),
(34, 34, 'danh-muc-hang-hoa', 'Lá»›p 10', 24, NULL),
(35, 35, 'danh-muc-hang-hoa', 'Lá»›p 11', 24, NULL),
(36, 36, 'danh-muc-hang-hoa', 'Lá»›p 12', 24, NULL),
(37, 37, 'danh-muc-hang-hoa', 'SÃCH THAM KHáº¢O', 8, NULL),
(38, 38, 'danh-muc-hang-hoa', 'STK Máº§m non', 37, NULL),
(39, 39, 'danh-muc-hang-hoa', 'STK Tiáº¿u há»c', 37, NULL),
(40, 40, 'danh-muc-hang-hoa', 'STK Cáº¥p 2', 37, NULL),
(41, 41, 'danh-muc-hang-hoa', 'STK Cáº¥p 3', 37, NULL);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `cong_no`
--
ALTER TABLE `cong_no`
  ADD CONSTRAINT `fk_cong_no_doi_tac` FOREIGN KEY (`id_doi_tac`) REFERENCES `doi_tac` (`id_doi_tac`);

--
-- Contraintes pour la table `ct_hoa_don`
--
ALTER TABLE `ct_hoa_don`
  ADD CONSTRAINT `fk_cthoadon_hoadon` FOREIGN KEY (`id_hoa_don`) REFERENCES `hoa_don` (`id_hoa_don`),
  ADD CONSTRAINT `fk_cthoadon_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`);

--
-- Contraintes pour la table `ct_phieu_nhap`
--
ALTER TABLE `ct_phieu_nhap`
  ADD CONSTRAINT `fk_ctphieunhap_phieunhap` FOREIGN KEY (`id_phieu_nhap`) REFERENCES `phieu_nhap` (`id_phieu_nhap`),
  ADD CONSTRAINT `fk_ctphieunhap_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`);

--
-- Contraintes pour la table `gia_xuat`
--
ALTER TABLE `gia_xuat`
  ADD CONSTRAINT `fk_giaxuat_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`);

--
-- Contraintes pour la table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `fk_hoadon_doitac` FOREIGN KEY (`id_doi_tac`) REFERENCES `doi_tac` (`id_doi_tac`),
  ADD CONSTRAINT `fk_hoadon_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `phieu_chi`
--
ALTER TABLE `phieu_chi`
  ADD CONSTRAINT `fk_phieuchi_congno` FOREIGN KEY (`id_cong_no`) REFERENCES `cong_no` (`id_cong_no`),
  ADD CONSTRAINT `fk_phieuchi_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `fk_phieunhap_doitac` FOREIGN KEY (`id_doi_tac`) REFERENCES `doi_tac` (`id_doi_tac`),
  ADD CONSTRAINT `fk_phieunhap_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `phieu_thu`
--
ALTER TABLE `phieu_thu`
  ADD CONSTRAINT `fk_phieuthu_congno` FOREIGN KEY (`id_cong_no`) REFERENCES `cong_no` (`id_cong_no`),
  ADD CONSTRAINT `fk_phieuthu_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_sanpham_termtaxonomy` FOREIGN KEY (`id_don_vi_tinh`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`),
  ADD CONSTRAINT `fk_sanpham_zftermtaxonomy` FOREIGN KEY (`id_loai`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`);

--
-- Contraintes pour la table `zf_term_taxonomy`
--
ALTER TABLE `zf_term_taxonomy`
  ADD CONSTRAINT `zf_term_taxonomy_ibfk_1` FOREIGN KEY (`term_id`) REFERENCES `zf_term` (`term_id`),
  ADD CONSTRAINT `zf_term_taxonomy_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
