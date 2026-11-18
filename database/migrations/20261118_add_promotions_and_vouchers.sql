-- T&T Computer: UC16-UC18 promotions and vouchers.
-- Review the target database and back it up before applying. This migration is additive.
-- Do not run NOTE/database.sql to upgrade an existing database: that script drops tables.

ALTER TABLE DonHang
    ADD COLUMN MaVoucherCode VARCHAR(50) NULL AFTER TrangThaiTT,
    ADD COLUMN GiamGiaVoucher DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER MaVoucherCode,
    ADD CONSTRAINT CK_DonHang_GiamGiaVoucher CHECK (GiamGiaVoucher >= 0);

ALTER TABLE GiaoDichThanhToan
    ADD COLUMN MaGatewayTxn VARCHAR(100) NULL AFTER MaDH,
    ADD UNIQUE KEY UQ_GiaoDichThanhToan_GatewayTxn (MaGatewayTxn);

CREATE TABLE ChuongTrinhGiamGia (
    MaCTGG VARCHAR(20) PRIMARY KEY,
    TenCTGG VARCHAR(100) NOT NULL,
    PhamVi VARCHAR(20) NOT NULL,
    LoaiGiam VARCHAR(20) NOT NULL,
    GiaTri DECIMAL(18,2) NOT NULL,
    NgayBatDau DATETIME NOT NULL,
    NgayKetThuc DATETIME NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'HoatDong',
    CONSTRAINT CK_ChuongTrinhGiamGia_PhamVi CHECK (PhamVi IN ('ToanTrang', 'SanPham')),
    CONSTRAINT CK_ChuongTrinhGiamGia_Loai CHECK (LoaiGiam IN ('PhanTram', 'SoTien')),
    CONSTRAINT CK_ChuongTrinhGiamGia_GiaTri CHECK (GiaTri > 0),
    CONSTRAINT CK_ChuongTrinhGiamGia_PhanTram CHECK (LoaiGiam <> 'PhanTram' OR GiaTri <= 100),
    CONSTRAINT CK_ChuongTrinhGiamGia_ThoiGian CHECK (NgayKetThuc > NgayBatDau),
    CONSTRAINT CK_ChuongTrinhGiamGia_TrangThai CHECK (TrangThai IN ('HoatDong', 'TamDung', 'KetThuc'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ChuongTrinhGiamGiaSanPham (
    MaCTGG VARCHAR(20) NOT NULL,
    MaSP VARCHAR(20) NOT NULL,
    PRIMARY KEY (MaCTGG, MaSP),
    CONSTRAINT FK_CTGGSanPham_ChuongTrinh FOREIGN KEY (MaCTGG)
        REFERENCES ChuongTrinhGiamGia(MaCTGG) ON DELETE CASCADE,
    CONSTRAINT FK_CTGGSanPham_SanPham FOREIGN KEY (MaSP)
        REFERENCES SanPham(MaSP)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Voucher (
    MaVoucher VARCHAR(20) PRIMARY KEY,
    MaCode VARCHAR(50) NOT NULL UNIQUE,
    LoaiGiam VARCHAR(20) NOT NULL,
    GiaTri DECIMAL(18,2) NOT NULL,
    NgayBatDau DATETIME NOT NULL,
    NgayKetThuc DATETIME NOT NULL,
    DonToiThieu DECIMAL(18,2) NOT NULL DEFAULT 0,
    GiamToiDa DECIMAL(18,2) NULL,
    TongLuotToiDa INT UNSIGNED NULL,
    LuotMoiKhachToiDa INT UNSIGNED NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'HoatDong',
    CONSTRAINT CK_Voucher_Loai CHECK (LoaiGiam IN ('PhanTram', 'SoTien')),
    CONSTRAINT CK_Voucher_GiaTri CHECK (GiaTri > 0),
    CONSTRAINT CK_Voucher_PhanTram CHECK (LoaiGiam <> 'PhanTram' OR GiaTri <= 100),
    CONSTRAINT CK_Voucher_GiamToiDa CHECK (GiamToiDa IS NULL OR GiamToiDa >= 0),
    CONSTRAINT CK_Voucher_DonToiThieu CHECK (DonToiThieu >= 0),
    CONSTRAINT CK_Voucher_ThoiGian CHECK (NgayKetThuc > NgayBatDau),
    CONSTRAINT CK_Voucher_TrangThai CHECK (TrangThai IN ('HoatDong', 'VoHieuHoa'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE VoucherSuDung (
    MaSuDung VARCHAR(40) PRIMARY KEY,
    MaVoucher VARCHAR(20) NOT NULL,
    MaDH VARCHAR(20) NOT NULL UNIQUE,
    MaND VARCHAR(20) NOT NULL,
    SoTienGiam DECIMAL(18,2) NOT NULL,
    ThoiGian DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY UQ_VoucherSuDung_Voucher_Don (MaVoucher, MaDH),
    CONSTRAINT FK_VoucherSuDung_Voucher FOREIGN KEY (MaVoucher)
        REFERENCES Voucher(MaVoucher),
    CONSTRAINT FK_VoucherSuDung_DonHang FOREIGN KEY (MaDH)
        REFERENCES DonHang(MaDH),
    CONSTRAINT FK_VoucherSuDung_NguoiDung FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND),
    CONSTRAINT CK_VoucherSuDung_SoTien CHECK (SoTienGiam >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
