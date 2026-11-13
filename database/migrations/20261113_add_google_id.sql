-- Chạy một lần trên database TTComputer hiện có; migration không xóa dữ liệu.
ALTER TABLE TaiKhoan
    ADD COLUMN GoogleID VARCHAR(255) NULL,
    ADD UNIQUE KEY UQ_TaiKhoan_GoogleID (GoogleID);
