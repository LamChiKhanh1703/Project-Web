-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 22, 2025 lúc 10:28 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webdochoi`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id_danhmuc` int(11) NOT NULL,
  `ten_danhmuc` varchar(50) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `trangthai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id_danhmuc`, `ten_danhmuc`, `mo_ta`, `trangthai`) VALUES
(1, 'Đồ chơi cho nam', 'Đồ chơi dành cho con đực', 0),
(2, 'Đồ chơi cho nữ', 'Đồ chơi dành cho con cái', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dong_sanpham`
--

CREATE TABLE `dong_sanpham` (
  `id_dong` int(11) NOT NULL,
  `ten_dong` varchar(50) NOT NULL,
  `id_nhanhieu` int(11) NOT NULL,
  `id_danhmuc` int(11) DEFAULT NULL,
  `trangthai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dong_sanpham`
--

INSERT INTO `dong_sanpham` (`id_dong`, `ten_dong`, `id_nhanhieu`, `id_danhmuc`, `trangthai`) VALUES
(1, 'Lắp ráp', 1, NULL, 0),
(2, 'Lego', 2, NULL, 0),
(3, 'Xe cộ', 2, NULL, 0),
(4, 'Con vật', 1, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang_chitiet`
--

CREATE TABLE `donhang_chitiet` (
  `id_chitiet_donhang` int(11) NOT NULL,
  `id_donhang` int(11) NOT NULL,
  `id_spchitiet` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `gia_ban` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `donhang_chitiet`
--

INSERT INTO `donhang_chitiet` (`id_chitiet_donhang`, `id_donhang`, `id_spchitiet`, `soluong`, `gia_ban`) VALUES
(1, 1, 1, 2, 40000),
(2, 2, 1, 10, 40000),
(3, 2, 2, 8, 250000),
(4, 3, 3, 1, 55000),
(5, 3, 2, 1, 250000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id_donhang` int(11) NOT NULL,
  `ten_nguoi_nhan` varchar(50) NOT NULL,
  `sodienthoai` varchar(15) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `diachi` text NOT NULL,
  `ghichu` text DEFAULT NULL,
  `id_khach` int(11) NOT NULL,
  `id_thanhtoan` int(11) NOT NULL,
  `id_vanchuyen` int(11) NOT NULL,
  `ngay_tao` datetime NOT NULL DEFAULT current_timestamp(),
  `tong_tien` int(11) NOT NULL,
  `phi_ship` int(11) NOT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: mới tạo, 1: đang xử lý, 2: đang vận chuyển, 3: đã hoàn thành, 4: đã hủy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id_donhang`, `ten_nguoi_nhan`, `sodienthoai`, `email`, `diachi`, `ghichu`, `id_khach`, `id_thanhtoan`, `id_vanchuyen`, `ngay_tao`, `tong_tien`, `phi_ship`, `trang_thai`) VALUES
(1, 'Lâm Khánh', '0906054826', 'lamkhanh@gmail.com', 'Hà Nội', 'đừng đọc', 1, 1, 1, '2025-05-23 02:19:56', 90000, 10000, 2),
(2, 'Lâm Khánh', '0906054826', 'lamkhanh@gmail.com', 'hà nội', 'chịu', 1, 1, 1, '2025-05-23 02:24:14', 2410000, 10000, 3),
(3, 'Lâm Khánh', '0906054826', 'lamkhanh@gmail.com', 'Hải Phòng', '', 1, 1, 3, '2025-05-23 03:27:12', 405000, 100000, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang_chitiet`
--

CREATE TABLE `giohang_chitiet` (
  `id_giochitiet` int(11) NOT NULL,
  `id_gio` int(11) NOT NULL,
  `id_spchitiet` int(11) NOT NULL,
  `soluong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

CREATE TABLE `gio_hang` (
  `id_gio` int(11) NOT NULL,
  `id_khach` int(11) NOT NULL,
  `ngaycapnhat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinh_anh`
--

CREATE TABLE `hinh_anh` (
  `id_hinh` int(11) NOT NULL,
  `id_sp` int(11) NOT NULL,
  `ten_file` varchar(255) NOT NULL,
  `trang_thai` tinyint(1) NOT NULL COMMENT '0: ảnh đại diện, 1: các ảnh con'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `hinh_anh`
--

INSERT INTO `hinh_anh` (`id_hinh`, `id_sp`, `ten_file`, `trang_thai`) VALUES
(9, 4, 'dochoi1.jpg', 0),
(10, 4, 'dochoi1mini1.jpg', 1),
(11, 4, 'dochoi1mini2.jpg', 1),
(12, 4, 'dochoi1mini3.jpg', 1),
(13, 4, 'dochoi1mini4.jpg', 1),
(14, 5, 'xedochoi1.jpg', 0),
(15, 5, 'xedochoi1mini1.jpg', 1),
(16, 5, 'xedochoi1mini2.jpg', 1),
(17, 6, 'lego1.jpg', 0),
(18, 6, 'lego1.jpg', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khach_hang`
--

CREATE TABLE `khach_hang` (
  `id_khach` int(11) NOT NULL,
  `hoten` varchar(50) NOT NULL,
  `sodienthoai` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `matkhau` varchar(255) NOT NULL,
  `diachi` text DEFAULT NULL,
  `ngay_dangky` datetime NOT NULL DEFAULT current_timestamp(),
  `trangthai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: active, 1: deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khach_hang`
--

INSERT INTO `khach_hang` (`id_khach`, `hoten`, `sodienthoai`, `email`, `matkhau`, `diachi`, `ngay_dangky`, `trangthai`) VALUES
(1, 'Lâm Khánh', '0906054826', 'lamkhanh@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', NULL, '2025-05-23 02:01:00', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyen_mai`
--

CREATE TABLE `khuyen_mai` (
  `id_khuyenmai` int(11) NOT NULL,
  `ten_khuyenmai` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `ma_giamgia` varchar(20) DEFAULT NULL,
  `giatri_giam` int(11) NOT NULL,
  `loai_giam` tinyint(4) NOT NULL COMMENT '0: giảm theo %, 1: giảm theo số tiền',
  `ngay_batdau` datetime NOT NULL,
  `ngay_ketthuc` datetime NOT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lien_he`
--

CREATE TABLE `lien_he` (
  `id_lienhe` int(11) NOT NULL,
  `hotline` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `diachi` text NOT NULL,
  `link_facebook` varchar(100) DEFAULT NULL,
  `link_youtube` varchar(100) DEFAULT NULL,
  `link_tiktok` varchar(100) DEFAULT NULL,
  `link_instagram` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhan_hieu`
--

CREATE TABLE `nhan_hieu` (
  `id_nhan` int(11) NOT NULL,
  `ten_nhanhieu` varchar(50) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `trangthai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhan_hieu`
--

INSERT INTO `nhan_hieu` (`id_nhan`, `ten_nhanhieu`, `logo`, `trangthai`) VALUES
(1, 'Đồ chơi trẻ em', 'dochoitreem.jpg', 0),
(2, 'Đồ chơi thanh niên', 'dochoithanhnien.jpg', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quan_tri`
--

CREATE TABLE `quan_tri` (
  `id_admin` int(11) NOT NULL,
  `hoten` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `matkhau` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `vaitro` tinyint(4) NOT NULL COMMENT '0: admin, 1: nhân viên sale, 2: chăm sóc khách hàng, 3: nhân viên kho, 4: kế toán, 5: quản lý',
  `trangthai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: active, 1: deactive',
  `ngay_tao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quan_tri`
--

INSERT INTO `quan_tri` (`id_admin`, `hoten`, `email`, `matkhau`, `avatar`, `vaitro`, `trangthai`, `ngay_tao`) VALUES
(1, 'Lam Chi Khanh', 'lamkhanh@gmail,com', '123456', NULL, 0, 0, '2025-05-12 21:17:51'),
(2, 'Lâm Khanh', 'khanh@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', NULL, 0, 0, '2025-05-22 21:10:13'),
(4, 'Lukaku', 'anhKu@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', NULL, 2, 0, '2025-05-23 03:14:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham_chitiet`
--

CREATE TABLE `sanpham_chitiet` (
  `id_spchitiet` int(11) NOT NULL,
  `id_sp` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `gia_nhap` int(11) NOT NULL,
  `gia_goc` int(11) NOT NULL,
  `gia_sale` int(11) DEFAULT NULL,
  `id_khuyenmai` int(11) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham_chitiet`
--

INSERT INTO `sanpham_chitiet` (`id_spchitiet`, `id_sp`, `sku`, `so_luong`, `gia_nhap`, `gia_goc`, `gia_sale`, `id_khuyenmai`, `trang_thai`) VALUES
(1, 4, '1', 990, 20000, 50000, 40000, NULL, 0),
(2, 5, '2', 91, 20000, 500000, 250000, NULL, 0),
(3, 6, '3', 999, 20000, 100000, 55000, NULL, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `id_sp` int(11) NOT NULL,
  `ten_sp` varchar(100) NOT NULL,
  `mo_ta_ngan` text DEFAULT NULL,
  `mo_ta_chi_tiet` longtext DEFAULT NULL,
  `id_nhanhieu` int(11) NOT NULL,
  `id_dong` int(11) NOT NULL,
  `id_danhmuc` int(11) DEFAULT NULL,
  `luot_xem` int(11) NOT NULL DEFAULT 0,
  `noi_bat` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: không, 1: có',
  `ngay_tao` datetime NOT NULL DEFAULT current_timestamp(),
  `ngaycapnhat` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trangthai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id_sp`, `ten_sp`, `mo_ta_ngan`, `mo_ta_chi_tiet`, `id_nhanhieu`, `id_dong`, `id_danhmuc`, `luot_xem`, `noi_bat`, `ngay_tao`, `ngaycapnhat`, `trangthai`) VALUES
(4, 'Lắp ráp đồ chơi ', 'lắp đê', 'Lắp đê hay lắm', 1, 1, 1, 0, 1, '2025-05-23 02:00:23', '2025-05-23 02:00:23', 0),
(5, 'Xe điều khiển từ xa', 'rừm rừm', 'xe đi rừm rừm', 2, 3, 1, 0, 1, '2025-05-23 02:23:12', '2025-05-23 02:23:12', 0),
(6, 'LEgo nhà nè', 'LEgo thôi', 'Lego nhà thôi', 2, 2, 1, 0, 1, '2025-05-23 03:16:32', '2025-05-23 03:16:32', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id_thanhtoan` int(11) NOT NULL,
  `ten_hinh_thuc` varchar(50) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `thong_tin_them` text DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thanh_toan`
--

INSERT INTO `thanh_toan` (`id_thanhtoan`, `ten_hinh_thuc`, `mo_ta`, `thong_tin_them`, `trang_thai`) VALUES
(1, 'Thanh toán khi nhận hàng', 'Hàng đến, đưa tiền cho shipper', 'Đừng đọc, ko có gì đâu', 0),
(2, 'Chuyển khoản', 'Chuyển tiền vào đây cu', 'STK: 123456789, Người nhận: Host', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tin_tuc`
--

CREATE TABLE `tin_tuc` (
  `id_tintuc` int(11) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `mo_ta_ngan` text DEFAULT NULL,
  `noi_dung` longtext DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `ngay_dang` datetime NOT NULL DEFAULT current_timestamp(),
  `nguoi_dang` int(11) NOT NULL,
  `luot_xem` int(11) NOT NULL DEFAULT 0,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `van_chuyen`
--

CREATE TABLE `van_chuyen` (
  `id_vanchuyen` int(11) NOT NULL,
  `ten_vanchuyen` varchar(50) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `phi_ship` int(11) NOT NULL,
  `thoi_gian` varchar(50) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: hiện, 1: ẩn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `van_chuyen`
--

INSERT INTO `van_chuyen` (`id_vanchuyen`, `ten_vanchuyen`, `mo_ta`, `phi_ship`, `thoi_gian`, `trang_thai`) VALUES
(1, 'Xe máy', 'Đi trong thành phố thôi cu', 10000, '1-2 ngày', 0),
(2, 'Ô tô', 'Đi trong miền thôi cu', 50000, '3-5 ngày', 0),
(3, 'Máy bay', 'Trong nước và nước ngoài cu', 100000, '7-10 ngày', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id_danhmuc`);

--
-- Chỉ mục cho bảng `dong_sanpham`
--
ALTER TABLE `dong_sanpham`
  ADD PRIMARY KEY (`id_dong`),
  ADD KEY `id_nhanhieu` (`id_nhanhieu`),
  ADD KEY `id_danhmuc` (`id_danhmuc`);

--
-- Chỉ mục cho bảng `donhang_chitiet`
--
ALTER TABLE `donhang_chitiet`
  ADD PRIMARY KEY (`id_chitiet_donhang`),
  ADD KEY `FK_donhang` (`id_donhang`),
  ADD KEY `FK_sanpham_chitiet` (`id_spchitiet`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_donhang`),
  ADD KEY `FK_khachhang` (`id_khach`),
  ADD KEY `FK_thanhtoan` (`id_thanhtoan`),
  ADD KEY `FK_vanchuyen` (`id_vanchuyen`);

--
-- Chỉ mục cho bảng `giohang_chitiet`
--
ALTER TABLE `giohang_chitiet`
  ADD PRIMARY KEY (`id_giochitiet`),
  ADD KEY `FK_gio` (`id_gio`),
  ADD KEY `FK_sp_chitiet` (`id_spchitiet`);

--
-- Chỉ mục cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`id_gio`),
  ADD KEY `FK_user` (`id_khach`);

--
-- Chỉ mục cho bảng `hinh_anh`
--
ALTER TABLE `hinh_anh`
  ADD PRIMARY KEY (`id_hinh`),
  ADD KEY `id_sp` (`id_sp`);

--
-- Chỉ mục cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`id_khach`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id_khuyenmai`),
  ADD UNIQUE KEY `ma_giamgia` (`ma_giamgia`);

--
-- Chỉ mục cho bảng `lien_he`
--
ALTER TABLE `lien_he`
  ADD PRIMARY KEY (`id_lienhe`);

--
-- Chỉ mục cho bảng `nhan_hieu`
--
ALTER TABLE `nhan_hieu`
  ADD PRIMARY KEY (`id_nhan`);

--
-- Chỉ mục cho bảng `quan_tri`
--
ALTER TABLE `quan_tri`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `sanpham_chitiet`
--
ALTER TABLE `sanpham_chitiet`
  ADD PRIMARY KEY (`id_spchitiet`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `FK_sanpham` (`id_sp`),
  ADD KEY `id_khuyenmai` (`id_khuyenmai`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id_sp`),
  ADD KEY `FK_nhanhieu` (`id_nhanhieu`),
  ADD KEY `FK_dong` (`id_dong`),
  ADD KEY `id_danhmuc` (`id_danhmuc`);

--
-- Chỉ mục cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id_thanhtoan`);

--
-- Chỉ mục cho bảng `tin_tuc`
--
ALTER TABLE `tin_tuc`
  ADD PRIMARY KEY (`id_tintuc`),
  ADD KEY `nguoi_dang` (`nguoi_dang`);

--
-- Chỉ mục cho bảng `van_chuyen`
--
ALTER TABLE `van_chuyen`
  ADD PRIMARY KEY (`id_vanchuyen`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id_danhmuc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `dong_sanpham`
--
ALTER TABLE `dong_sanpham`
  MODIFY `id_dong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `donhang_chitiet`
--
ALTER TABLE `donhang_chitiet`
  MODIFY `id_chitiet_donhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id_donhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `giohang_chitiet`
--
ALTER TABLE `giohang_chitiet`
  MODIFY `id_giochitiet` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `id_gio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hinh_anh`
--
ALTER TABLE `hinh_anh`
  MODIFY `id_hinh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  MODIFY `id_khach` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  MODIFY `id_khuyenmai` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lien_he`
--
ALTER TABLE `lien_he`
  MODIFY `id_lienhe` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nhan_hieu`
--
ALTER TABLE `nhan_hieu`
  MODIFY `id_nhan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `quan_tri`
--
ALTER TABLE `quan_tri`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `sanpham_chitiet`
--
ALTER TABLE `sanpham_chitiet`
  MODIFY `id_spchitiet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id_sp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id_thanhtoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `tin_tuc`
--
ALTER TABLE `tin_tuc`
  MODIFY `id_tintuc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `van_chuyen`
--
ALTER TABLE `van_chuyen`
  MODIFY `id_vanchuyen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `dong_sanpham`
--
ALTER TABLE `dong_sanpham`
  ADD CONSTRAINT `dong_sanpham_ibfk_1` FOREIGN KEY (`id_nhanhieu`) REFERENCES `nhan_hieu` (`id_nhan`),
  ADD CONSTRAINT `dong_sanpham_ibfk_2` FOREIGN KEY (`id_danhmuc`) REFERENCES `danh_muc` (`id_danhmuc`);

--
-- Các ràng buộc cho bảng `donhang_chitiet`
--
ALTER TABLE `donhang_chitiet`
  ADD CONSTRAINT `FK_donhang` FOREIGN KEY (`id_donhang`) REFERENCES `don_hang` (`id_donhang`),
  ADD CONSTRAINT `FK_sanpham_chitiet` FOREIGN KEY (`id_spchitiet`) REFERENCES `sanpham_chitiet` (`id_spchitiet`);

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `FK_khachhang` FOREIGN KEY (`id_khach`) REFERENCES `khach_hang` (`id_khach`),
  ADD CONSTRAINT `FK_thanhtoan` FOREIGN KEY (`id_thanhtoan`) REFERENCES `thanh_toan` (`id_thanhtoan`),
  ADD CONSTRAINT `FK_vanchuyen` FOREIGN KEY (`id_vanchuyen`) REFERENCES `van_chuyen` (`id_vanchuyen`);

--
-- Các ràng buộc cho bảng `giohang_chitiet`
--
ALTER TABLE `giohang_chitiet`
  ADD CONSTRAINT `FK_gio` FOREIGN KEY (`id_gio`) REFERENCES `gio_hang` (`id_gio`),
  ADD CONSTRAINT `FK_sp_chitiet` FOREIGN KEY (`id_spchitiet`) REFERENCES `sanpham_chitiet` (`id_spchitiet`);

--
-- Các ràng buộc cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `FK_user` FOREIGN KEY (`id_khach`) REFERENCES `khach_hang` (`id_khach`);

--
-- Các ràng buộc cho bảng `hinh_anh`
--
ALTER TABLE `hinh_anh`
  ADD CONSTRAINT `hinh_anh_ibfk_1` FOREIGN KEY (`id_sp`) REFERENCES `san_pham` (`id_sp`);

--
-- Các ràng buộc cho bảng `sanpham_chitiet`
--
ALTER TABLE `sanpham_chitiet`
  ADD CONSTRAINT `FK_sanpham` FOREIGN KEY (`id_sp`) REFERENCES `san_pham` (`id_sp`),
  ADD CONSTRAINT `sanpham_chitiet_ibfk_1` FOREIGN KEY (`id_khuyenmai`) REFERENCES `khuyen_mai` (`id_khuyenmai`);

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `FK_dong` FOREIGN KEY (`id_dong`) REFERENCES `dong_sanpham` (`id_dong`),
  ADD CONSTRAINT `FK_nhanhieu` FOREIGN KEY (`id_nhanhieu`) REFERENCES `nhan_hieu` (`id_nhan`),
  ADD CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`id_danhmuc`) REFERENCES `danh_muc` (`id_danhmuc`);

--
-- Các ràng buộc cho bảng `tin_tuc`
--
ALTER TABLE `tin_tuc`
  ADD CONSTRAINT `tin_tuc_ibfk_1` FOREIGN KEY (`nguoi_dang`) REFERENCES `quan_tri` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
