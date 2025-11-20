<?php  
include 'connect.php';  
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;   
$sql = "SELECT * FROM san_pham WHERE MASP = ?";  
$stmt = mysqli_prepare($connect, $sql);  
mysqli_stmt_bind_param($stmt, "i", $id);  
mysqli_stmt_execute($stmt);  
$result = mysqli_stmt_get_result($stmt);  

if ($result && mysqli_num_rows($result) > 0) {  
    $product = mysqli_fetch_assoc($result);  
} else {  
    echo "Sản phẩm không tồn tại.";  
    exit;  
}  

mysqli_close($connect);  
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
        .product-detail {  
            max-width: 1200px;  
            margin: 20px auto;  
            padding: 20px;  
        }  
        .product-card-detail {  
            border: 2px solid #ccc;  
            border-radius: 8px;  
            padding: 10px;  
            background-color: #f0ffff;  
            text-align: center;  
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);  
            min-height: 1000px;
            max-height: 1500px;
            width: 100%;
        }
        
        .product-image {  
            width: 500px;        
            height: 500px;   
        }  

        .product-info {  
            text-align: left;  
            margin-top: 20px;  
        }  

        .product-description {  
            color: #333;  
            line-height: 1.6;  
            margin: 20px 0;  
            text-align: justify;  
        }  

        .add-to-cart {  
            display: inline-block;  
            background-color: #ff9900;  
            color: white;  
            padding: 10px 20px;  
            border-radius: 5px;  
            margin-top: 20px;  
        }  

        .add-to-cart:hover {  
            background-color: #ff8800;  
        }  

        nav {  
            width: 100%;   
            background-color: #000;  
            position: relative;  
        }  

        nav ul {  
            display: flex;  
            justify-content: space-between;   
            align-items: center;   
            height: 60px;  
            margin: 0;   
            padding: 0 20px;  
            list-style-type: none;  
        }  
    </style>  
</head>  
<body>  
    <nav>  
        <div class="container">  
            <ul>  
                <li><a href="index.php"><i class="fa-solid fa-laptop"></i>LaptopDz</a></li>  
                <li><input type="text" placeholder="Search ..."><i style="color: black;" class="fa-solid fa-magnifying-glass"></i></li>  
                <li><a href="../login/login.php"><i class="fa-solid fa-user"></i>Đăng xuất</a></li>  
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i>Giỏ Hàng</a></li>  
                <li><a href=""><i class="fa-solid fa-headphones"></i>Hotline:</a></li>  
            </ul>  
        </div>  
    </nav>  
    <div class="product-detail">  
        <div class="product-card-detail">  
            <p class="product-name"><?php echo htmlspecialchars($product['TENSP']); ?></p>  
            <img src="<?php echo htmlspecialchars($product['ANHSP']); ?>"   
                 alt="<?php echo htmlspecialchars($product['TENSP']); ?>"   
                 class="product-image"> 

            <div class="product-info">  
                <p class="product-price">Giá: <?php echo number_format($product['GIATHANH'], 0, ',', '.'); ?> VNĐ</p>  
                
                <div class="product-description">  
                    <h3>Mô tả sản phẩm:</h3>  
                    <?php echo nl2br(htmlspecialchars($product['ChiTiet'])); ?>  
                </div>  

                <?php if (!empty($product['ThongSoKT'])): ?>  
                <div class="product-description">  
                    <h3>Thông số kỹ thuật:</h3>  
                    <?php echo nl2br(htmlspecialchars($product['ThongSoKT'])); ?>  
                </div>  
                <?php endif; ?>  

                <a href="cart.php?action=add&id=<?php echo $product['MASP']; ?>" class="add-to-cart">  
                    Thêm vào giỏ hàng  
                </a>  
            </div>  
        </div>  
    </div>  
</body>  
</html> 