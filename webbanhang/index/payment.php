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
    
    if (empty($name)) {
        $errors[] = "Vui lòng nhập họ tên";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    if (empty($phone) || !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Số điện thoại không hợp lệ";
    }
    if (empty($address)) {
        $errors[] = "Vui lòng nhập địa chỉ";
    }
    if (empty($payment_method)) {
        $errors[] = "Vui lòng chọn phương thức thanh toán";
    }
    
    if (empty($errors)) {
        $total = 0;
        foreach ($_SESSION['cart'] as $product) {
            $total += $product['price'] * $product['quantity'];
        }
        
        $sql = "INSERT INTO orders (customer_name, email, phone, address, total_amount, payment_method, order_date) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
                
        $stmt = mysqli_prepare($connect, $sql);
        mysqli_stmt_bind_param($stmt, "ssssds", $name, $email, $phone, $address, $total, $payment_method);
        
        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($connect);
            $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $sql);
            
            foreach ($_SESSION['cart'] as $product) {
                mysqli_stmt_bind_param($stmt, "iiid", $order_id, $product['id'], $product['quantity'], $product['price']);
                mysqli_stmt_execute($stmt);
            }
            
            unset($_SESSION['cart']); 
            header('Location: checkout.php?order_id=' . $order_id);
            exit();
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
    <title>Thanh Toán - LaptopDz</title>
    <style>
        .payment-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .payment-methods {
            margin: 20px 0;
        }

        .payment-method {
            margin: 10px 0;
        }

        .order-summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .submit-button {
            background-color: #ff9900;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .submit-button:hover {
            background-color: #ff8800;
        }

        .error {
            color: red;
            margin-bottom: 20px;
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
                <li><a href="#"><i class="fa-solid fa-headphones"></i>Hotline</a></li>
            </ul>
        </div>
    </nav>

    <div class="payment-container">
        <h1>Thanh Toán</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="order-summary">
            <h2>Đơn hàng của bạn</h2>
            <?php 
            $total = 0;
            foreach ($_SESSION['cart'] as $product): 
                $subtotal = $product['price'] * $product['quantity'];
                $total += $subtotal;
            ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($product['name']); ?> x <?php echo $product['quantity']; ?></span>
                    <span><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</span>
                </div>
            <?php endforeach; ?>
            <div class="order-item" style="font-weight: bold;">
                <span>Tổng cộng:</span>
                <span><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span>
            </div>
        </div>

        <form method="post" action="payment.php">
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

            <button type="submit" class="submit-button">Đặt hàng</button>
        </form>
    </div>
</body>
</html>