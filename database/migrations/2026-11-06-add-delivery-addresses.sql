-- Migration tạo bảng địa chỉ nhận hàng và liên kết với người dùng.
CREATE TABLE IF NOT EXISTS DiaChiNhanHang (
    MaDiaChi INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    MaND VARCHAR(20) NOT NULL,
    DiaChi VARCHAR(255) NOT NULL,
    UNIQUE KEY UQ_DiaChiNhanHang_MaND_DiaChi (MaND, DiaChi),
    CONSTRAINT FK_DiaChiNhanHang_NguoiDung
        FOREIGN KEY (MaND)
        REFERENCES NguoiDung(MaND)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
