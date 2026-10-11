USE TTComputer;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. DỮ LIỆU NGƯỜI DÙNG
-- ============================================================

INSERT INTO NguoiDung (MaND, HoTen, SDT, Email) VALUES
('ND001','Nguyễn Văn An','0901000001','an.nguyen@gmail.com'),
('ND002','Trần Văn Bình','0901000002','binh.tran@gmail.com'),
('ND003','Lê Minh Châu','0901000003','chau.le@gmail.com'),
('ND004','Phạm Quốc Dũng','0901000004','dung.pham@gmail.com'),
('ND005','Võ Đức Thịnh','0901000005','thinh.vo@gmail.com'),
('ND006','Nguyễn Hoàng Nam','0901000006','nam.nguyen@gmail.com'),
('ND007','Trần Minh Khang','0901000007','khang.tran@gmail.com'),
('ND008','Lê Quốc Huy','0901000008','huy.le@gmail.com'),
('ND009','Phạm Minh Tuấn','0901000009','tuan.pham@gmail.com'),
('ND010','Nguyễn Đức Anh','0901000010','anh.nguyen@gmail.com'),
('ND011','Trần Quốc Việt','0901000011','viet.tran@gmail.com'),
('ND012','Lê Minh Đức','0901000012','duc.le@gmail.com'),
('ND013','Nguyễn Thành Đạt','0901000013','dat.nguyen@gmail.com'),
('ND014','Phạm Gia Hưng','0901000014','hung.pham@gmail.com'),
('ND015','Đặng Minh Quân','0901000015','quan.dang@gmail.com'),
('ND016','Bùi Hoàng Long','0901000016','long.bui@gmail.com'),
('ND017','Nguyễn Nhật Minh','0901000017','minh.nguyen@gmail.com'),
('ND018','Đỗ Anh Khoa','0901000018','khoa.do@gmail.com'),
('ND019','Hồ Minh Khôi','0901000019','khoi.ho@gmail.com'),
('ND020','Phan Thanh Tùng','0901000020','tung.phan@gmail.com');

-- ============================================================
-- 2. DỮ LIỆU TÀI KHOẢN
-- ============================================================

INSERT INTO TaiKhoan
(MaTK, MaND, TenDangNhap, MatKhau, VaiTro, TrangThai) VALUES
('TK001','ND001','nguyenvanan','123456','KhachHang','HoatDong'),
('TK002','ND002','tranvanbinh','123456','KhachHang','HoatDong'),
('TK003','ND003','leminhchau','123456','KhachHang','HoatDong'),
('TK004','ND004','phamquocdung','123456','KhachHang','HoatDong'),
('TK005','ND005','thinhvo','123456','KhachHang','HoatDong'),
('TK006','ND006','nguyenhoangnam','123456','KhachHang','HoatDong'),
('TK007','ND007','tranminhkhang','123456','KhachHang','HoatDong'),
('TK008','ND008','lequochuy','123456','KhachHang','HoatDong'),
('TK009','ND009','phamminhtuan','123456','KhachHang','HoatDong'),
('TK010','ND010','nguyenducanh','123456','KhachHang','HoatDong'),
('TK011','ND011','tranquocviet','123456','NhanVien','HoatDong'),
('TK012','ND012','leminhduc','123456','NhanVien','HoatDong'),
('TK013','ND013','nguyenthanhdat','123456','NhanVien','HoatDong'),
('TK014','ND014','phamgiahung','123456','NhanVien','HoatDong'),
('TK015','ND015','dangminhquan','123456','NhanVien','HoatDong'),
('TK016','ND016','buihoanglong','123456','QuanTriVien','HoatDong'),
('TK017','ND017','nguyennhatminh','123456','QuanTriVien','HoatDong'),
('TK018','ND018','doanhkhoa','123456','KhachHang','HoatDong'),
('TK019','ND019','hominhkhoi','123456','KhachHang','HoatDong'),
('TK020','ND020','phanthanhtung','123456','KhachHang','HoatDong');

-- ============================================================
-- 3. DANH MỤC
-- ============================================================

INSERT INTO DanhMuc (MaDM, TenDM) VALUES
('DM001','Chuột'),
('DM002','Bàn phím'),
('DM003','Tai nghe'),
('DM004','Laptop'),
('DM005','Màn hình'),
('DM006','Ghế Gaming'),
('DM007','Lót chuột'),
('DM008','Card đồ họa'),
('DM009','RAM'),
('DM010','Ổ cứng SSD');

