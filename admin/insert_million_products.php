<?php
ob_start(); 
set_time_limit(0); 
ini_set('memory_limit', '1024M'); 

require_once('connect.php');

// Tải dữ liệu hình ảnh từ bảng chuẩn (nếu tồn tại)
$imageMap = [];
$tableExists = $conn->query("SHOW TABLES LIKE 'san_pham_that_backup'");
if ($tableExists && $tableExists->num_rows > 0) {
    $resBackup = $conn->query("SELECT MALOAI, MAHANG, ANHSP FROM san_pham_that_backup");
    if ($resBackup) {
        while($row = $resBackup->fetch_assoc()) {
            if (!isset($imageMap[$row['MALOAI']][$row['MAHANG']])) {
                $imageMap[$row['MALOAI']][$row['MAHANG']] = $row['ANHSP'];
            }
        }
    }
    echo "Đã load ảnh từ bảng backup.<br>";
} else {
    echo "Bảng backup không tồn tại, sử dụng ảnh mặc định.<br>";
}

// Tải tên Loại và Hãng
$loaiNames = [];
$resL = $conn->query("SELECT MALOAI, TENLOAI FROM loai_san_pham");
while($r = $resL->fetch_assoc()) { $loaiNames[$r['MALOAI']] = $r['TENLOAI']; }

$hangNames = [];
$resH = $conn->query("SELECT MAHANG, TENHANG, loai FROM hang");
while($r = $resH->fetch_assoc()) { $hangNames[$r['loai']][$r['MAHANG']] = $r['TENHANG']; }

echo "Đang dọn dẹp bảng san_pham...<br>";
$conn->query("TRUNCATE TABLE san_pham");

echo "Bắt đầu nạp 1.000.000 sản phẩm...<br>";

$batchSize = 2500; 
$total = 1000000;
$startTime = microtime(true);

for ($batch = 0; $batch < ($total / $batchSize); $batch++) {
    $sql = "INSERT INTO san_pham (MASP, MALOAI, MAHANG, TENSP, ANHSP, GIATHANH, ChiTiet) VALUES ";
    $rows = [];
    
    for ($i = 0; $i < $batchSize; $i++) {
        $num = ($batch * $batchSize) + $i + 1;
        $mLoai = array_rand($hangNames);
        $mHang = array_rand($hangNames[$mLoai]);
        
        $tLoai = $loaiNames[$mLoai];
        $tHang = $hangNames[$mLoai][$mHang];
        $anhsp = isset($imageMap[$mLoai][$mHang]) ? $imageMap[$mLoai][$mHang] : 'https://cdn.viettelstore.vn/Images/Product/ProductImage/1782776985.jpeg';
        
        $masp = 'SP' . str_pad($num, 7, '0', STR_PAD_LEFT);
        $tensp = $conn->real_escape_string("$tLoai $tHang " . str_pad($num, 7, '0', STR_PAD_LEFT));
        $gia = rand(5000000, 45000000);
        
        $rows[] = "('$masp', '$mLoai', '$mHang', '$tensp', '$anhsp', '$gia', 'Sản phẩm chính hãng')";
    }
    
    $sql .= implode(',', $rows);
    if (!$conn->query($sql)) { die("Lỗi: " . $conn->error); }

    if (($batch + 1) % 10 == 0) {
        echo "Đã chèn " . (($batch + 1) * $batchSize) . " dòng...<br>";
        if (ob_get_level() > 0) { ob_flush(); }
        flush();
    }
}

echo "<h3>THÀNH CÔNG! Đã nạp xong 1 triệu sản phẩm.</h3>";
?>