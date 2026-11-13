-- Migration thêm trạng thái kinh doanh cho danh mục.
ALTER TABLE DanhMuc
    ADD COLUMN TrangThai VARCHAR(30) NOT NULL DEFAULT 'DangKinhDoanh';
