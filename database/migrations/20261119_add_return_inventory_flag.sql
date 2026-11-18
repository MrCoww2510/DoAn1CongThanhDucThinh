-- Add the return-restock guard used by Order::confirmReturnForManagement.
-- Run once against an existing database that does not already have this column.
ALTER TABLE DonHang
    ADD COLUMN DaHoanKho TINYINT(1) NOT NULL DEFAULT 0 AFTER TrangThaiTT;
