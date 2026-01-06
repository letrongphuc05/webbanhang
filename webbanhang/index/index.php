<?php
session_start();
include('connect.php'); 

// Phân trang & Lọc
$limit = 12; 
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $limit;

// BẮT CÁC THÔNG SỐ LỌC
$category_id = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$brand_id = isset($_GET['brand']) ? $_GET['brand'] : ''; 

$where_clause = "WHERE 1=1";

if ($category_id) { $where_clause .= " AND MALOAI = '$category_id'"; }
if ($brand_id) { $where_clause .= " AND MAHANG = '$brand_id'"; }
if ($search) { $where_clause .= " AND TENSP LIKE '%$search%'"; }

$total_sql = "SELECT COUNT(*) FROM san_pham $where_clause";
$total_result = mysqli_query($connect, $total_sql);
$total_row = mysqli_fetch_array($total_result);
$total_products = $total_row[0];
$totalPages = ceil($total_products / $limit);

$sql = "SELECT * FROM san_pham $where_clause LIMIT $limit OFFSET $offset";
$result = mysqli_query($connect, $sql);

// Hàm Helper: Đảm bảo tất cả các filter được giữ lại
function getPaginationQueryString($page, $category_id, $search, $brand_id) {
    $q = "?page=" . $page;
    if ($category_id) { $q .= "&category=" . urlencode($category_id); }
    if ($search) { $q .= "&search=" . urlencode($search); }
    if ($brand_id) { $q .= "&brand=" . urlencode($brand_id); }
    return $q;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - LaptopDz</title>
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="interface.css?v=<?php echo time(); ?>"> 
</head>
<body>

    <nav>
        <div class="container nav-content">
            
            <a href="index.php" class="logo">
                <i class="fa-solid fa-laptop-code"></i> LaptopDz
            </a>
            
            <div class="search-wrapper">
                <form action="index.php" method="GET" class="search-box-form">
                    <input type="text" name="search" placeholder="Bạn cần tìm gì?" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <?php if ($category_id): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_id); ?>">
                    <?php endif; ?>
                    <?php if ($brand_id): ?>
                        <input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand_id); ?>">
                    <?php endif; ?>
                </form>
            </div>

            <ul class="header-icons">
                <li>
                    <a href="tel:1900 3636"><i class="fa-solid fa-headphones"></i> <span>Hotline</span> <span style="font-weight: bold; font-size: 14px;">1900 3636</span></a>
                </li>
                
                <li>
                    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> <span>Giỏ hàng</span></a>
                </li>

                <?php if(isset($_SESSION['user'])): ?>
                    <li>
                        <a href="#" style="cursor: default;">
                            <i class="fa-solid fa-user-check"></i> 
                            <span><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="../login/logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i> 
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="../login/login.php">
                            <i class="fa-solid fa-user"></i> 
                            <span>Đăng nhập</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </nav>

    <section class="menu-bar">
        <div class="container">
            <div class="menu-bar-content">
                <ul>
                    <?php
                    $sql_loai = "SELECT * FROM loai_san_pham";
                    $result_loai = mysqli_query($connect, $sql_loai);
                    while ($row_loai = mysqli_fetch_assoc($result_loai)) {
                        $maloai = $row_loai['MALOAI'];
                    ?>
                        <li>
                            <a href="<?php echo getPaginationQueryString(1, $maloai, $search, ''); ?>">
                                <i class="fa-solid fa-laptop"></i> <?php echo $row_loai['TENLOAI']; ?>
                                <i class="fa-solid fa-caret-down" style="margin-left: 5px; font-size: 10px;"></i>
                            </a>
                            <div class="submenu">
                                <ul>
                                    <?php
                                    $sql_hang = "SELECT * FROM hang WHERE loai = '$maloai'";
                                    $result_hang = mysqli_query($connect, $sql_hang);
                                    while ($row_hang = mysqli_fetch_assoc($result_hang)) {
                                        echo '<li><a href="' . getPaginationQueryString(1, $maloai, $search, $row_hang['MAHANG']) . '">' . $row_hang['TENHANG'] . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </section>

    <h1>🔥 Sản Phẩm Mới Nhất 🔥</h1>
    
    <?php if ($category_id || $brand_id || $search): ?>
        <div class="container" style="text-align:center; margin-bottom: 20px;">
            <a href="index.php" style="color: #cd1818; text-decoration: none; border: 1px solid #cd1818; padding: 5px 15px; border-radius: 5px;">
                ❌ Xóa bộ lọc hiện tại
            </a>
        </div>
    <?php endif; ?>

    <div class="product">
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($product = mysqli_fetch_assoc($result)) { 
        ?>
            <div class="product-card" onclick="window.location.href='detail.php?id=<?php echo $product['MASP']; ?>';">
                <a href="detail.php?id=<?php echo $product['MASP']; ?>">
                    <img src="<?php echo htmlspecialchars($product['ANHSP']); ?>" alt="<?php echo htmlspecialchars($product['TENSP']); ?>">
                </a>
                
                <div class="product-name">
                    <a href="detail.php?id=<?php echo $product['MASP']; ?>">
                        <?php echo htmlspecialchars($product['TENSP']); ?>
                    </a>
                </div>
                
                <p class="product-price"><?php echo number_format($product['GIATHANH'], 0, ',', '.'); ?> ₫</p>
                
                <a class="detail-link" href="detail.php?id=<?php echo $product['MASP']; ?>">Xem chi tiết</a>
            </div>
        <?php 
            } 
        } else {
            echo '<p style="text-align:center; width:100%; grid-column: 1/-1;">Không tìm thấy sản phẩm nào!</p>';
        }
        ?>
    </div>

    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="<?php echo getPaginationQueryString($currentPage - 1, $category_id, $search, $brand_id); ?>">&laquo; Trước</a>
        <?php endif; ?>

        <span class="page-current">Trang <?php echo $currentPage; ?> / <?php echo $totalPages; ?></span>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?php echo getPaginationQueryString($currentPage + 1, $category_id, $search, $brand_id); ?>">Sau &raquo;</a>
        <?php endif; ?>

        <!-- Form nhập số trang -->
        <form method="GET" action="index.php" class="goto-page-form" style="display: inline-block; margin-left: 15px;">
            <label for="goto-page">Đến trang:</label>
            <input type="number" name="page" id="goto-page" min="1" max="<?php echo $totalPages; ?>" 
                   placeholder="<?php echo $currentPage; ?>" 
                   style="width: 60px; padding: 5px; text-align: center; border: 1px solid #ddd; border-radius: 4px;">
            <button type="submit" style="padding: 5px 10px; background: #cd1818; color: white; border: none; border-radius: 4px; cursor: pointer;">Đi</button>
            
            <?php if ($category_id): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_id); ?>">
            <?php endif; ?>
            <?php if ($search): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <?php endif; ?>
            <?php if ($brand_id): ?>
                <input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand_id); ?>">
            <?php endif; ?>
        </form>
    </div>

    <script>
        // Validate input trang
        document.getElementById('goto-page').addEventListener('input', function() {
            const max = parseInt(this.max);
            const min = parseInt(this.min);
            let value = parseInt(this.value);
            
            if (value > max) this.value = max;
            if (value < min) this.value = min;
        });
    </script>

</body>
</html>