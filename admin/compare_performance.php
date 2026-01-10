<?php
// Cấu hình hệ thống để xử lý dữ liệu lớn
ini_set('memory_limit', '2048M'); 
set_time_limit(600);             

require_once('connect.php');

/**
 * Hàm đo thời gian thực thi Query
 */
function measure_query($conn, $sql) {
    // Sử dụng SQL_NO_CACHE để kết quả đo khách quan nhất (không lấy từ RAM)
    $sql_no_cache = str_replace("SELECT", "SELECT SQL_NO_CACHE", $sql);
    
    $start = microtime(true);
    $result = $conn->query($sql_no_cache);
    $end = microtime(true);
    
    return [
        'time' => ($end - $start),
        'count' => $result ? $result->num_rows : 0,
        'result' => $result
    ];
}

// Nhận tham số từ form
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 1000;
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : 'Laptop';
$search_masp = isset($_POST['search_masp']) ? $_POST['search_masp'] : '';

$res1 = $res2 = $res3 = $res4 = null;
$product_found = null;
$products_noindex = [];
$products_index = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $sql_unoptimized = "SELECT * FROM san_pham WHERE TENSP LIKE '%$keyword%' LIMIT $limit";
    $res1 = measure_query($conn, $sql_unoptimized);
    
    if ($res1['result']) {
        $count = 0;
        while ($row = $res1['result']->fetch_assoc()) {
            if ($count >= 100) break; // Chỉ lấy 100 mẫu hiển thị
            $products_noindex[] = $row;
            $count++;
        }
        $res1['result']->free(); 
    }

    $sql_optimized = "SELECT * FROM san_pham WHERE TENSP LIKE '$keyword%' LIMIT $limit";
    $res2 = measure_query($conn, $sql_optimized);
    
    if ($res2['result']) {
        $count = 0;
        while ($row = $res2['result']->fetch_assoc()) {
            if ($count >= 100) break;
            $products_index[] = $row;
            $count++;
        }
        $res2['result']->free();
    }

    //  TÌM KIẾM THEO MÃ SẢN PHẨM (MASP) ---
    if ($search_masp) {
        $sql_search = "SELECT * FROM san_pham WHERE MASP = '$search_masp'";
        
        // Đo thời gian tìm kiếm
        $res3 = measure_query($conn, $sql_search); 
        if ($res3['count'] > 0) {
            $product_found = $res3['result']->fetch_assoc();
        }
        $res4 = measure_query($conn, $sql_search); 
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>So Sánh Hiệu Năng 1 Triệu Dòng</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #d32f2f; margin-bottom: 30px; text-transform: uppercase; }
        
        .form-section { background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #dee2e6; margin-bottom: 30px; }
        .input-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { width: 100%; background: #d32f2f; color: white; padding: 15px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #dee2e6; text-align: center; }
        th { background: #343a40; color: white; }
        .slow { color: #d32f2f; font-weight: bold; }
        .fast { color: #2e7d32; font-weight: bold; }
        
        .section-title { background: #333; color: white; padding: 10px; border-radius: 6px; margin-top: 30px; text-align: center; font-weight: bold; }
        .product-preview { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px; }
        .scroll-area { height: 500px; overflow-y: auto; background: #f1f3f5; padding: 15px; border-radius: 8px; border: 1px solid #ddd; }
        .item { background: white; display: flex; gap: 10px; padding: 10px; margin-bottom: 10px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .item img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .detail-table { width: 100%; margin: 0; text-align: left; }
        .detail-table td { text-align: left; padding: 8px; }
    </style>
</head>
<body>

<div class="container">
    <h2> HỆ THỐNG SO SÁNH HIỆU NĂNG </h2>
    
    <div class="form-section">
        <form method="POST">
            <div class="input-grid">
                <div>
                    <label>Số lượng cần load:</label>
                    <input type="number" name="limit" value="<?php echo $limit; ?>" max="1000000">
                </div>
                <div>
                    <label>Từ khóa (TENSP):</label>
                    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <div>
                    <label>Tìm Mã SP (MASP):</label>
                    <input type="text" name="search_masp" value="<?php echo htmlspecialchars($search_masp); ?>" placeholder="VD: SP000001">
                </div>
            </div>
            <button type="submit" class="btn-submit">CHẠY TEST HIỆU NĂNG TỔNG THỂ</button>
        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        
        <?php if ($res1 && $res2): ?>
        <div class="section-title">KẾT QUẢ PHÂN TÍCH LOAD <?php echo number_format($limit); ?> DÒNG</div>
        <table>
            <tr>
                <th>Tiêu chí</th>
                <th>❌ Chưa tối ưu (Full Scan)</th>
                <th>✅ Đã tối ưu (Index Scan)</th>
            </tr>
            <tr>
                <td>Thời gian thực thi</td>
                <td class="slow"><?php echo number_format($res1['time'], 6); ?> giây</td>
                <td class="fast"><?php echo number_format($res2['time'], 6); ?> giây</td>
            </tr>
            <tr>
                <td>Số lượng đã quét</td>
                <td class="slow"><?php echo number_format($res1['count']); ?> sản phẩm</td>
                <td class="fast"><?php echo number_format($res2['count']); ?> sản phẩm</td>
            </tr>
            <tr>
                <td>Hiệu quả</td>
                <td>Chậm (Gây lag Server)</td>
                <td class="fast">Nhanh gấp <?php echo ($res2['time'] > 0) ? number_format($res1['time'] / $res2['time'], 2) : 'vô cực'; ?> lần</td>
            </tr>
        </table>

        <div class="product-preview">
            <div>
                <h4 style="text-align:center; color:#d32f2f">❌ Không Index (Hiển thị mẫu 100/<?php echo number_format($res1['count']); ?>)</h4>
                <div class="scroll-area">
                    <?php foreach ($products_noindex as $p): ?>
                        <div class="item">
                            <img src="<?php echo $p['ANHSP']; ?>">
                            <div>
                                <small><?php echo $p['MASP']; ?></small>
                                <div style="font-weight:bold; font-size:12px;"><?php echo $p['TENSP']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <h4 style="text-align:center; color:#2e7d32">✅ Có Index (Hiển thị mẫu 100/<?php echo number_format($res2['count']); ?>)</h4>
                <div class="scroll-area">
                    <?php foreach ($products_index as $p): ?>
                        <div class="item">
                            <img src="<?php echo $p['ANHSP']; ?>">
                            <div>
                                <small><?php echo $p['MASP']; ?></small>
                                <div style="font-weight:bold; font-size:12px;"><?php echo $p['TENSP']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($search_masp && $res3): ?>
            <div class="section-title">KẾT QUẢ TÌM KIẾM THEO MÃ: <?php echo htmlspecialchars($search_masp); ?></div>
            
            <?php if ($product_found): ?>
            <div style="display: flex; gap: 20px; background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 15px; border: 1px solid #ddd;">
                <img src="<?php echo $product_found['ANHSP']; ?>" style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px;">
                <table class="detail-table">
                    <tr><td><b>Mã sản phẩm:</b></td><td><?php echo $product_found['MASP']; ?></td></tr>
                    <tr><td><b>Tên sản phẩm:</b></td><td><?php echo $product_found['TENSP']; ?></td></tr>
                    <tr><td><b>Giá thành:</b></td><td class="slow"><?php echo number_format($product_found['GIATHANH'], 0, ',', '.'); ?> đ</td></tr>
                    <tr><td><b>Mã loại:</b></td><td><?php echo $product_found['MALOAI']; ?></td></tr>
                    <tr><td><b>Mã hãng:</b></td><td><?php echo $product_found['MAHANG']; ?></td></tr>
                </table>
            </div>
            <?php else: ?>
                <p style="text-align:center; color: red; margin-top: 15px;">❌ Không tìm thấy sản phẩm có mã này!</p>
            <?php endif; ?>

            <table style="margin-top: 15px;">
                <tr>
                    <th>Tiêu chí</th>
                    <th>Tìm kiếm không dùng Index</th>
                    <th>Tìm kiếm có dùng Index (MASP)</th>
                </tr>
                <tr>
                    <td>Thời gian thực thi</td>
                    <td class="slow"><?php echo number_format($res3['time'], 6); ?> giây</td>
                    <td class="fast"><?php echo number_format($res4['time'], 6); ?> giây</td>
                </tr>
            </table>
        <?php endif; ?>

    <?php endif; ?>
</div>

</body>
</html>