-- ============================================================
-- 4. 120 SẢN PHẨM
-- ============================================================

INSERT INTO SanPham
(MaSP, MaDM, TenSP, GiaBan, SoLuongTon) VALUES

-- CHUỘT - 15
('SP001','DM001','Logitech G102 Lightsync',599000,45),
('SP002','DM001','Logitech G304 Lightspeed',899000,32),
('SP003','DM001','Logitech G502 Hero',1299000,28),
('SP004','DM001','Logitech G502 X',2499000,20),
('SP005','DM001','Razer DeathAdder Essential',699000,40),
('SP006','DM001','Razer DeathAdder V2',1099000,35),
('SP007','DM001','Razer Basilisk V3',1399000,25),
('SP008','DM001','Razer Viper Mini',799000,38),
('SP009','DM001','ASUS TUF Gaming M3',549000,30),
('SP010','DM001','ASUS ROG Gladius III',2299000,18),
('SP011','DM001','Corsair Katar Pro',649000,25),
('SP012','DM001','Corsair Sabre RGB Pro',1699000,17),
('SP013','DM001','SteelSeries Rival 3',799000,30),
('SP014','DM001','HyperX Pulsefire Haste',999000,26),
('SP015','DM001','DareU EM908',499000,42),

-- BÀN PHÍM - 15
('SP016','DM002','Logitech K120',249000,50),
('SP017','DM002','Logitech G213 Prodigy',1099000,30),
('SP018','DM002','Logitech G413 SE',1399000,24),
('SP019','DM002','Razer BlackWidow V3',2299000,18),
('SP020','DM002','Razer Huntsman Mini',2499000,20),
('SP021','DM002','Razer Ornata V3',1899000,22),
('SP022','DM002','ASUS TUF Gaming K1',899000,28),
('SP023','DM002','ASUS ROG Strix Scope RX',2499000,15),
('SP024','DM002','Corsair K55 RGB Pro',1299000,25),
('SP025','DM002','Corsair K70 RGB Pro',3999000,12),
('SP026','DM002','SteelSeries Apex 3',1399000,20),
('SP027','DM002','SteelSeries Apex Pro',4999000,10),
('SP028','DM002','HyperX Alloy Origins',2299000,18),
('SP029','DM002','Akko 5075B Plus',1999000,23),
('SP030','DM002','DareU EK87',899000,30),

-- TAI NGHE - 15
('SP031','DM003','Logitech G335',1699000,25),
('SP032','DM003','Logitech G435 Lightspeed',1899000,20),
('SP033','DM003','Logitech G733 Lightspeed',2999000,18),
('SP034','DM003','Razer BlackShark V2 X',1099000,35),
('SP035','DM003','Razer Kraken V3',1999000,20),
('SP036','DM003','Razer Barracuda X',2499000,17),
('SP037','DM003','ASUS TUF Gaming H3',999000,30),
('SP038','DM003','ASUS ROG Delta S',2999000,15),
('SP039','DM003','Corsair HS55 Stereo',1199000,24),
('SP040','DM003','Corsair HS80 RGB Wireless',2999000,14),
('SP041','DM003','SteelSeries Arctis Nova 1',1899000,20),
('SP042','DM003','SteelSeries Arctis Nova 7',4299000,12),
('SP043','DM003','HyperX Cloud Stinger 2',999000,28),
('SP044','DM003','HyperX Cloud III',2399000,20),
('SP045','DM003','DareU EH416',599000,35),

-- LAPTOP - 15
('SP046','DM004','ASUS Vivobook 15',14990000,10),
('SP047','DM004','ASUS Vivobook 16',16990000,12),
('SP048','DM004','ASUS TUF Gaming A15',24990000,8),
('SP049','DM004','ASUS ROG Strix G16',39990000,5),
('SP050','DM004','Lenovo IdeaPad Slim 3',12990000,15),
('SP051','DM004','Lenovo LOQ 15',22990000,8),
('SP052','DM004','Lenovo Legion 5',29990000,6),
('SP053','DM004','Acer Aspire 5',13990000,12),
('SP054','DM004','Acer Nitro V15',21990000,10),
('SP055','DM004','Acer Predator Helios Neo 16',32990000,5),
('SP056','DM004','HP 15s',11990000,14),
('SP057','DM004','HP Victus 15',21990000,9),
('SP058','DM004','Dell Inspiron 15',15990000,11),
('SP059','DM004','MSI Thin 15',19990000,10),
('SP060','DM004','MSI Katana 15',26990000,7),

