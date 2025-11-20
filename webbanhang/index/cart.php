<?php
session_start();
include 'connect.php';
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = mysqli_prepare($connect, "SELECT MASP, TENSP, GIATHANH, ANHSP FROM san_pham WHERE MASP = ?");
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);    
    if ($product = mysqli_fetch_assoc($result)) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = array(
                'id' => $product['MASP'],
                'name' => $product['TENSP'],
                'price' => $product['GIATHANH'],
                'image' => $product['ANHSP'],
                'quantity' => 1
            );
        }
        header('Location: cart.php');
        exit();
    }
}
if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
    header('Location: cart.php');
    exit();
}
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: cart.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    $_SESSION['cart'] = array();
    header('Location: cart.php');
    exit();
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
    <title>Giỏ Hàng - LaptopDz</title>
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
        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f0ffff;
            margin-bottom: 20px;
        }

        .cart-table th, 
        .cart-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .cart-table th {
            background-color: #333;
            color: #ff9900;
        }

        .cart-table img {
            max-width: 100px;
            height: auto;
        }

        .quantity-input {
            width: 60px;
            padding: 5px;
            text-align: center;
        }

        .cart-buttons {
            margin-top: 20px;
            text-align: right;
        }

        .cart-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff9900;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        .cart-button:hover {
            background-color: #ff8800;
        }

        .cart-total {
            text-align: right;
            font-size: 1.2em;
            margin: 20px 0;
            color: #ff0000;
        }

        .empty-cart {
            text-align: center;
            padding: 40px;
            font-size: 1.2em;
            color: #666;
        }

        .remove-button {
            color: #ff0000;
            text-decoration: none;
        }

        .remove-button:hover {
            color: #cc0000;
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
                <li><a href="#"><i class="fa-solid fa-headphones"></i>Hotline</a></li>
            </ul>
        </div>
    </nav>

    <div class="cart-container">
        <h1>Giỏ Hàng</h1>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p>Giỏ hàng của bạn đang trống</p>
                <a href="index.php" class="cart-button">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <form method="post" action="cart.php">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $product): 
                            $subtotal = $product['price'] * $product['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
                                <td><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $product['id']; ?>]" 
                                           value="<?php echo $product['quantity']; ?>" 
                                           min="1" class="quantity-input">
                                </td>
                                <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <a href="cart.php?action=remove&id=<?php echo $product['id']; ?>" 
                                       class="remove-button"
                                       onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-total">
                    <strong>Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong>
                </div>

                <div class="cart-buttons">
                    <a href="index.php" class="cart-button">Tiếp tục mua sắm</a>
                    <button type="submit" name="update" class="cart-button">Cập nhật giỏ hàng</button>
                    <a href="cart.php?action=clear" class="cart-button" 
                       onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                        Xóa giỏ hàng
                    </a>
                    <a href="payment.php" class="cart-button">Thanh toán</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>