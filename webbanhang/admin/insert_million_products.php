<?php
set_time_limit(0); // Không giới hạn thời gian chạy
ini_set('memory_limit', '1024M'); // Tăng memory limit

require_once('connect.php');

// Xóa các sản phẩm test cũ trước khi insert
echo "Đang xóa dữ liệu test cũ...\n";
echo "<br>";
$deleteResult = $conn->query("DELETE FROM san_pham WHERE MASP LIKE 'TEST%'");
if ($deleteResult) {
    echo "Đã xóa " . $conn->affected_rows . " sản phẩm test cũ<br>\n";
} else {
    echo "Lỗi khi xóa: " . $conn->error . "<br>\n";
}

echo "<br>";
echo "Bắt đầu insert 1 triệu sản phẩm mới...\n";
echo "<br>";

// Danh sách mẫu loại sản phẩm và hãng
$loaiSP = ['1', '2', '3', '4', '5']; // LAPTOP, THIẾT BỊ, PC, SMARTWATCH, ĐIỆN THOẠI
$hangSP = [
    '1' => ['1', '2', '3', '4'], // LAPTOP: DELL, ASUS, MACBOOK, SAMSUNG
    '2' => ['5', '6', '7', '8', '9', '10', '11'], // THIẾT BỊ
    '3' => ['12', '13', '14', '15', '16', '17'], // PC
    '4' => ['18', '19', '20', '21', '22', '23'], // SMARTWATCH
    '5' => ['24', '25', '26', '27', '28', '29'] // ĐIỆN THOẠI
];

$batchSize = 1000; // Insert 1000 records mỗi lần
$totalProducts = 1000000; // Tổng số sản phẩm cần insert
$batches = $totalProducts / $batchSize;

// Base64 image placeholder (1x1 pixel transparent gif)
$dummyImage = 'https://cdn.viettelstore.vn/Images/Product/ProductImage/1782776985.jpeg';

$startTime = microtime(true);

for ($batch = 0; $batch < $batches; $batch++) {
    $sql = "INSERT INTO san_pham (MASP, MALOAI, MAHANG, TENSP, ANHSP, GIATHANH, ChiTiet) VALUES ";
    $values = [];
    
    for ($i = 0; $i < $batchSize; $i++) {
        $recordNum = ($batch * $batchSize) + $i + 1;
        
        // Random loại và hãng
        $loai = $loaiSP[array_rand($loaiSP)];
        $hang = $hangSP[$loai][array_rand($hangSP[$loai])];
        
        // Tạo MASP unique
        $masp = 'TEST' . str_pad($recordNum, 7, '0', STR_PAD_LEFT);
        
        // Tạo tên sản phẩm
        $tensp = "Test Product $recordNum - Type $loai";
        
        // Random giá từ 1tr đến 50tr
        $gia = rand(1000000, 50000000);
        
        // Chi tiết sản phẩm
        $chitiet = "Đây là sản phẩm test số $recordNum để kiểm tra performance database. ";
        $chitiet .= "MASP: $masp, MALOAI: $loai, MAHANG: $hang, GIA: $gia. ";
        $chitiet .= "Thông số kỹ thuật: CPU test, RAM test, ROM test, Camera test";
        
        // Escape dữ liệu cho mysqli
        $tensp_escaped = $conn->real_escape_string($tensp);
        $dummyImage_escaped = $conn->real_escape_string($dummyImage);
        $chitiet_escaped = $conn->real_escape_string($chitiet);
        
        $values[] = "('$masp', '$loai', '$hang', '$tensp_escaped', '$dummyImage_escaped', '$gia', '$chitiet_escaped')";
    }
    
    $sql .= implode(', ', $values);
    
    if ($conn->query($sql)) {
        // Hiển thị tiến độ mỗi 10 batch (10,000 records)
        if (($batch + 1) % 10 == 0) {
            $inserted = ($batch + 1) * $batchSize;
            $percent = round(($inserted / $totalProducts) * 100, 2);
            $elapsed = round(microtime(true) - $startTime, 2);
            echo "Đã insert: $inserted sản phẩm ($percent%) - Thời gian: {$elapsed}s<br>\n";
            flush();
            ob_flush();
        }
    } else {
        echo "Lỗi tại batch " . ($batch + 1) . ": " . $conn->error . "<br>\n";
        break;
    }
}

$endTime = microtime(true);
$totalTime = round($endTime - $startTime, 2);

echo "<br>";
echo "====================================<br>";
echo "Hoàn thành!<br>";
echo "Tổng số sản phẩm đã insert: 1,000,000<br>";
echo "Tổng thời gian: {$totalTime} giây (" . round($totalTime/60, 2) . " phút)<br>";
echo "Tốc độ trung bình: " . round($totalProducts/$totalTime, 2) . " records/giây<br>";
echo "====================================<br>";

// Đếm lại số sản phẩm trong database
$result = $conn->query("SELECT COUNT(*) as total FROM san_pham");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<br>Tổng số sản phẩm trong database: " . number_format($row['total']) . "<br>";
} else {
    echo "Lỗi khi đếm: " . $conn->error;
}

$conn->close();

?>
