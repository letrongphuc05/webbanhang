<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? ''); 
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    
    $errors = [];
    if (empty($name)) { $errors[] = "Vui lòng nhập họ tên"; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Email không hợp lệ"; }
    if (empty($phone) || !preg_match("/^[0-9]{10}$/", $phone)) { $errors[] = "Số điện thoại không hợp lệ"; }
    if (empty($address)) { $errors[] = "Vui lòng nhập địa chỉ"; }
    if (empty($payment_method)) { $errors[] = "Vui lòng chọn phương thức thanh toán"; }
    
    if (empty($errors)) {
        $total = 0;
        foreach ($_SESSION['cart'] as $product) {
            $total += $product['price'] * $product['quantity'];
        }
        
        $sql_order = "INSERT INTO orders (customer_name, email, phone, address, total_amount, payment_method, order_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($connect, $sql_order);
        mysqli_stmt_bind_param($stmt, "ssssis", $name, $email, $phone, $address, $total, $payment_method);
        
        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($connect);
            
            // Sửa: Thay đổi cột product_id thành product_code để lưu MASP
            $sql_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($connect, $sql_detail);
            
            foreach ($_SESSION['cart'] as $product) {
                // Chuyển MASP thành product_id bằng cách extract số từ MASP
                // Nếu là số thuần (MASP cũ) thì dùng trực tiếp, nếu là TEST... thì lấy 0
                $product_id_value = is_numeric($product['id']) ? intval($product['id']) : 0;
                
                mysqli_stmt_bind_param($stmt_detail, "iiid", $order_id, $product_id_value, $product['quantity'], $product['price']);
                mysqli_stmt_execute($stmt_detail);
            }
            
            unset($_SESSION['cart']);
            header("Location: checkout.php?order_id=" . $order_id);
            exit();
        } else {
            $errors[] = "Lỗi đặt hàng: " . mysqli_error($connect);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="interface.css">
    <title>Thanh toán - LaptopDz</title>
    <style>
        body { background-color: #f4f6f9; font-family: Arial, sans-serif; }
        .payment-container { max-width: 800px; margin: 30px auto; padding: 30px; background-color: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #cd1818; margin-bottom: 25px; text-transform: uppercase; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; transition: border-color 0.3s; }
        .form-group input:focus, .form-group textarea:focus { border-color: #cd1818; outline: none; }
        .payment-methods { margin-top: 30px; border-top: 2px solid #f0f0f0; padding-top: 20px; }
        .payment-methods h2 { font-size: 18px; margin-bottom: 15px; color: #333; }
        .payment-method { margin-bottom: 12px; padding: 10px; border: 1px solid #eee; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
        .payment-method:hover { background-color: #fff5f5; border-color: #cd1818; }
        .payment-method input { margin-right: 10px; }
        .submit-button { display: block; width: 100%; padding: 15px; background-color: #cd1818; color: white; border: none; border-radius: 6px; font-size: 18px; font-weight: bold; cursor: pointer; margin-top: 30px; transition: background 0.3s; }
        .submit-button:hover { background-color: #a81313; }
        .error-message { color: #cd1818; background-color: #fff5f5; padding: 10px; border: 1px solid #ffcccc; border-radius: 4px; margin-bottom: 20px; }
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

    <div class="container">
        <div class="payment-container">
            <a href="cart.php" class="btn-back">← Quay lại giỏ hàng</a>
            
            <h1>Thanh toán đơn hàng</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="name">Họ và tên:</label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ giao hàng:</label>
                    <textarea id="address" name="address" required rows="3"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                </div>

                <div class="payment-methods">
                    <h2>Phương thức thanh toán</h2>
                    
                    <div class="payment-method">
                        <input type="radio" id="cod" name="payment_method" value="cod" 
                               <?php echo ($payment_method ?? '') === 'cod' ? 'checked' : ''; ?>>
                        <label for="cod">Thanh toán khi nhận hàng (COD)</label>
                    </div>

                    <div class="payment-method">
                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer"
                               <?php echo ($payment_method ?? '') === 'bank_transfer' ? 'checked' : ''; ?>>
                        <label for="bank_transfer">Chuyển khoản ngân hàng</label>
                    </div>

                    <div class="payment-method">
                        <input type="radio" id="momo" name="payment_method" value="momo"
                               <?php echo ($payment_method ?? '') === 'momo' ? 'checked' : ''; ?>>
                        <label for="momo">Ví MoMo</label>
                    </div>
                </div>

                <button type="submit" class="submit-button">ĐẶT HÀNG NGAY</button>
            </form>
        </div>
    </div>
</body>
</html>