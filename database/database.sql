CREATE DATABASE TTComputer;
GO

USE TTComputer;
GO


-- =========================================
-- 1. NGƯỜI DÙNG
-- =========================================

CREATE TABLE NguoiDung (
    MaND VARCHAR(20) PRIMARY KEY,
    HoTen NVARCHAR(100) NOT NULL,
    SDT VARCHAR(15),
    Email VARCHAR(100)
);
GO


-- =========================================
-- 2. TÀI KHOẢN
-- =========================================

CREATE TABLE TaiKhoan (
    MaTK VARCHAR(20) PRIMARY KEY,
    MaND VARCHAR(20) NOT NULL UNIQUE,
    TenDangNhap VARCHAR(50) NOT NULL UNIQUE,
    MatKhau VARCHAR(255) NOT NULL,
    VaiTro VARCHAR(20) NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'HoatDong',

    CONSTRAINT FK_TaiKhoan_NguoiDung
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT CK_TaiKhoan_VaiTro
        CHECK (VaiTro IN ('KhachHang', 'NhanVien', 'QuanTriVien')),

    CONSTRAINT CK_TaiKhoan_TrangThai
        CHECK (TrangThai IN ('HoatDong', 'Khoa'))
);
GO


-- =========================================
-- 3. DANH MỤC
-- =========================================

CREATE TABLE DanhMuc (
    MaDM VARCHAR(20) PRIMARY KEY,
    TenDM NVARCHAR(100) NOT NULL UNIQUE
);
GO


-- =========================================
-- 4. SẢN PHẨM
-- =========================================

CREATE TABLE SanPham (
    MaSP VARCHAR(20) PRIMARY KEY,
    MaDM VARCHAR(20) NOT NULL,
    TenSP NVARCHAR(200) NOT NULL,
    GiaBan DECIMAL(18,2) NOT NULL,
    SoLuongTon INT NOT NULL DEFAULT 0,

    CONSTRAINT FK_SanPham_DanhMuc
        FOREIGN KEY (MaDM)
        REFERENCES DanhMuc(MaDM),

    CONSTRAINT CK_SanPham_GiaBan
        CHECK (GiaBan >= 0),

    CONSTRAINT CK_SanPham_SoLuongTon
        CHECK (SoLuongTon >= 0)
);
GO


-- =========================================
-- 5. CHI TIẾT SẢN PHẨM
-- =========================================

CREATE TABLE ChiTietSanPham (
    MaCTSP VARCHAR(20) PRIMARY KEY,
    MaSP VARCHAR(20) NOT NULL UNIQUE,
    LoaiSP NVARCHAR(100),
    ThongSoKT NVARCHAR(MAX),
    ThuongHieu NVARCHAR(100),
    TrangThai NVARCHAR(50),

    CONSTRAINT FK_ChiTietSanPham_SanPham
        FOREIGN KEY (MaSP)
        REFERENCES SanPham(MaSP)
        ON DELETE CASCADE
);
GO


-- =========================================
-- 6. GIỎ HÀNG
-- =========================================

CREATE TABLE GioHang (
    MaGH VARCHAR(20) PRIMARY KEY,
    MaND VARCHAR(20) NOT NULL UNIQUE,
    TongTien DECIMAL(18,2) NOT NULL DEFAULT 0,

    CONSTRAINT FK_GioHang_NguoiDung
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT CK_GioHang_TongTien
        CHECK (TongTien >= 0)
);
GO


-- =========================================
-- 7. CHI TIẾT GIỎ HÀNG
-- =========================================

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
);
GO


-- =========================================
-- 8. ĐƠN HÀNG
-- =========================================

CREATE TABLE DonHang (
    MaDH VARCHAR(20) PRIMARY KEY,
    MaND VARCHAR(20) NOT NULL,
    MaNV VARCHAR(20) NULL,

    TenNguoiNhan NVARCHAR(100) NOT NULL,
    DiaChiGiao NVARCHAR(255) NOT NULL,
    SDTNhan VARCHAR(15) NOT NULL,

    TongTien DECIMAL(18,2) NOT NULL,
    PhuongThucTT VARCHAR(20) NOT NULL,
    TrangThaiDH NVARCHAR(50) NOT NULL,
    TrangThaiTT NVARCHAR(50) NOT NULL,
    NgayTao DATETIME NOT NULL DEFAULT GETDATE(),

    CONSTRAINT FK_DonHang_KhachHang
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT FK_DonHang_NhanVien
        FOREIGN KEY (MaNV)
        REFERENCES NguoiDung(MaND),

    CONSTRAINT CK_DonHang_TongTien
        CHECK (TongTien >= 0),

    CONSTRAINT CK_DonHang_PhuongThucTT
        CHECK (PhuongThucTT IN ('COD', 'Online')),

    CONSTRAINT CK_DonHang_TrangThaiDH
        CHECK (
            TrangThaiDH IN (
                N'Chờ xác nhận',
                N'Đang chuẩn bị hàng',
                N'Đang giao hàng',
                N'Hoàn thành',
                N'Đã hủy'
            )
        ),

    CONSTRAINT CK_DonHang_TrangThaiTT
        CHECK (
            TrangThaiTT IN (
                N'Chưa thanh toán',
                N'Đã thanh toán'
            )
        )
);
GO


-- =========================================
-- 9. CHI TIẾT ĐƠN HÀNG
-- =========================================

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
);
GO


-- =========================================
-- 10. GIAO DỊCH THANH TOÁN
-- =========================================

CREATE TABLE GiaoDichThanhToan (
    MaGD VARCHAR(50) PRIMARY KEY,
    MaDH VARCHAR(20) NOT NULL UNIQUE,
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
);
GO