-- MÀN HÌNH - 15
('SP061','DM005','LG UltraGear 24GN60R',3999000,20),
('SP062','DM005','LG UltraGear 27GN800',6999000,15),
('SP063','DM005','LG UltraGear 27GR75Q',7499000,12),
('SP064','DM005','Samsung Odyssey G3 24',4499000,18),
('SP065','DM005','Samsung Odyssey G5 27',6999000,14),
('SP066','DM005','Samsung Odyssey G7 32',11990000,8),
('SP067','DM005','ASUS TUF Gaming VG249Q1A',4299000,20),
('SP068','DM005','ASUS TUF Gaming VG27AQ',7999000,12),
('SP069','DM005','ASUS ROG Swift PG279QM',16990000,6),
('SP070','DM005','AOC 24G2SP',3999000,25),
('SP071','DM005','AOC Q27G2S',6499000,15),
('SP072','DM005','AOC C32G2ZE',6999000,10),
('SP073','DM005','ViewSonic VX2428',3899000,20),
('SP074','DM005','Gigabyte G27Q',7499000,13),
('SP075','DM005','Gigabyte M27Q',8999000,10),

-- GHẾ GAMING - 10
('SP076','DM006','DAREU GC-238',2999000,15),
('SP077','DM006','DAREU GC-242',3499000,12),
('SP078','DM006','DXRacer Formula',5999000,8),
('SP079','DM006','DXRacer Air',6999000,6),
('SP080','DM006','AKRacing Core Series',7499000,7),
('SP081','DM006','Cougar Armor One',4999000,10),
('SP082','DM006','Cougar Explore S',5999000,8),
('SP083','DM006','Warrior WGC101',2999000,12),
('SP084','DM006','E-Dra Jupiter EGC204',3999000,10),
('SP085','DM006','E-Dra Citizen EGC200',3299000,11),

-- LÓT CHUỘT - 10
('SP086','DM007','Logitech G240',399000,30),
('SP087','DM007','Logitech G640',799000,25),
('SP088','DM007','Razer Gigantus V2 Medium',499000,35),
('SP089','DM007','Razer Gigantus V2 XXL',999000,20),
('SP090','DM007','ASUS ROG Scabbard II',899000,18),
('SP091','DM007','Corsair MM300',499000,30),
('SP092','DM007','Corsair MM700',1299000,15),
('SP093','DM007','SteelSeries QcK Medium',399000,40),
('SP094','DM007','SteelSeries QcK Heavy',699000,30),
('SP095','DM007','HyperX Pulsefire Mat',599000,25),

-- CARD ĐỒ HỌA - 10
('SP096','DM008','ASUS Dual RTX 4060 8GB',8499000,10),
('SP097','DM008','Gigabyte RTX 4060 Windforce',8299000,12),
('SP098','DM008','MSI RTX 4060 Ventus 2X',8699000,9),
('SP099','DM008','ASUS Dual RTX 5060 8GB',9999000,10),
('SP100','DM008','Gigabyte RTX 5060 Windforce',10290000,12),
('SP101','DM008','MSI RTX 5060 Ventus 2X',10490000,8),
('SP102','DM008','ASUS Dual RTX 5060 Ti 8GB',11990000,7),
('SP103','DM008','Gigabyte RTX 5060 Ti Gaming OC',12990000,6),
('SP104','DM008','MSI RTX 5060 Ti 16GB',14990000,5),
('SP105','DM008','ASUS RTX 5070 12GB',17990000,4),

-- RAM - 7
('SP106','DM009','Kingston Fury Beast 8GB DDR4',499000,35),
('SP107','DM009','Kingston Fury Beast 16GB DDR4',899000,30),
('SP108','DM009','Kingston Fury Beast 32GB DDR4',1699000,20),
('SP109','DM009','Corsair Vengeance 16GB DDR4',999000,25),
('SP110','DM009','Corsair Vengeance 32GB DDR4',1899000,18),
('SP111','DM009','Corsair Vengeance 16GB DDR5',1299000,25),
('SP112','DM009','Corsair Vengeance 32GB DDR5',2399000,15),

