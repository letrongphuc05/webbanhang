<?php
session_start();
include 'connect.php';

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = intval($_GET['order_id']);
$sql = "SELECT * FROM orders WHERE order_id = ?";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="interface.css">
    <title>Đặt hàng thành công - LaptopDz</title>
    <style>
        .thank-you-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-icon {
            color: #4CAF50;
            font-size: 80px;
            margin-bottom: 20px;
        }

        .thank-you-title {
            color: #4CAF50;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .order-info {
            margin: 10px 0;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .order-info:last-child {
            border-bottom: none;
        }

        .buttons {
            margin-top: 30px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ff9900;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #ff8800;
        }

        .order-number {
            font-size: 24px;
            color: #ff9900;
            margin: 20px 0;
        }

        .estimated-delivery {
            background-color: #fff3e0;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="no-print">
        <div class="container">
            <ul>
                <li><a href="index.php"><i class="fa-solid fa-laptop"></i>LaptopDz</a></li>
                <li><input type="text" placeholder="Search ..."><i style="color: black;" class="fa-solid fa-magnifying-glass"></i></li>
[Tin nhắn đã thu hồi]
                <li><a href="../login/login.php"><i class="fa-solid fa-user"></i></a></li>
                <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i>Giỏ Hàng</a></li>
                <li><a href="#"><i class="fa-solid fa-headphones"></i>Hotline</a></li>
            </ul>
        </div>
    </nav>

    <div class="thank-you-container">
        <i class="fas fa-check-circle success-icon"></i>
        <h1 class="thank-you-title">Cảm ơn bạn đã đặt hàng!</h1>
        
        <div class="order-number">
            Mã đơn hàng: #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?>
        </div>

        <p>Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời gian sớm nhất.</p>

        <div class="order-details">
            <div class="order-info">
                <strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['customer_name']); ?>
            </div>
            <div class="order-info">
                <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?>
            </div>
            <div class="order-info">
                <strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?>
            </div>
            <div class="order-info">
                <strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['address']); ?>
            </div>
            <div class="order-info">
                <strong>Phương thức thanh toán:</strong> 
                <?php 
                    $payment_methods = [
                        'cod' => 'Thanh toán khi nhận hàng',
                        'bank_transfer' => 'Chuyển khoản ngân hàng',
                        'momo' => 'Ví MoMo'
                    ];
                    echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                ?>
            </div>
            <div class="order-info">
                <strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ
            </div>
        </div>

        <div class="estimated-delivery">
            <i class="fas fa-truck"></i>
            <strong>Thời gian giao hàng dự kiến:</strong>
            <?php 
                $delivery_date = date('d/m/Y', strtotime('+3 days'));
                echo $delivery_date;
            ?>
        </div>

        <?php if ($order['payment_method'] == 'bank_transfer'): ?>
            <div class="order-details">
                <h3>Thông tin chuyển khoản</h3>
                <div class="order-info">
                    <strong>Ngân hàng:</strong> MBBANK
                </div>
                <div class="order-info">
                    <strong>Số tài khoản:</strong> 0915907623
</div>
                <div class="order-info">
                    <strong>Chủ tài khoản:</strong> CÔNG TY TNHH LAPTOPDZ
                </div>
                <div class="order-info">
                    <strong>Nội dung chuyển khoản:</strong> DH<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="buttons no-print">
            <a href="index.php" class="button">Tiếp tục mua sắm</a>
            <button onclick="window.print()" class="button">
                <i class="fas fa-print"></i> In đơn hàng
            </button>
        </div>

        <p class="no-print">
            Mọi thắc mắc xin vui lòng liên hệ hotline: <strong>1900 8386</strong>
        </p>
    </div>
</body>
</html>