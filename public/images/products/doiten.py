import os

# ============================================================
# CẤU HÌNH
# ============================================================

# Thư mục chứa ảnh
FOLDER = r"C:\xampp\htdocs\DoAn1CongThanhDucThinh\public\images\products"

# ============================================================
# DANH SÁCH SẢN PHẨM
# ============================================================

products = {
    "SP001": "Logitech G102 Lightsync",
    "SP002": "Logitech G304 Lightspeed",
    "SP003": "Logitech G502 Hero",
    "SP004": "Logitech G502 X",
    "SP005": "Razer DeathAdder Essential",
    "SP006": "Razer DeathAdder V2",
    "SP007": "Razer Basilisk V3",
    "SP008": "Razer Viper Mini",
    "SP009": "ASUS TUF Gaming M3",
    "SP010": "ASUS ROG Gladius III",
    "SP011": "Corsair Katar Pro",
    "SP012": "Corsair Sabre RGB Pro",
    "SP013": "SteelSeries Rival 3",
    "SP014": "HyperX Pulsefire Haste",
    "SP015": "DareU EM908",
    "SP016": "Logitech K120",
    "SP017": "Logitech G213 Prodigy",
    "SP018": "Logitech G413 SE",
    "SP019": "Razer BlackWidow V3",
    "SP020": "Razer Huntsman Mini",
    "SP021": "Razer Ornata V3",
    "SP022": "ASUS TUF Gaming K1",
    "SP023": "ASUS ROG Strix Scope RX",
    "SP024": "Corsair K55 RGB Pro",
    "SP025": "Corsair K70 RGB Pro",
    "SP026": "SteelSeries Apex 3",
    "SP027": "SteelSeries Apex Pro",
    "SP028": "HyperX Alloy Origins",
    "SP029": "Akko 5075B Plus",
    "SP030": "DareU EK87",
    "SP031": "Logitech G335",
    "SP032": "Logitech G435 Lightspeed",
    "SP033": "Logitech G733 Lightspeed",
    "SP034": "Razer BlackShark V2 X",
    "SP035": "Razer Kraken V3",
    "SP036": "Razer Barracuda X",
    "SP037": "ASUS TUF Gaming H3",
    "SP038": "ASUS ROG Delta S",
    "SP039": "Corsair HS55 Stereo",
    "SP040": "Corsair HS80 RGB Wireless",
    "SP041": "SteelSeries Arctis Nova 1",
    "SP042": "SteelSeries Arctis Nova 7",
    "SP043": "HyperX Cloud Stinger 2",
    "SP044": "HyperX Cloud III",
    "SP045": "DareU EH416",
    "SP046": "ASUS Vivobook 15",
    "SP047": "ASUS Vivobook 16",
    "SP048": "ASUS TUF Gaming A15",
    "SP049": "ASUS ROG Strix G16",
    "SP050": "Lenovo IdeaPad Slim 3",
    "SP051": "Lenovo LOQ 15",
    "SP052": "Lenovo Legion 5",
    "SP053": "Acer Aspire 5",
    "SP054": "Acer Nitro V15",
    "SP055": "Acer Predator Helios Neo 16",
    "SP056": "HP 15s",
    "SP057": "HP Victus 15",
    "SP058": "Dell Inspiron 15",
    "SP059": "MSI Thin 15",
    "SP060": "MSI Katana 15",
    "SP061": "LG UltraGear 24GN60R",
    "SP062": "LG UltraGear 27GN800",
    "SP063": "LG UltraGear 27GR75Q",
    "SP064": "Samsung Odyssey G3 24",
    "SP065": "Samsung Odyssey G5 27",
    "SP066": "Samsung Odyssey G7 32",
    "SP067": "ASUS TUF Gaming VG249Q1A",
    "SP068": "ASUS TUF Gaming VG27AQ",
    "SP069": "ASUS ROG Swift PG279QM",
    "SP070": "AOC 24G2SP",
    "SP071": "AOC Q27G2S",
    "SP072": "AOC C32G2ZE",
    "SP073": "ViewSonic VX2428",
    "SP074": "Gigabyte G27Q",
    "SP075": "Gigabyte M27Q",
    "SP076": "DAREU GC-238",
    "SP077": "DAREU GC-242",
    "SP078": "DXRacer Formula",
    "SP079": "DXRacer Air",
    "SP080": "AKRacing Core Series",
    "SP081": "Cougar Armor One",
    "SP082": "Cougar Explore S",
    "SP083": "Warrior WGC101",
    "SP084": "E-Dra Jupiter EGC204",
    "SP085": "E-Dra Citizen EGC200",
    "SP086": "Logitech G240",
    "SP087": "Logitech G640",
    "SP088": "Razer Gigantus V2 Medium",
    "SP089": "Razer Gigantus V2 XXL",
    "SP090": "ASUS ROG Scabbard II",
    "SP091": "Corsair MM300",
    "SP092": "Corsair MM700",
    "SP093": "SteelSeries QcK Medium",
    "SP094": "SteelSeries QcK Heavy",
    "SP095": "HyperX Pulsefire Mat",
    "SP096": "ASUS Dual RTX 4060 8GB",
    "SP097": "Gigabyte RTX 4060 Windforce",
    "SP098": "MSI RTX 4060 Ventus 2X",
    "SP099": "ASUS Dual RTX 5060 8GB",
    "SP100": "Gigabyte RTX 5060 Windforce",
    "SP101": "MSI RTX 5060 Ventus 2X",
    "SP102": "ASUS Dual RTX 5060 Ti 8GB",
    "SP103": "Gigabyte RTX 5060 Ti Gaming OC",
    "SP104": "MSI RTX 5060 Ti 16GB",
    "SP105": "ASUS RTX 5070 12GB",
    "SP106": "Kingston Fury Beast 8GB DDR4",
    "SP107": "Kingston Fury Beast 16GB DDR4",
    "SP108": "Kingston Fury Beast 32GB DDR4",
    "SP109": "Corsair Vengeance 16GB DDR4",
    "SP110": "Corsair Vengeance 32GB DDR4",
    "SP111": "Corsair Vengeance 16GB DDR5",
    "SP112": "Corsair Vengeance 32GB DDR5",
    "SP113": "Samsung 980 500GB",
    "SP114": "Samsung 990 EVO 1TB",
    "SP115": "WD Blue SN580 500GB",
    "SP116": "WD Blue SN580 1TB",
    "SP117": "Kingston NV2 500GB",
    "SP118": "Kingston NV2 1TB",
    "SP119": "Crucial P3 Plus 1TB",
    "SP120": "Lexar NM790 1TB",
}