-- SSD - 8
('SP113','DM010','Samsung 980 500GB',1199000,25),
('SP114','DM010','Samsung 990 EVO 1TB',2499000,18),
('SP115','DM010','WD Blue SN580 500GB',999000,30),
('SP116','DM010','WD Blue SN580 1TB',1799000,25),
('SP117','DM010','Kingston NV2 500GB',899000,35),
('SP118','DM010','Kingston NV2 1TB',1599000,30),
('SP119','DM010','Crucial P3 Plus 1TB',1699000,20),
('SP120','DM010','Lexar NM790 1TB',1999000,18);

-- ============================================================
-- 5. CHI TIẾT SẢN PHẨM
-- ============================================================
-- Dùng INSERT ... SELECT để tự tạo đủ 120 dòng CTSP.
-- Không dùng cú pháp SQL Server như ROW_NUMBER + toán tử + để nối chuỗi.

INSERT INTO ChiTietSanPham
(MaCTSP, MaSP, LoaiSP, ThongSoKT, ThuongHieu, HinhAnh, MoTa, TrangThai)
SELECT
    CONCAT('CT', LPAD(CAST(SUBSTRING(MaSP, 3) AS UNSIGNED), 3, '0')),
    MaSP,

    CASE MaDM
        WHEN 'DM001' THEN 'Chuột Gaming'
        WHEN 'DM002' THEN 'Bàn phím Gaming'
        WHEN 'DM003' THEN 'Tai nghe Gaming'
        WHEN 'DM004' THEN 'Laptop'
        WHEN 'DM005' THEN 'Màn hình Gaming'
        WHEN 'DM006' THEN 'Ghế Gaming'
        WHEN 'DM007' THEN 'Lót chuột'
        WHEN 'DM008' THEN 'Card đồ họa'
        WHEN 'DM009' THEN 'RAM'
        WHEN 'DM010' THEN 'Ổ cứng SSD'
    END,

    CASE MaDM
        WHEN 'DM001' THEN 'Kết nối USB, DPI cao, cảm biến chính xác, thiết kế công thái học'
        WHEN 'DM002' THEN 'Bàn phím cơ, kết nối USB, đèn LED RGB, thiết kế gaming'
        WHEN 'DM003' THEN 'Âm thanh Stereo, microphone tích hợp, đệm tai êm ái'
        WHEN 'DM004' THEN 'CPU hiệu năng cao, RAM dung lượng lớn, SSD tốc độ cao, màn hình Full HD'
        WHEN 'DM005' THEN 'Độ phân giải cao, tần số quét cao, thời gian phản hồi thấp'
        WHEN 'DM006' THEN 'Khung thép chắc chắn, đệm mút dày, tựa lưng công thái học'
        WHEN 'DM007' THEN 'Bề mặt vải mịn, chống trượt, kích thước phù hợp gaming'
        WHEN 'DM008' THEN 'GPU hiệu năng cao, hỗ trợ Ray Tracing, DLSS, bộ nhớ GDDR'
        WHEN 'DM009' THEN 'Bộ nhớ hiệu năng cao, hỗ trợ đa nhiệm và gaming'
        WHEN 'DM010' THEN 'Ổ cứng SSD NVMe tốc độ cao, chuẩn PCIe, độ bền tốt'
    END,

    CASE MaDM
        WHEN 'DM001' THEN
            CASE
                WHEN MaSP IN ('SP001','SP002','SP003','SP004') THEN 'Logitech'
                WHEN MaSP IN ('SP005','SP006','SP007','SP008') THEN 'Razer'
                WHEN MaSP IN ('SP009','SP010') THEN 'ASUS'
                WHEN MaSP IN ('SP011','SP012') THEN 'Corsair'
                WHEN MaSP = 'SP013' THEN 'SteelSeries'
                WHEN MaSP = 'SP014' THEN 'HyperX'
                ELSE 'DareU'
            END

        WHEN 'DM002' THEN
            CASE
                WHEN MaSP IN ('SP016','SP017','SP018') THEN 'Logitech'
                WHEN MaSP IN ('SP019','SP020','SP021') THEN 'Razer'
                WHEN MaSP IN ('SP022','SP023') THEN 'ASUS'
                WHEN MaSP IN ('SP024','SP025') THEN 'Corsair'
                WHEN MaSP IN ('SP026','SP027') THEN 'SteelSeries'
                WHEN MaSP = 'SP028' THEN 'HyperX'
                WHEN MaSP = 'SP029' THEN 'Akko'
                ELSE 'DareU'
            END

        WHEN 'DM003' THEN
            CASE
                WHEN MaSP IN ('SP031','SP032','SP033') THEN 'Logitech'
                WHEN MaSP IN ('SP034','SP035','SP036') THEN 'Razer'
                WHEN MaSP IN ('SP037','SP038') THEN 'ASUS'
                WHEN MaSP IN ('SP039','SP040') THEN 'Corsair'
                WHEN MaSP IN ('SP041','SP042') THEN 'SteelSeries'
                WHEN MaSP IN ('SP043','SP044') THEN 'HyperX'
                ELSE 'DareU'
            END

        WHEN 'DM004' THEN
            CASE
                WHEN MaSP IN ('SP046','SP047','SP048','SP049') THEN 'ASUS'
                WHEN MaSP IN ('SP050','SP051','SP052') THEN 'Lenovo'
                WHEN MaSP IN ('SP053','SP054','SP055') THEN 'Acer'
                WHEN MaSP IN ('SP056','SP057') THEN 'HP'
                WHEN MaSP = 'SP058' THEN 'Dell'
                ELSE 'MSI'
            END

        WHEN 'DM005' THEN
            CASE
                WHEN MaSP IN ('SP061','SP062','SP063') THEN 'LG'
                WHEN MaSP IN ('SP064','SP065','SP066') THEN 'Samsung'
                WHEN MaSP IN ('SP067','SP068','SP069') THEN 'ASUS'
                WHEN MaSP IN ('SP070','SP071','SP072') THEN 'AOC'
                WHEN MaSP = 'SP073' THEN 'ViewSonic'
                ELSE 'Gigabyte'
            END

        WHEN 'DM006' THEN
            CASE
                WHEN MaSP IN ('SP076','SP077') THEN 'DAREU'
                WHEN MaSP IN ('SP078','SP079') THEN 'DXRacer'
                WHEN MaSP = 'SP080' THEN 'AKRacing'
                WHEN MaSP IN ('SP081','SP082') THEN 'Cougar'
                ELSE 'E-Dra'
            END

        WHEN 'DM007' THEN
            CASE
                WHEN MaSP IN ('SP086','SP087') THEN 'Logitech'
                WHEN MaSP IN ('SP088','SP089') THEN 'Razer'
                WHEN MaSP = 'SP090' THEN 'ASUS'
                WHEN MaSP IN ('SP091','SP092') THEN 'Corsair'
                WHEN MaSP IN ('SP093','SP094') THEN 'SteelSeries'
                ELSE 'HyperX'
            END

        WHEN 'DM008' THEN
            CASE
                WHEN MaSP IN ('SP096','SP099') THEN 'ASUS'
                WHEN MaSP IN ('SP097','SP100','SP103') THEN 'Gigabyte'
                ELSE 'MSI'
            END

        WHEN 'DM009' THEN
            CASE
                WHEN MaSP IN ('SP106','SP107','SP108') THEN 'Kingston'
                ELSE 'Corsair'
            END

        WHEN 'DM010' THEN
            CASE
                WHEN MaSP IN ('SP113','SP114') THEN 'Samsung'
                WHEN MaSP IN ('SP115','SP116') THEN 'Western Digital'
                WHEN MaSP IN ('SP117','SP118') THEN 'Kingston'
                WHEN MaSP = 'SP119' THEN 'Crucial'
                ELSE 'Lexar'
            END
    END,

    CONCAT('/images/products/', MaSP, '.jpg'),
    'Sản phẩm chính hãng, phù hợp cho nhu cầu học tập, làm việc và gaming.',
    'Đang bán'

FROM SanPham
ORDER BY MaSP;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- KIỂM TRA DỮ LIỆU
-- ============================================================

SELECT 'NguoiDung' AS Bang, COUNT(*) AS SoLuong FROM NguoiDung
UNION ALL
SELECT 'TaiKhoan', COUNT(*) FROM TaiKhoan
UNION ALL
SELECT 'DanhMuc', COUNT(*) FROM DanhMuc
UNION ALL
SELECT 'SanPham', COUNT(*) FROM SanPham
UNION ALL
SELECT 'ChiTietSanPham', COUNT(*) FROM ChiTietSanPham;

-- Kết quả mong đợi:
-- NguoiDung       = 20
-- TaiKhoan        = 20
-- DanhMuc         = 10
-- SanPham         = 120
-- ChiTietSanPham  = 120