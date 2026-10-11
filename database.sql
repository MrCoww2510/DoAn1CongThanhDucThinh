-- ============================================================
-- TTComputer - MySQL / phpMyAdmin
-- Chuyển từ cú pháp SQL Server sang MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS TTComputer
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE TTComputer;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS GiaoDichThanhToan;
DROP TABLE IF EXISTS LichSuKho;
DROP TABLE IF EXISTS ChiTietDonHang;
DROP TABLE IF EXISTS DonHang;
DROP TABLE IF EXISTS ChiTietGioHang;
DROP TABLE IF EXISTS GioHang;
DROP TABLE IF EXISTS ChiTietSanPham;
DROP TABLE IF EXISTS SanPham;
DROP TABLE IF EXISTS DanhMuc;
DROP TABLE IF EXISTS TaiKhoan;
DROP TABLE IF EXISTS NguoiDung;

SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================
-- 1. NGƯỜI DÙNG
-- ============================================================

CREATE TABLE NguoiDung (
    MaND VARCHAR(20) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    SDT VARCHAR(15),
    Email VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 2. TÀI KHOẢN
-- ============================================================

CREATE TABLE TaiKhoan (
    MaTK VARCHAR(20) PRIMARY KEY,
    MaND VARCHAR(20) NOT NULL UNIQUE,
    TenDangNhap VARCHAR(50) NOT NULL UNIQUE,
    MatKhau VARCHAR(255) NOT NULL,
    VaiTro VARCHAR(20) NOT NULL,
    TrangThai VARCHAR(30) NOT NULL DEFAULT 'HoatDong',

    CONSTRAINT FK_TaiKhoan_NguoiDung
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT CK_TaiKhoan_VaiTro
        CHECK (
            VaiTro IN (
                'KhachHang',
                'NhanVien',
                'QuanTriVien'
            )
        ),

    CONSTRAINT CK_TaiKhoan_TrangThai
        CHECK (
            TrangThai IN (
                'HoatDong',
                'VoHieuHoa',
                'KhongConHoatDong'
            )
        )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 3. DANH MỤC
-- ============================================================

CREATE TABLE DanhMuc (
    MaDM VARCHAR(20) PRIMARY KEY,
    TenDM VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 4. SẢN PHẨM
-- ============================================================

CREATE TABLE SanPham (
    MaSP VARCHAR(20) PRIMARY KEY,
    MaDM VARCHAR(20) NOT NULL,
    TenSP VARCHAR(200) NOT NULL,
    GiaBan DECIMAL(18,2) NOT NULL,
    SoLuongTon INT NOT NULL DEFAULT 0,

    CONSTRAINT FK_SanPham_DanhMuc
        FOREIGN KEY (MaDM)
        REFERENCES DanhMuc(MaDM),

    CONSTRAINT CK_SanPham_GiaBan
        CHECK (GiaBan >= 0),

    CONSTRAINT CK_SanPham_SoLuongTon
        CHECK (SoLuongTon >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 5. CHI TIẾT SẢN PHẨM
-- ============================================================

CREATE TABLE ChiTietSanPham (
    MaCTSP VARCHAR(20) PRIMARY KEY,
    MaSP VARCHAR(20) NOT NULL UNIQUE,
    LoaiSP VARCHAR(100),
    ThongSoKT TEXT,
    ThuongHieu VARCHAR(100),
    HinhAnh VARCHAR(500),
    MoTa TEXT,
    TrangThai VARCHAR(50),

    CONSTRAINT FK_ChiTietSanPham_SanPham
        FOREIGN KEY (MaSP)
        REFERENCES SanPham(MaSP)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 6. GIỎ HÀNG
-- ============================================================

CREATE TABLE GioHang (
    MaGH VARCHAR(20) PRIMARY KEY,
    MaND VARCHAR(20) NOT NULL UNIQUE,
    TongTien DECIMAL(18,2) NOT NULL DEFAULT 0,

    CONSTRAINT FK_GioHang_NguoiDung
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT CK_GioHang_TongTien
        CHECK (TongTien >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 7. CHI TIẾT GIỎ HÀNG
-- ============================================================

CREATE TABLE ChiTietGioHang (
    MaCTGH VARCHAR(20) PRIMARY KEY,
    MaGH VARCHAR(20) NOT NULL,
    MaSP VARCHAR(20) NOT NULL,
    SoLuong INT NOT NULL,

    CONSTRAINT FK_ChiTietGioHang_GioHang
        FOREIGN KEY (MaGH)
        REFERENCES GioHang(MaGH)
        ON DELETE CASCADE,

    CONSTRAINT FK_ChiTietGioHang_SanPham
        FOREIGN KEY (MaSP)
        REFERENCES SanPham(MaSP),

    CONSTRAINT CK_ChiTietGioHang_SoLuong
        CHECK (SoLuong > 0),

    CONSTRAINT UQ_GioHang_SanPham
        UNIQUE (MaGH, MaSP)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 8. ĐƠN HÀNG
-- ============================================================

CREATE TABLE DonHang (
    MaDH VARCHAR(20) PRIMARY KEY,
    MaND VARCHAR(20) NOT NULL,
    MaNV VARCHAR(20) NULL,
    TenNguoiNhan VARCHAR(100) NOT NULL,
    DiaChiGiao VARCHAR(255) NOT NULL,
    SDTNhan VARCHAR(15) NOT NULL,
    TongTien DECIMAL(18,2) NOT NULL,
    PhuongThucTT VARCHAR(20) NOT NULL,
    TrangThaiDH VARCHAR(50) NOT NULL,
    TrangThaiTT VARCHAR(50) NOT NULL,
    NgayTao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT FK_DonHang_KhachHang
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT FK_DonHang_NhanVien
        FOREIGN KEY (MaNV)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT CK_DonHang_TongTien
        CHECK (TongTien >= 0),

    CONSTRAINT CK_DonHang_PhuongThucTT
        CHECK (
            PhuongThucTT IN (
                'COD',
                'Online'
            )
        ),

    CONSTRAINT CK_DonHang_TrangThaiDH
        CHECK (
            TrangThaiDH IN (
                'Chờ xác nhận',
                'Đang chuẩn bị hàng',
                'Đang giao hàng',
                'Hoàn thành',
                'Đã hủy'
            )
        ),

    CONSTRAINT CK_DonHang_TrangThaiTT
        CHECK (
            TrangThaiTT IN (
                'Chưa thanh toán',
                'Đã thanh toán'
            )
        )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 9. CHI TIẾT ĐƠN HÀNG
-- ============================================================

CREATE TABLE ChiTietDonHang (
    MaCTDH VARCHAR(20) PRIMARY KEY,
    MaDH VARCHAR(20) NOT NULL,
    MaSP VARCHAR(20) NOT NULL,
    SoLuong INT NOT NULL,
    DonGia DECIMAL(18,2) NOT NULL,

    CONSTRAINT FK_ChiTietDonHang_DonHang
        FOREIGN KEY (MaDH)
        REFERENCES DonHang(MaDH)
        ON DELETE CASCADE,

    CONSTRAINT FK_ChiTietDonHang_SanPham
        FOREIGN KEY (MaSP)
        REFERENCES SanPham(MaSP),

    CONSTRAINT CK_ChiTietDonHang_SoLuong
        CHECK (SoLuong > 0),

    CONSTRAINT CK_ChiTietDonHang_DonGia
        CHECK (DonGia >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 10. GIAO DỊCH THANH TOÁN
-- ============================================================

CREATE TABLE GiaoDichThanhToan (
    MaGD VARCHAR(50) PRIMARY KEY,
    MaDH VARCHAR(20) NOT NULL,
    SoTien DECIMAL(18,2) NOT NULL,
    TrangThai VARCHAR(30) NOT NULL,
    ThoiGianGiaoDich DATETIME NULL,

    CONSTRAINT FK_GiaoDichThanhToan_DonHang
        FOREIGN KEY (MaDH)
        REFERENCES DonHang(MaDH),

    CONSTRAINT CK_GiaoDichThanhToan_SoTien
        CHECK (SoTien >= 0),

    CONSTRAINT CK_GiaoDichThanhToan_TrangThai
        CHECK (
            TrangThai IN (
                'ThanhCong',
                'ThatBai',
                'DangXuLy'
            )
        )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- 11. LỊCH SỬ KHO
-- ============================================================

CREATE TABLE LichSuKho (
    MaLS VARCHAR(20) PRIMARY KEY,
    MaSP VARCHAR(20) NOT NULL,
    SoLuongThayDoi INT NOT NULL,
    LoaiThaoTac VARCHAR(30) NOT NULL,
    MaND VARCHAR(20) NOT NULL,
    ThoiGian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT FK_LichSuKho_SanPham
        FOREIGN KEY (MaSP)
        REFERENCES SanPham(MaSP),

    CONSTRAINT FK_LichSuKho_NguoiDung
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT CK_LichSuKho_LoaiThaoTac
        CHECK (
            LoaiThaoTac IN (
                'NhapKho',
                'DieuChinh',
                'XuatKho'
            )
        )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;