-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2014 at 07:12 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nha_sach`
--

-- --------------------------------------------------------

--
-- Table structure for table `cong_no`
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
-- Table structure for table `ct_hoa_don`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `ct_hoa_don`
--

INSERT INTO `ct_hoa_don` (`id_ct_hoa_don`, `id_hoa_don`, `id_san_pham`, `gia`, `so_luong`) VALUES
(1, 43, 24, 15750, 200),
(2, 43, 24, 15750, 200),
(3, 44, 24, 15750, -200),
(4, 44, 24, 15750, -200),
(5, 44, 24, 15750, -200),
(6, 45, 26, 5500, 84),
(7, 45, 26, 5500, 84),
(8, 45, 26, 5500, 84),
(9, 45, 26, 5500, 84);

-- --------------------------------------------------------

--
-- Table structure for table `ct_phieu_nhap`
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
-- Table structure for table `doi_tac`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `doi_tac`
--

INSERT INTO `doi_tac` (`id_doi_tac`, `ho_ten`, `dia_chi`, `email`, `mo_ta`, `dien_thoai_co_dinh`, `di_dong`, `hinh_anh`, `website`, `twitter`, `loai_doi_tac`, `id_kenh_phan_phoi`) VALUES
(1, 'Phan van thanh', 'Cau ke', 'phanvanthanhda10tt@gmail.com', 'Phan van thanh Phan van thanh', 0, 1699580585, NULL, NULL, NULL, 45, 39),
(2, 'Luu Kim Loan', 'Tra cu', 'luukimloan@gmail.com', 'Luu Kim Loan', NULL, 1699580585, NULL, NULL, NULL, 45, 40),
(3, 'Huá»³nh Sa Quang', 'ChÃ¢u ThÃ nh, TrÃ  Vinh', 'hsqs3u@gmail.com', '<p>zxcvbnm,<br></p>', 743628504, 987309606, 'photo_default.png', 'chohailua.vn', 'hsqtwitter.com', 45, 41),
(4, 'Phan VÄƒn Tiá»n', 'ChÃ¢u Ä‘iá»n, Cáº§u kÃ¨, TrÃ  vinh', 'phanvantien@gmail.com', 'zxcvbnm', 2147483647, 1655479171, 'photo_default.png', 'phanvantien.vn', 'phanvantien.com', 45, 42),
(5, 'Nguyá»…n Minh ÄÆ°Æ¡ng', 'TrÃ  vinh', 'nguyenminhduong@gmail.com', '<p>Giáº£ng viÃªn<br></p>', 746582709, 987654321, 'photo_default.png', 'nguyenminhduong.vn', 'nguyenminhduong.com', 46, 43);

-- --------------------------------------------------------

--
-- Table structure for table `gia_xuat`
--

CREATE TABLE IF NOT EXISTS `gia_xuat` (
  `id_gia_xuat` int(11) NOT NULL AUTO_INCREMENT,
  `id_san_pham` int(11) DEFAULT NULL,
  `gia_xuat` float NOT NULL,
  `id_kenh_phan_phoi` bigint(20) NOT NULL,
  PRIMARY KEY (`id_gia_xuat`),
  KEY `fk_giaxuat_sanpham` (`id_san_pham`),
  KEY `fk_gia_xuat_term_taxonomy` (`id_kenh_phan_phoi`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `gia_xuat`
--

INSERT INTO `gia_xuat` (`id_gia_xuat`, `id_san_pham`, `gia_xuat`, `id_kenh_phan_phoi`) VALUES
(12, 24, 15759, 39),
(13, 24, 16509, 40),
(14, 24, 17259, 41),
(15, 24, 18009, 42),
(16, 24, 18759, 43),
(17, 25, 26250, 39),
(18, 25, 27500, 40),
(19, 25, 28750, 41),
(20, 25, 30000, 42),
(21, 25, 31250, 43),
(22, 26, 5250, 39),
(23, 26, 5500, 40),
(24, 26, 5750, 41),
(25, 26, 6000, 42),
(26, 26, 6250, 43);

-- --------------------------------------------------------

--
-- Table structure for table `hoa_don`
--

CREATE TABLE IF NOT EXISTS `hoa_don` (
  `id_hoa_don` int(11) NOT NULL AUTO_INCREMENT,
  `ma_hoa_don` char(6) NOT NULL,
  `ngay_xuat` date NOT NULL,
  `id_doi_tac` int(11) NOT NULL,
  `id_user_nv` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_hoa_don`),
  KEY `fk_hoadon_doitac` (`id_doi_tac`),
  KEY `fk_hoadon_user` (`id_user_nv`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

--
-- Dumping data for table `hoa_don`
--

INSERT INTO `hoa_don` (`id_hoa_don`, `ma_hoa_don`, `ngay_xuat`, `id_doi_tac`, `id_user_nv`) VALUES
(43, '161815', '2014-11-26', 1, NULL),
(44, '163019', '2014-11-26', 1, NULL),
(45, '042747', '2014-11-27', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `phieu_chi`
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
-- Table structure for table `phieu_nhap`
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
-- Table structure for table `phieu_thu`
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
-- Table structure for table `san_pham`
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
  `gia_nhap` float NOT NULL,
  PRIMARY KEY (`id_san_pham`),
  KEY `fk_sanpham_termtaxonomy` (`id_don_vi_tinh`),
  KEY `fk_sanpham_zftermtaxonomy` (`id_loai`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `san_pham`
--

INSERT INTO `san_pham` (`id_san_pham`, `ma_san_pham`, `ten_san_pham`, `mo_ta`, `hinh_anh`, `nhan`, `id_don_vi_tinh`, `id_loai`, `ton_kho`, `gia_nhap`) VALUES
(24, 'sgkto1', 'ToÃ¡n lá»›p 1', '<p>ToÃ¡n lá»›p 1<br></p>', 'photo_default.png', 'SGK', 50, 24, 400, 15000),
(25, 'sgkto2', 'ToÃ¡n lá»›p 2', '<p>ToÃ¡n lá»›p 2<br></p>', 'photo_default.png', 'SGK', 50, 24, 83, 25000),
(26, 'sgkto3', 'ToÃ¡n lá»›p 3', '<p>ToÃ¡n lá»›p 3<br></p>', 'photo_default.png', 'SGK', 50, 24, -252, 5000);

-- --------------------------------------------------------

--
-- Table structure for table `user`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `display_name`, `hoten`, `dia_chi`, `email`, `state`, `mo_ta`, `dien_thoai_co_dinh`, `di_dong`, `hinh_anh`, `website`, `twitter`, `loai_tai_khoan`) VALUES
(1, 'phanvanthanh', 'fasdf', 'Phan Van Thanh', 'Phan Van Thanh', 'Cau ke', 'phanvanthanhda10tt@gmail.com', 0, 'fnmdfasdf', 74, 1699580585, 'photo_default.png', NULL, NULL, 48);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleId` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL,
  `parent_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `roleId`, `is_default`, `parent_id`) VALUES
(1, 'khach', 0, NULL),
(2, 'nguoi-dung', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_role_linker`
--

CREATE TABLE IF NOT EXISTS `user_role_linker` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zf_term`
--

CREATE TABLE IF NOT EXISTS `zf_term` (
  `term_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `term_group` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`term_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `zf_term`
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
(14, 'VÄ‚N PHÃ’NG PHáº¨M', 'van-phong-pham', 0),
(19, 'THIáº¾T Bá»Š TRÆ¯á»œNG Há»ŒC', 'thiet-bi-truong-hoc', 0),
(24, 'SÃCH GIÃO KHOA', 'sach-giao-khoa', 0),
(37, 'SÃCH THAM KHáº¢O', 'sach-tham-khao', 0),
(38, 'KÃªnh phÃ¢n phá»‘i', 'kenh-phan-phoi', 0),
(39, 'Äiá»ƒm bÃ¡n', 'diem-ban', 0),
(40, 'TrÆ°á»ng há»c', 'truong-hoc', 0),
(41, 'CÆ¡ quan', 'co-quan', 0),
(42, 'Cá»­a hÃ ng', 'cua-hang', 0),
(43, 'BÃ¡n láº½', 'ban-le', 0),
(44, 'Äá»‘i tÃ¡c', 'doi-tac', 0),
(45, 'KhÃ¡ch hÃ ng', 'khach-hang', 0),
(46, 'NhÃ  cung cáº¥p', 'nha-cung-cap', 0),
(47, 'Loáº¡i TÃ i Khoan', 'loai-tai-khoan', 0),
(48, 'NhÃ¢n viÃªn há»‡ thá»‘ng', 'nhan-vien-he-thong', 0),
(49, 'NhÃ¢n viÃªn giang hÃ ng', 'nhan-vien-giang-hang', 0),
(50, 'Cuá»‘n', 'cuon', 0);

-- --------------------------------------------------------

--
-- Table structure for table `zf_term_taxonomy`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `zf_term_taxonomy`
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
(14, 14, 'danh-muc-hang-hoa', 'VÄ‚N PHÃ’NG PHáº¨M', 8, NULL),
(19, 19, 'danh-muc-hang-hoa', 'THIáº¾T Bá»Š TRÆ¯á»œNG Há»ŒC', 8, NULL),
(24, 24, 'danh-muc-hang-hoa', 'SÃCH GIÃO KHOA', 8, NULL),
(37, 37, 'danh-muc-hang-hoa', 'SÃCH THAM KHáº¢O', 8, NULL),
(38, 38, 'kenh-phan-phoi', 'Taxonomy', NULL, NULL),
(39, 39, 'kenh-phan-phoi', '5', 38, NULL),
(40, 40, 'kenh-phan-phoi', '10', 38, NULL),
(41, 41, 'kenh-phan-phoi', '15', 38, NULL),
(42, 42, 'kenh-phan-phoi', '20', 38, NULL),
(43, 43, 'kenh-phan-phoi', '25', 38, NULL),
(44, 44, 'doi-tac', 'Taxonomy', NULL, NULL),
(45, 45, 'doi-tac', 'KhÃ¡c hÃ ng', 44, NULL),
(46, 46, 'doi-tac', 'NhÃ  cung cáº¥p', 44, NULL),
(47, 47, 'loai-tai-khoan', 'Taxonomy', NULL, NULL),
(48, 48, 'loai-tai-khoan', 'NhÃ¢n viÃªn há»‡ thá»‘ng', 47, NULL),
(49, 49, 'loai-tai-khoan', 'NhÃ¢n viÃªn giang hÃ ng', 47, NULL),
(50, 50, 'don-vi-tinh', 'Cuá»‘n sÃ¡ch', 1, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cong_no`
--
ALTER TABLE `cong_no`
  ADD CONSTRAINT `fk_cong_no_doi_tac` FOREIGN KEY (`id_doi_tac`) REFERENCES `doi_tac` (`id_doi_tac`);

--
-- Constraints for table `ct_hoa_don`
--
ALTER TABLE `ct_hoa_don`
  ADD CONSTRAINT `fk_cthoadon_hoadon` FOREIGN KEY (`id_hoa_don`) REFERENCES `hoa_don` (`id_hoa_don`),
  ADD CONSTRAINT `fk_cthoadon_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`);

--
-- Constraints for table `ct_phieu_nhap`
--
ALTER TABLE `ct_phieu_nhap`
  ADD CONSTRAINT `fk_ctphieunhap_phieunhap` FOREIGN KEY (`id_phieu_nhap`) REFERENCES `phieu_nhap` (`id_phieu_nhap`),
  ADD CONSTRAINT `fk_ctphieunhap_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`);

--
-- Constraints for table `gia_xuat`
--
ALTER TABLE `gia_xuat`
  ADD CONSTRAINT `fk_gia_xuat_term_taxonomy` FOREIGN KEY (`id_kenh_phan_phoi`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`),
  ADD CONSTRAINT `fk_giaxuat_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`);

--
-- Constraints for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `fk_hoadon_doitac` FOREIGN KEY (`id_doi_tac`) REFERENCES `doi_tac` (`id_doi_tac`),
  ADD CONSTRAINT `fk_hoadon_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `phieu_chi`
--
ALTER TABLE `phieu_chi`
  ADD CONSTRAINT `fk_phieuchi_congno` FOREIGN KEY (`id_cong_no`) REFERENCES `cong_no` (`id_cong_no`),
  ADD CONSTRAINT `fk_phieuchi_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `phieu_nhap`
--
ALTER TABLE `phieu_nhap`
  ADD CONSTRAINT `fk_phieunhap_doitac` FOREIGN KEY (`id_doi_tac`) REFERENCES `doi_tac` (`id_doi_tac`),
  ADD CONSTRAINT `fk_phieunhap_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `phieu_thu`
--
ALTER TABLE `phieu_thu`
  ADD CONSTRAINT `fk_phieuthu_congno` FOREIGN KEY (`id_cong_no`) REFERENCES `cong_no` (`id_cong_no`),
  ADD CONSTRAINT `fk_phieuthu_user` FOREIGN KEY (`id_user_nv`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_sanpham_termtaxonomy` FOREIGN KEY (`id_don_vi_tinh`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`),
  ADD CONSTRAINT `fk_sanpham_zftermtaxonomy` FOREIGN KEY (`id_loai`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`);

--
-- Constraints for table `zf_term_taxonomy`
--
ALTER TABLE `zf_term_taxonomy`
  ADD CONSTRAINT `zf_term_taxonomy_ibfk_1` FOREIGN KEY (`term_id`) REFERENCES `zf_term` (`term_id`),
  ADD CONSTRAINT `zf_term_taxonomy_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
