-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2014 at 09:46 AM
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
  `du_no` float NOT NULL,
  PRIMARY KEY (`id_cong_no`),
  KEY `fk_cong_no_doi_tac` (`id_doi_tac`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `cong_no`
--

INSERT INTO `cong_no` (`id_cong_no`, `id_doi_tac`, `ki`, `no_dau_ki`, `no_phat_sinh`, `du_no`) VALUES
(1, 15, '2014-11-01', 2000000, 3000000, 4000000),
(2, 16, '2014-11-05', 5000000, 4000000, 1000000),
(3, 15, '2014-11-30', 4000000, 2000000, 3000000),
(5, 20, '2014-11-01', 3000000, 4000000, 2000000),
(8, 20, '2014-12-01', 2000000, 39265000, 40865000),
(9, 20, '2014-12-02', 40865000, 39265000, 30000),
(10, 20, '2014-12-02', 30000, 39265000, 39290600),
(11, 20, '2014-12-02', 39290600, 29300000, 0),
(12, 20, '2014-12-03', 0, 400000, 300000),
(13, 20, '2014-12-03', 300000, 21000000, 444),
(14, 15, '2014-12-01', 3000000, 53750000, 0),
(16, 16, '2014-11-30', 1000000, 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `ct_hoa_don`
--

INSERT INTO `ct_hoa_don` (`id_ct_hoa_don`, `id_hoa_don`, `id_san_pham`, `gia`, `so_luong`) VALUES
(1, 13, 27, 1000, 5),
(2, 13, 28, 3000, 5),
(3, 14, 27, 10000, 5),
(4, 14, 28, 11000, 10),
(5, 15, 29, 4000, 5),
(6, 15, 30, 5000, 5),
(7, 16, 27, 10000, 5),
(8, 16, 29, 20000, 5),
(9, 16, 28, 30000, 1),
(10, 16, 30, 50000, 1),
(11, 17, 27, 10000, 5),
(12, 17, 28, 20000, 5),
(13, 18, 27, 5000, 925),
(14, 18, 28, 0, 1895),
(15, 19, 27, 25000, 1000),
(16, 19, 28, 23750, 1000),
(17, 20, 27, 11000, 1000),
(18, 20, 29, 5500, 100);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `ct_phieu_nhap`
--

INSERT INTO `ct_phieu_nhap` (`id_ct_phieu_nhap`, `id_phieu_nhap`, `id_san_pham`, `gia_nhap`, `so_luong`) VALUES
(25, 13, 27, 15000, 200),
(26, 13, 28, 25000, 300),
(27, 14, 27, 15000, 200),
(28, 14, 28, 25000, 300),
(29, 15, 27, 15000, 200),
(30, 15, 28, 25000, 300),
(31, 16, 27, 15000, 200),
(32, 16, 28, 25000, 300),
(33, 17, 28, 25000, 300),
(34, 17, 29, 5000, 400),
(35, 18, 27, 20000, 800),
(36, 18, 28, 19000, 700),
(37, 19, 27, 10000, 10),
(38, 19, 28, 30000, 10),
(39, 31, 27, 15000, 2),
(40, 32, 27, 15000, 2),
(41, 33, 27, 15000, 2),
(42, 34, 27, 15000, 2),
(43, 36, 29, 5000, 4),
(44, 37, 29, 5000, 4),
(45, 38, 29, 5000, 4),
(46, 39, 29, 5000, 4),
(47, 40, 29, 5000, 4),
(48, 41, 27, 15000, 2),
(49, 41, 28, 25000, 3),
(50, 41, 29, 5000, 4);

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
  `ngay_dang_ky` date NOT NULL,
  PRIMARY KEY (`id_doi_tac`),
  KEY `fk_doitac_zf_term_taxonomy` (`loai_doi_tac`),
  KEY `ho_ten` (`ho_ten`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `doi_tac`
--

INSERT INTO `doi_tac` (`id_doi_tac`, `ho_ten`, `dia_chi`, `email`, `mo_ta`, `dien_thoai_co_dinh`, `di_dong`, `hinh_anh`, `website`, `twitter`, `loai_doi_tac`, `id_kenh_phan_phoi`, `ngay_dang_ky`) VALUES
(15, 'KH Phan VÄƒn Thanh', 'áº¤p ChÃ¢u HÆ°ng, XÃ£ ChÃ¢u Äiá»n, Huyá»‡n Cáº§u KÃ¨, Tá»‰nh TrÃ  Vinh', 'phanvanthanhda10tt@gmail.com', '<p>LÃ  sinh viÃªn chuyÃªn ngÃ nh CÃ´ng nghá»‡ ThÃ´ng tin má»›i ra trÆ°á»ng.</p>', 2147483647, 1699580585, 'photo_default.png', 'phanvanthanhda10tt.vn', 'phanvanthanhda10tt.twitter', 45, 43, '2014-11-01'),
(16, 'KH Tráº§n Minh Hiáº¿u', 'ChÃ¢u ThÃ nh, TrÃ  Vinh', 'tranminhhieu@gmail.com', '<p>LÃ  giáº£ng viÃªn trÆ°á»ng Äáº¡i há»c TrÃ  Vinh</p>', 2147483647, 939353325, 'photo_default.png', 'tranminhhieu.com', 'tranminhhieu.twitter', 45, 40, '2014-11-02'),
(17, 'KH Huá»³nh Sa Quang', 'ChÃ¢u ThÃ nh, TrÃ  Vinh', 'hsqs3u@gmail.com', '<p>LÃ  giÃ¡m Ä‘á»‘c cÃ´ng ty S3U</p>', 987309606, 2147483647, 'photo_default.png', 'chohailua.vn', 'hsqtwitter.com', 45, 41, '2014-11-02'),
(18, 'KH Huá»³nh Thá»‹ NhÆ° Ngá»c', 'CÃ ng Long, TrÃ  Vinh', 'huynhthinhungoc@gmail.com', 'LÃ  thÆ° kÃ½ cá»§a cÃ´ng ty S3U', 2147483647, 987654321, 'photo_default.png', 'huynhthinhungoc.vn', 'huynhthinhungoc.twitter', 45, 43, '2014-11-03'),
(19, 'KH LÆ°u Kim Loan', 'Tiá»ƒu Cáº§n, TrÃ  Vinh', 'luukimloan@gmail.com', '<p>LÃ  Láº­p TrÃ¬nh ViÃªn sÃ¡ng giÃ¡ nháº¥t</p>', 9879876, 9765987, 'photo_default.png', 'luukimloan.vn', 'luukimloan.twitter', 45, 42, '2014-11-01'),
(20, 'NCC Nguyá»…n Minh ÄÆ°Æ¡ng', 'VÅ©ng Lim, Vá»‰nh Long', 'nguyenminhduong@gmail.com', '<p>LÃ  NhÃ  cung cáº¥p tÃ´m khÃ´ sá»‘ 01 trÃ  vinh</p>', 989875887, 989875887, 'photo_default.png', 'nguyenminhduong.vn', 'nguyenminhduong.twitter.com', 46, 39, '2014-11-14'),
(21, 'KH Kim Ngá»c Tuyá»n', 'TÃ¢n SÆ¡n, TrÃ  CÃº', 'kimngoctuyen@gmail.com', 'LÃ  khÃ¡ch hÃ ng á»Ÿ TrÃ  CÃº', 2147483647, 1698732526, 'bc7cfbe0f01286edd8765c7de5594c79_1 (1).jpg', 'kimngoctuyen.vn', 'kimngoctuyen.twitter', 0, 43, '2014-12-03');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `gia_xuat`
--

INSERT INTO `gia_xuat` (`id_gia_xuat`, `id_san_pham`, `gia_xuat`, `id_kenh_phan_phoi`) VALUES
(1, 27, 15750, 39),
(2, 27, 16500, 40),
(3, 27, 17250, 41),
(4, 27, 18000, 42),
(5, 27, 18750, 43),
(6, 28, 26250, 39),
(7, 28, 27500, 40),
(8, 28, 28750, 41),
(9, 28, 30000, 42),
(10, 28, 31250, 43),
(11, 29, 5250, 39),
(12, 29, 5500, 40),
(13, 29, 5750, 41),
(14, 29, 6000, 42),
(15, 29, 6250, 43),
(16, 30, 0, 39),
(17, 30, 0, 40),
(18, 30, 0, 41),
(19, 30, 0, 42),
(20, 30, 0, 43),
(21, 31, 0, 39),
(22, 31, 0, 40),
(23, 31, 0, 41),
(24, 31, 0, 42),
(25, 31, 0, 43),
(26, 32, 0, 39),
(27, 32, 0, 40),
(28, 32, 0, 41),
(29, 32, 0, 42),
(30, 32, 0, 43),
(31, 33, 0, 39),
(32, 33, 0, 40),
(33, 33, 0, 41),
(34, 33, 0, 42),
(35, 33, 0, 43);

-- --------------------------------------------------------

--
-- Table structure for table `hoa_don`
--

CREATE TABLE IF NOT EXISTS `hoa_don` (
  `id_hoa_don` int(11) NOT NULL AUTO_INCREMENT,
  `ma_hoa_don` char(255) NOT NULL,
  `ngay_xuat` date NOT NULL,
  `id_doi_tac` int(11) NOT NULL,
  `id_user_nv` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id_hoa_don`),
  KEY `fk_hoadon_doitac` (`id_doi_tac`),
  KEY `fk_hoadon_user` (`id_user_nv`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `hoa_don`
--

INSERT INTO `hoa_don` (`id_hoa_don`, `ma_hoa_don`, `ngay_xuat`, `id_doi_tac`, `id_user_nv`, `status`) VALUES
(13, '1214-0013', '2014-12-01', 15, NULL, 1),
(14, '1214-0014', '2014-11-01', 15, NULL, 1),
(15, '1214-0015', '2013-03-01', 15, NULL, 1),
(16, '1214-0016', '2013-04-01', 17, NULL, 0),
(17, '1214-0017', '2014-09-30', 15, NULL, 1),
(18, '1214-0018', '2014-12-01', 15, NULL, 1),
(19, '1214-0019', '2014-12-03', 15, NULL, 1),
(20, '1214-0020', '2014-12-04', 16, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `phieu_chi`
--

CREATE TABLE IF NOT EXISTS `phieu_chi` (
  `id_phieu_chi` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_nv` int(11) NOT NULL,
  `id_cong_no` int(11) NOT NULL,
  `ly_do` longtext,
  `so_tien` float NOT NULL,
  `ngay_thanh_toan` date NOT NULL,
  PRIMARY KEY (`id_phieu_chi`),
  KEY `fk_phieuchi_user` (`id_user_nv`),
  KEY `fk_phieuchi_congno` (`id_cong_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `phieu_chi`
--

INSERT INTO `phieu_chi` (`id_phieu_chi`, `id_user_nv`, `id_cong_no`, `ly_do`, `so_tien`, `ngay_thanh_toan`) VALUES
(1, 1, 5, 'Có tiền thì trả chứ sao', 5000000, '2014-12-01'),
(2, 1, 8, 'CÃ³ thÃ¬ tráº£', 399998, '2014-12-02'),
(3, 1, 9, 'CÃ³ tiá»n thÃ¬ tráº£', 80100000, '2014-12-02'),
(4, 1, 10, 'CÃ³  tiá»n thÃ¬ tráº£', 4444, '2014-12-02'),
(5, 1, 11, 'CÃ³ tiá»n thÃ¬ tráº£, GiÃ u mÃ ', 68590600, '2014-12-03'),
(6, 1, 12, '', 100000, '2014-12-03'),
(7, 1, 13, '', 213000, '2014-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `phieu_nhap`
--

CREATE TABLE IF NOT EXISTS `phieu_nhap` (
  `id_phieu_nhap` int(11) NOT NULL AUTO_INCREMENT,
  `ma_phieu_nhap` char(255) NOT NULL,
  `ngay_nhap` date NOT NULL,
  `id_doi_tac` int(11) NOT NULL,
  `id_user_nv` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id_phieu_nhap`),
  KEY `fk_phieunhap_user` (`id_user_nv`),
  KEY `fk_phieunhap_doitac` (`id_doi_tac`),
  KEY `ma_phieu_nhap` (`ma_phieu_nhap`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `phieu_nhap`
--

INSERT INTO `phieu_nhap` (`id_phieu_nhap`, `ma_phieu_nhap`, `ngay_nhap`, `id_doi_tac`, `id_user_nv`, `status`) VALUES
(13, '072546', '2014-12-01', 20, 1, 1),
(14, '073058', '2014-12-01', 20, 1, 1),
(15, '1214-0015', '2014-12-02', 20, 1, 1),
(16, '1214-0016', '2014-12-02', 20, 1, 1),
(17, '1214-0017', '2014-12-02', 20, 1, 1),
(18, '1214-0018', '2014-12-03', 20, 1, 1),
(19, '1214-0019', '2014-12-03', 20, 1, 1),
(20, '041747', '2014-12-08', 20, 11, 0),
(21, '042130', '2014-12-08', 20, 11, 0),
(22, '042546', '2014-12-08', 20, 11, 0),
(23, '043120', '2014-12-08', 20, 11, 0),
(24, '1214-0024', '2014-12-08', 20, 11, 0),
(25, '1214-0025', '2014-12-08', 20, 11, 0),
(26, '1214-0026', '2014-12-08', 20, 11, 0),
(27, '1214-0027', '2014-12-08', 20, 11, 0),
(28, '1214-0028', '2014-12-08', 20, 11, 0),
(29, '1214-0029', '2014-12-08', 20, 11, 0),
(30, '1214-0030', '2014-12-08', 20, 11, 0),
(31, '1214-0031', '2014-12-08', 20, 11, 0),
(32, '1214-0032', '2014-12-08', 20, 11, 0),
(33, '1214-0033', '2014-12-08', 20, 11, 0),
(34, '1214-0034', '2014-12-08', 20, 11, 0),
(35, '1214-0035', '2014-12-08', 20, 11, 0),
(36, '1214-0036', '2014-12-08', 20, 11, 0),
(37, '1214-0037', '2014-12-08', 20, 11, 0),
(38, '1214-0038', '2014-12-08', 20, 11, 0),
(39, '1214-0039', '2014-12-08', 20, 11, 0),
(40, '1214-0040', '2014-12-08', 20, 11, 0),
(41, '1214-0041', '2014-12-08', 20, 11, 0);

-- --------------------------------------------------------

--
-- Table structure for table `phieu_thu`
--

CREATE TABLE IF NOT EXISTS `phieu_thu` (
  `id_phieu_thu` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_nv` int(11) NOT NULL,
  `id_cong_no` int(11) NOT NULL,
  `ly_do` longtext,
  `so_tien` float NOT NULL,
  `ngay_thanh_toan` date NOT NULL,
  PRIMARY KEY (`id_phieu_thu`),
  KEY `fk_phieuthu_user` (`id_user_nv`),
  KEY `fk_phieuthu_congno` (`id_cong_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `phieu_thu`
--

INSERT INTO `phieu_thu` (`id_phieu_thu`, `id_user_nv`, `id_cong_no`, `ly_do`, `so_tien`, `ngay_thanh_toan`) VALUES
(1, 1, 1, 'Ngta trả thì lấy', 1000000, '2014-11-30'),
(2, 1, 2, 'Ngta trả thì lấy', 8000000, '2014-11-30'),
(3, 1, 3, 'Ngta trả thì lấy', 3000000, '2014-12-01'),
(4, 1, 14, 'CÃ³ tiá»n thÃ¬ tráº£ chá»© lÃ½ do gÃ¬ ná»¯a', 56750000, '2014-12-03'),
(6, 1, 16, '', 1000000, '2014-12-03');

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
  KEY `fk_sanpham_zftermtaxonomy` (`id_loai`),
  KEY `ma_san_pham` (`ma_san_pham`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `san_pham`
--

INSERT INTO `san_pham` (`id_san_pham`, `ma_san_pham`, `ten_san_pham`, `mo_ta`, `hinh_anh`, `nhan`, `id_don_vi_tinh`, `id_loai`, `ton_kho`, `gia_nhap`) VALUES
(27, 'sgkto1', 'SGK ToÃ¡n Lá»›p 01', '<p>SGK ToÃ¡n Lá»›p 01<br></p>', 'photo_default.png', 'SGK', 50, 37, 10, 15000),
(28, 'sgkto2', 'SGK ToÃ¡n Lá»›p 02', '<p>SGK ToÃ¡n Lá»›p 02<br></p>', 'photo_default.png', 'SGK', 50, 24, 15, 25000),
(29, 'sgkto3', 'SGK ToÃ¡n Lá»›p 03', '<p>SGK ToÃ¡n Lá»›p 03<br></p>', 'photo_default.png', 'SGK', 50, 24, 20, 5000),
(30, 'sgkto4', 'SGK ToÃ¡n Lá»›p 04', '<p>SGK ToÃ¡n Lá»›p 04<br></p>', 'photo_default.png', 'SGK', 50, 24, 4, 4000),
(31, 'sgkto5', 'SGK ToÃ¡n Lá»›p 05', 'SÃ¡ch giÃ¡o khoa toÃ¡n lá»›p 02', '2f084e518946ac46a2dd461ace9bdb3d_000.jpg', 'SGK', 50, 37, 0, 0),
(32, 'sgkto6', 'SGK ToÃ¡n Lá»›p 06', '<p>SGK ToÃ¡n lá»›p 06</p>', 'bf3a3144dc122fe7e8bda74d4ae7fa3e_00.jpg', 'SGK', 50, 24, 0, 0),
(33, 'sgkto7', 'SGK ToÃ¡n Lá»›p 07', '<p>SGK ToÃ¡n lá»›p 07</p>', 'photo_default.png', 'SGK', 50, 24, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `state` smallint(6) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `dien_thoai_co_dinh` varchar(12) DEFAULT NULL,
  `di_dong` varchar(12) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `display_name`, `password`, `state`, `ho_ten`, `dia_chi`, `mo_ta`, `dien_thoai_co_dinh`, `di_dong`, `twitter`) VALUES
(1, NULL, NULL, NULL, '', 0, '', NULL, NULL, NULL, NULL, NULL),
(11, NULL, 'loan@gmail.com', 'Loan', '$2y$14$Xks2ibFloM6HLsNh7WlUDO76581pGVDUM1PODOkj7qHlJ5fVREQNS', 0, 'LÆ°u Kim Loan', 'TrÃ  Vinh', '', 'sd', '1234', 'sdfg');

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

--
-- Dumping data for table `user_role_linker`
--

INSERT INTO `user_role_linker` (`user_id`, `role_id`) VALUES
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

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
(50, 'Cuá»‘n', 'cuon', 0),
(51, 'KhÃ´ng hoáº¡t Ä‘á»™ng', 'khong-hoat-dong', 0);

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
(0, 51, 'doi-tac', 'Äá»‘i tÃ¡c thuá»™c loáº¡i nÃ y lÃ  Ä‘á»‘i tÃ¡c Ä‘Ã£ bá»‹ xÃ³a', 44, NULL),
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
-- Constraints for table `doi_tac`
--
ALTER TABLE `doi_tac`
  ADD CONSTRAINT `fk_doitac_zf_term_taxonomy` FOREIGN KEY (`loai_doi_tac`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`);

--
-- Constraints for table `gia_xuat`
--
ALTER TABLE `gia_xuat`
  ADD CONSTRAINT `fk_giaxuat_sanpham` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`),
  ADD CONSTRAINT `fk_gia_xuat_term_taxonomy` FOREIGN KEY (`id_kenh_phan_phoi`) REFERENCES `zf_term_taxonomy` (`term_taxonomy_id`);

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