# ============================================================
# ĐỔI TÊN
# ============================================================

# Các định dạng ảnh hỗ trợ
extensions = {
    ".jpg", ".jpeg", ".png", ".webp",
    ".bmp", ".gif", ".tiff", ".tif"
}

# Tạo mapping: tên sản phẩm -> mã SP
name_to_code = {
    name.lower(): code
    for code, name in products.items()
}

# Đọc toàn bộ file trong thư mục
files = os.listdir(FOLDER)

renamed = 0
not_found = []

for code, product_name in products.items():

    found = False

    for filename in files:

        # Bỏ qua file không phải ảnh
        ext = os.path.splitext(filename)[1].lower()

        if ext not in extensions:
            continue

        # Tên file không có phần mở rộng
        filename_without_ext = os.path.splitext(filename)[0]

        # So sánh tên sản phẩm
        if filename_without_ext.strip().lower() == product_name.lower():

            old_path = os.path.join(FOLDER, filename)
            new_path = os.path.join(FOLDER, code + ext)

            # Nếu file đích đã tồn tại thì báo và bỏ qua
            if os.path.exists(new_path):
                print(f"[BỎ QUA] {code} - File đã tồn tại: {code + ext}")
                found = True
                break

            os.rename(old_path, new_path)

            print(f"[OK] {filename}  ->  {code + ext}")

            renamed += 1
            found = True
            break

    if not found:
        not_found.append(f"{code} - {product_name}")


# ============================================================
# KẾT QUẢ
# ============================================================

print("\n" + "=" * 60)
print("HOÀN TẤT")
print("=" * 60)

print(f"Đã đổi tên: {renamed} ảnh")
print(f"Không tìm thấy: {len(not_found)} ảnh")

if not_found:
    print("\nCác ảnh không tìm thấy:")
    for item in not_found:
        print(" -", item)