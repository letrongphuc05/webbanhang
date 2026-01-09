<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Xử lý Thêm sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($connect, $_GET['id']);
    $stmt = mysqli_prepare($connect, "SELECT MASP, TENSP, GIATHANH, ANHSP FROM san_pham WHERE MASP = ?");
    mysqli_stmt_bind_param($stmt, "s", $product_id); 
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

// Xử lý Cập nhật
if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = intval($quantity);
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: cart.php');
    exit();
}

// Xử lý Xóa
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    unset($_SESSION['cart'][$_GET['id']]);
    header('Location: cart.php');
    exit();
}

// Xử lý Xóa tất cả
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit();
}

$total = 0;
$cart_items = $_SESSION['cart'] ?? [];
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="interface.css">
    <title>Giỏ hàng - LaptopDz</title>
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 { text-align: center; color: #333; margin-bottom: 30px; }
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .cart-table th, .cart-table td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        .cart-table th { background-color: #f8f8f8; color: #333; font-weight: bold; }
        .cart-table img { width: 80px; height: 80px; object-fit: contain; }
        .cart-table input[type="number"] { width: 60px; padding: 5px; text-align: center; border: 1px solid #ccc; border-radius: 4px; }
        .cart-total strong { font-size: 1.5em; color: #cd1818; }
        .cart-buttons { display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px; }
        .cart-button { display: inline-block; padding: 10px 20px; font-weight: bold; text-decoration: none; border-radius: 5px; transition: background-color 0.2s; border: none; cursor: pointer; text-align: center; }
        .cart-buttons a:not(.remove-button), .cart-buttons button { background-color: #cd1818; color: white; border: 1px solid #cd1818; }
        .cart-buttons a:hover, .cart-buttons button:hover { background-color: #a81313; }
        .remove-button { color: #cd1818; font-size: 1.2em; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #555; text-decoration: none; font-weight: bold; }
        .btn-back:hover { color: #cd1818; }
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

    <div class="container cart-container">
        <a href="index.php" class="btn-back">← Quay lại trang sản phẩm</a>
        <h1>🛒 Giỏ hàng của bạn</h1>

        <?php if (empty($cart_items)): ?>
            <p style="text-align: center; font-size: 1.2em;">Giỏ hàng của bạn hiện đang trống!</p>
            <p style="text-align: center;"><a href="index.php" class="cart-button" style="background-color: #007bff; color: white;">Tiếp tục mua sắm</a></p>
        <?php else: ?>
            <form method="post" action="cart.php">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $product): 
                            $subtotal = $product['price'] * $product['quantity'];
                        ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"></td>
                                <td style="text-align: left; font-weight: bold;"><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $product['id']; ?>]" 
                                           value="<?php echo $product['quantity']; ?>" min="1">
                                </td>
                                <td style="color: #cd1818; font-weight: bold;"><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</td>
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

                <div class="cart-total" style="text-align: right;">
                    <strong>Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong>
                </div>

                <div class="cart-buttons">
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