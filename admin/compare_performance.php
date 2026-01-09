<?php
require_once('connect.php');

function measure_query($conn, $sql) {
    $start = microtime(true);
    $result = $conn->query($sql);
    $end = microtime(true);
    return [
        'time' => ($end - $start),
        'count' => $result ? $result->num_rows : 0,
        'result' => $result
    ];
}

// Nhận tham số từ form
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 100;
$search_masp = isset($_POST['search_masp']) ? $_POST['search_masp'] : '';
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : 'Laptop Dell';

$res1 = null;
$res2 = null;
$res3 = null;
$res4 = null;
$product_found = null;
$products_noindex = [];
$products_index = [];

// Nếu có submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // TH1: Load N sản phẩm - CHƯA TỐI ƯU (Full Table Scan)
    $sql_unoptimized = "SELECT * FROM san_pham WHERE TENSP LIKE '%$keyword%' LIMIT $limit";
    $res1 = measure_query($conn, $sql_unoptimized);
    
    // Lưu danh sách sản phẩm không index (giới hạn 100 để tránh hết RAM)
    $display_limit = min(100, $limit); // Chỉ hiển thị tối đa 100 sản phẩm
    if ($res1['result'] && $limit <= 10000) { // Chỉ load vào mảng nếu <= 10000
        $count = 0;
        while ($row = $res1['result']->fetch_assoc()) {
            if ($count >= $display_limit) break;
            $products_noindex[] = $row;
            $count++;
        }
    }

    // TH2: Load N sản phẩm - ĐÃ TỐI ƯU (Với Index)
    // Lấy từ đầu tiên của keyword để tìm kiếm với index
    $first_word = explode(' ', trim($keyword))[0];
    $sql_optimized = "SELECT * FROM san_pham WHERE TENSP LIKE '$first_word%' LIMIT $limit";
    $res2 = measure_query($conn, $sql_optimized);
    
    // Lưu danh sách sản phẩm có index (giới hạn 100 để tránh hết RAM)
    if ($res2['result'] && $limit <= 10000) { // Chỉ load vào mảng nếu <= 10000
        $count = 0;
        while ($row = $res2['result']->fetch_assoc()) {
            if ($count >= $display_limit) break;
            $products_index[] = $row;
            $count++;
        }
    }
    
    // TH3: Search theo MASP - CHƯA INDEX
    if ($search_masp) {
        $sql_search_noindex = "SELECT * FROM san_pham WHERE MASP = '$search_masp'";
        $res3 = measure_query($conn, $sql_search_noindex);
        
        // Lấy thông tin sản phẩm
        if ($res3['count'] > 0) {
            $product_found = $res3['result']->fetch_assoc();
        }
        
        // TH4: Search theo MASP - CÓ INDEX (giả sử đã tạo index trên MASP)
        $res4 = measure_query($conn, $sql_search_noindex); // Cùng query nhưng có/không index tùy vào DB
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>So sánh hiệu năng 1 triệu dòng</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #f44336; margin-bottom: 30px; }
        
        .form-section { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group input[type="number"] { width: 200px; }
        .btn-submit { background: #f44336; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-submit:hover { background: #d32f2f; }
        
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #f44336; color: white; }
        .fast { color: green; font-weight: bold; }
        .slow { color: red; font-weight: bold; }
        .section-title { background: #333; color: white; padding: 10px; margin-top: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔥 SO SÁNH HIỆU NĂNG TRÊN 1 TRIỆU DÒNG 🔥</h2>
        
        <div class="form-section">
            <form method="POST">
                <div class="form-group">
                    <label>📊 Số lượng sản phẩm cần load:</label>
                    <input type="number" name="limit" value="<?php echo $limit; ?>" min="1" max="1000000" required>
                    <small style="color: #666; display: block; margin-top: 5px;">Tối đa: 1,000,000 sản phẩm</small>
                </div>
                
                <div class="form-group">
                    <label>🔍 Từ khóa tìm kiếm (TENSP):</label>
                    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Ví dụ: Laptop Dell">
                </div>
                
                <div class="form-group">
                    <label>🎯 Tìm theo mã sản phẩm (MASP):</label>
                    <input type="text" name="search_masp" value="<?php echo htmlspecialchars($search_masp); ?>" placeholder="Ví dụ: SP0000001">
                    <small style="color: #666; display: block; margin-top: 5px;">Để trống nếu không muốn test theo MASP</small>
                </div>
                
                <button type="submit" class="btn-submit">⚡ Chạy Test Hiệu Năng</button>
            </form>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $res1 && $res2): ?>
        
        <div class="section-title">📈 KẾT QUẢ LOAD <?php echo number_format($limit); ?> SẢN PHẨM</div>
        <table>
            <tr>
                <th>Tiêu chí</th>
                <th>Chưa tối ưu (LIKE '%...%')</th>
                <th>Đã tối ưu (LIKE '...%')</th>
            </tr>
            <tr>
                <td><strong>Query thực thi</strong></td>
                <td style="font-size: 11px;">LIKE '%<?php echo htmlspecialchars($keyword); ?>%'</td>
                <td style="font-size: 11px;">LIKE '<?php echo htmlspecialchars($first_word); ?>%'</td>
            </tr>
            <tr>
                <td><strong></strong>Phương pháp truy vấn</strong></td>
                <td>Full Table Scan</td>
                <td>Index Range Scan</td>
            </tr>
            <tr>
                <td><strong>Thời gian thực thi</strong></td>
                <td class="slow"><?php echo number_format($res1['time'], 6); ?> giây</td>
                <td class="fast"><?php echo number_format($res2['time'], 6); ?> giây</td>
            </tr>
            <tr>
                <td><strong>Số lượng kết quả</strong></td>
                <td><?php echo number_format($res1['count']); ?> sản phẩm</td>
                <td><?php echo number_format($res2['count']); ?> sản phẩm</td>
            </tr>
            <tr>
                <td><strong>Hiệu quả</strong></td>
                <td>Chậm (Gây lag Server)</td>
                <td class="fast">
                    <?php 
                    if ($res2['time'] > 0) {
                        echo 'Nhanh gấp ' . number_format($res1['time'] / $res2['time'], 2) . ' lần';
                    } else {
                        echo 'Cực kỳ nhanh!';
                    }
                    ?>
                </td>
            </tr>
        </table>
        
        <?php if (count($products_noindex) > 0 || count($products_index) > 0): ?>
        <!-- Hiển thị danh sách sản phẩm đã load -->
        <div class="section-title">📋 DANH SÁCH SẢN PHẨM ĐÃ LOAD (Hiển thị tối đa 100)</div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            
            <!-- Cột 1: Không Index -->
            <div>
                <h3 style="text-align: center; color: #f44336; margin-bottom: 15px;">
                    ❌ Không Index (<?php echo count($products_noindex); ?> sản phẩm)
                </h3>
                <div style="max-height: 600px; overflow-y: auto; background: #f9f9f9; padding: 10px; border-radius: 8px;">
                    <?php if (count($products_noindex) > 0): ?>
                        <?php foreach ($products_noindex as $product): ?>
                            <div style="background: white; margin-bottom: 10px; padding: 10px; border-radius: 5px; display: flex; gap: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <img src="<?php echo htmlspecialchars($product['ANHSP']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['TENSP']); ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: bold; color: #333; font-size: 12px; margin-bottom: 3px;">
                                        <?php echo htmlspecialchars($product['MASP']); ?>
                                    </div>
                                    <div style="font-size: 13px; color: #666; margin-bottom: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($product['TENSP']); ?>
                                    </div>
                                    <div style="color: #f44336; font-weight: bold; font-size: 14px;">
                                        <?php echo number_format($product['GIATHANH'], 0, ',', '.'); ?> ₫
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999; padding: 20px;">Không có dữ liệu</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Cột 2: Có Index -->
            <div>
                <h3 style="text-align: center; color: #4CAF50; margin-bottom: 15px;">
                    ✅ Có Index (<?php echo count($products_index); ?> sản phẩm)
                </h3>
                <div style="max-height: 600px; overflow-y: auto; background: #f9f9f9; padding: 10px; border-radius: 8px;">
                    <?php if (count($products_index) > 0): ?>
                        <?php foreach ($products_index as $product): ?>
                            <div style="background: white; margin-bottom: 10px; padding: 10px; border-radius: 5px; display: flex; gap: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <img src="<?php echo htmlspecialchars($product['ANHSP']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['TENSP']); ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: bold; color: #333; font-size: 12px; margin-bottom: 3px;">
                                        <?php echo htmlspecialchars($product['MASP']); ?>
                                    </div>
                                    <div style="font-size: 13px; color: #666; margin-bottom: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($product['TENSP']); ?>
                                    </div>
                                    <div style="color: #f44336; font-weight: bold; font-size: 14px;">
                                        <?php echo number_format($product['GIATHANH'], 0, ',', '.'); ?> ₫
                                    </div>
                                </div>
            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999; padding: 20px;">Không có dữ liệu</p>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
        
        <?php if ($limit > 10000): ?>
        <p style="text-align: center; background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; color: #856404;">
            ⚠️ <strong>Chú ý:</strong> Bạn đang test với <?php echo number_format($limit); ?> sản phẩm. 
            Để tiết kiệm bộ nhớ, danh sách sản phẩm chỉ hiển thị khi số lượng ≤ 10,000.
            <br>Thời gian load vẫn được tính chính xác!
        </p>
        <?php else: ?>
        <p style="text-align: center; margin-top: 15px; color: #666; font-size: 14px;">
            <em>💡 Scroll để xem toàn bộ danh sách sản phẩm</em>
        </p>
        <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($search_masp && $res3): ?>
        <div class="section-title">🎯 KẾT QUẢ TÌM KIẾM THEO MÃ SẢN PHẨM: <?php echo htmlspecialchars($search_masp); ?></div>
        
        <?php if ($product_found): ?>
        <!-- Thông tin sản phẩm chi tiết -->
        <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #f44336; margin-bottom: 15px;">📦 THÔNG TIN SẢN PHẨM</h3>
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 15px;">
                <div style="text-align: center;">
                    <img src="<?php echo htmlspecialchars($product_found['ANHSP']); ?>" 
                         alt="<?php echo htmlspecialchars($product_found['TENSP']); ?>" 
                         style="max-width: 100%; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                </div>
                <div>
                    <table style="width: 100%; margin: 0;">
                        <tr>
                            <td style="padding: 8px; background: #fff; font-weight: bold; width: 150px;">Mã SP:</td>
                            <td style="padding: 8px; background: #fff;"><?php echo htmlspecialchars($product_found['MASP']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background: #f5f5f5; font-weight: bold;">Tên sản phẩm:</td>
                            <td style="padding: 8px; background: #f5f5f5;"><?php echo htmlspecialchars($product_found['TENSP']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background: #fff; font-weight: bold;">Giá thành:</td>
                            <td style="padding: 8px; background: #fff; color: #f44336; font-size: 18px; font-weight: bold;">
                                <?php echo number_format($product_found['GIATHANH'], 0, ',', '.'); ?> ₫
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background: #f5f5f5; font-weight: bold;">Mã loại:</td>
                            <td style="padding: 8px; background: #f5f5f5;"><?php echo htmlspecialchars($product_found['MALOAI']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background: #fff; font-weight: bold;">Mã hãng:</td>
                            <td style="padding: 8px; background: #fff;"><?php echo htmlspecialchars($product_found['MAHANG']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; background: #f5f5f5; font-weight: bold; vertical-align: top;">Chi tiết:</td>
                            <td style="padding: 8px; background: #f5f5f5;"><?php echo nl2br(htmlspecialchars($product_found['ChiTiet'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Bảng so sánh hiệu năng -->
        <table>
            <tr>
                <th>Tiêu chí</th>
                <th>Không Index trên MASP</th>
                <th>Có Index trên MASP</th>
            </tr>
            <tr>
                <td><strong>Thời gian thực thi</strong></td>
                <td class="slow"><?php echo number_format($res3['time'], 6); ?> giây</td>
                <td class="fast"><?php echo number_format($res4['time'], 6); ?> giây</td>
            </tr>
            <tr>
                <td><strong>Số lượng kết quả</strong></td>
                <td><?php echo $res3['count']; ?> sản phẩm</td>
                <td><?php echo $res4['count']; ?> sản phẩm</td>
            </tr>
            <tr>
                <td><strong>Ghi chú</strong></td>
                <td colspan="2">
                    <?php if ($res3['count'] > 0): ?>
                        ✅ Tìm thấy sản phẩm! 
                        <?php if ($res3['time'] > $res4['time']): ?>
                            Có Index nhanh hơn <?php echo number_format($res3['time'] / $res4['time'], 2); ?> lần
                        <?php endif; ?>
                    <?php else: ?>
                        ❌ Không tìm thấy sản phẩm với mã này
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <p style="text-align: center; margin-top: 20px;"><em>💡 Để tạo Index trên MASP: CREATE INDEX idx_masp ON san_pham(MASP);</em></p>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 30px; color: #666;">
            <em>📌 Lưu ý: Tạo Index để tăng hiệu năng: CREATE INDEX idx_tensp ON san_pham(TENSP(50));</em>
        </p>
        
        <?php else: ?>
        <p style="text-align: center; color: #999; padding: 40px;">👆 Vui lòng nhập thông tin và nhấn "Chạy Test Hiệu Năng" để xem kết quả</p>
        <?php endif; ?>
    </div>
</body>
</html>