<?php  
session_start();
include 'connect.php';  

$id = isset($_GET['id']) ? mysqli_real_escape_string($connect, $_GET['id']) : 0; 
$sql = "SELECT * FROM san_pham WHERE MASP = ?";  
$stmt = mysqli_prepare($connect, $sql);  
mysqli_stmt_bind_param($stmt, "s", $id); 
mysqli_stmt_execute($stmt);  
$result = mysqli_stmt_get_result($stmt);  

if ($result && mysqli_num_rows($result) > 0) {  
    $product = mysqli_fetch_assoc($result);  
} else {  
    echo "Sản phẩm không tồn tại.";  
    exit;  
}  
?>  

<!DOCTYPE html>  
<html lang="vi">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.min.css">  
    <link rel="stylesheet" href="interface.css">  
    <title><?php echo htmlspecialchars($product['TENSP']); ?> - LaptopDz</title>  
    <style> 
        /* === CẤU TRÚC KHUNG CHI TIẾT === */
        .product-detail {  
            max-width: 1200px;  
            margin: 30px auto;  
            padding: 40px; 
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }  
        
        .product-card-detail {
            display: flex;
            gap: 50px; 
            align-items: flex-start;
        }

        .product-image {
            width: 45%; 
            height: auto;
            max-height: 500px;
            object-fit: contain;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 10px;
        }

        .product-info {
            width: 55%;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 30px; 
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 3px solid #f0f0f0; 
            padding-bottom: 15px;
            line-height: 1.4; 
        }

        .product-price {
            font-size: 34px; 
            font-weight: bold;
            color: #cd1818; 
            margin-bottom: 30px;
        }
        
        .add-to-cart {
            background-color: #cd1818; 
            color: white; 
            display: inline-block;
            padding: 15px 40px; 
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            white-space: nowrap; 
            box-shadow: 0 4px 10px rgba(205, 24, 24, 0.3);
        }
        .add-to-cart:hover {
            background-color: #a81313;
            transform: translateY(-2px);
        }

        .btn-back-to-list {
            background-color: #f5f5f5;
            color: #333;
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
            font-weight: 600;
            border: 1px solid #ddd;
            white-space: nowrap; 
        }
        .btn-back-to-list:hover {
            background-color: #e0e0e0;
            color: #000;
        }

        .product-description {
            margin-bottom: 25px;
            padding: 20px;
            border: 1px solid #eee; 
            border-radius: 8px;
            background: #fafafa;
        }
        .product-description h3 {
            color: #333; 
            margin-top: 0;
            font-size: 18px;
            border-left: 4px solid #cd1818;
            padding-left: 12px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .spec-list {
            list-style-type: none; 
            padding: 0;
        }
        .spec-list li {
            padding: 8px 0;
            border-bottom: 1px dashed #e0e0e0;
            display: flex;
            line-height: 1.5;
        }
        .spec-list li:before {
            content: "•";
            color: #cd1818;
            font-weight: bold;
            margin-right: 10px;
        }
        .spec-list li:last-child {
            border-bottom: none;
        }

        .actions-footer {
            margin-top: 30px;
            text-align: right; 
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .product-card-detail { flex-direction: column; }
            .product-image, .product-info { width: 100%; }
            .product-image { max-height: 300px; }
        }
    </style> 
</head>  
<body>  
    
    <nav>
        <div class="container nav-content">
            <a href="index.php" class="logo"><i class="fa-solid fa-laptop-code"></i> LaptopDz</a>
            
            <div class="search-wrapper">
                <form action="index.php" method="GET" class="search-box-form">
                    <input type="text" name="search" placeholder="Bạn cần tìm gì?">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <ul class="header-icons">
                <li>
                    <a href="tel:1900 3636"><i class="fa-solid fa-headphones"></i> <span>Hotline</span> <span style="font-weight: bold; font-size: 14px;">1900 3636</span></a>
                </li>
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> <span>Giỏ hàng</span></a></li>
                <?php if(isset($_SESSION['user'])): ?>
                    <li><a href="#"><i class="fa-solid fa-user-check"></i> <span><?php echo htmlspecialchars($_SESSION['user']); ?></span></a></li>
                    <li><a href="../login/logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span>Thoát</span></a></li>
                <?php else: ?>
                    <li><a href="../login/login.php"><i class="fa-solid fa-user"></i> <span>Đăng nhập</span></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div style="margin-top: 20px;">
            <a href="index.php" class="btn-back-to-list">
                <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ
            </a>
        </div>

        <div class="product-detail">  
            <div class="product-card-detail">  
                
                <img src="<?php echo htmlspecialchars($product['ANHSP']); ?>"   
                     alt="<?php echo htmlspecialchars($product['TENSP']); ?>"   
                     class="product-image"> 

                <div class="product-info">  
                    <div>
                        <h1 class="product-name"><?php echo htmlspecialchars($product['TENSP']); ?></h1>  
                        <p class="product-price"><?php echo number_format($product['GIATHANH'], 0, ',', '.'); ?> VNĐ</p>  
                    </div>

                    <div> 
                        <div class="product-description">  
                            <h3>Mô tả sản phẩm</h3>  
                            <div style="line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($product['ChiTiet'])); ?>  
                            </div>
                        </div>  

                        <?php if (!empty($product['ThongSoKT'])): ?>  
                        <div class="product-description">  
                            <h3>Thông số kỹ thuật</h3>  
                            <ul class="spec-list">
                                <?php 
                                $specs = explode("\n", htmlspecialchars($product['ThongSoKT']));
                                foreach ($specs as $spec) {
                                    if (trim($spec)) {
                                        echo '<li>' . trim($spec) . '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>  
                        <?php endif; ?>  
                    </div>

                    <div class="actions-footer">
                        <a href="cart.php?action=add&id=<?php echo $product['MASP']; ?>" class="add-to-cart">  
                            <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>