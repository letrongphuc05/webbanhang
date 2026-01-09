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
        body { background-color: #f4f6f9; font-family: Arial, sans-serif; }
        
        .thank-you-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745; 
            margin-bottom: 20px;
        }
        
        h1 {
            color: #cd1818; 
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .order-id {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
        
        .estimated-delivery {
            background-color: #fff5f5;
            border: 1px dashed #cd1818;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
            color: #333;
        }
        
        .estimated-delivery i {
            color: #cd1818;
            margin-right: 10px;
        }

        .order-details {
            text-align: left;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 5px solid #cd1818;
        }
        
        .order-details h3 {
            color: #cd1818;
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        /* CSS CHO PHẦN MÃ QR */
        .qr-section {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
        }
        .qr-section p {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .qr-image {
            width: 200px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px;
        }

        .order-info {
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .order-info strong {
            display: inline-block;
            width: 180px;
            color: #555;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        .button {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            color: white;
            background-color: #cd1818; 
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .button:hover {
            background-color: #a81313;
        }
        
        .button-secondary {
            background-color: #6c757d;
        }
        .button-secondary:hover {
            background-color: #5a6268;
        }

        @media print {
            .no-print { display: none; }
            .thank-you-container { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>

    <nav class="no-print">
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

    <div class="thank-you-container">
        <h1>Vui lòng chuyển khoản</h1>
        <div class="order-id">Mã đơn hàng: <strong>#<?php echo $order_id; ?></strong></div>
        
        <p>Cảm ơn bạn đã mua sắm tại <strong>LaptopDz</strong>. Đơn hàng của bạn đang được xử lý.</p>

        <div class="estimated-delivery">
            <i class="fa-solid fa-truck-fast"></i>
            <strong>Thời gian giao hàng dự kiến:</strong>
            <?php 
                $delivery_date = date('d/m/Y', strtotime('+3 days'));
                echo $delivery_date;
            ?>
        </div>

        <?php if ($order['payment_method'] == 'bank_transfer'): ?>
            <div class="order-details">
                <h3><i class="fa-solid fa-money-bill-transfer"></i> Thông tin chuyển khoản</h3>
                
                <div class="qr-section">
                    <p>Quét mã QR Ngân hàng:</p>
                    <img src="qr_mbbank.jpg" alt="Mã QR Thanh toán" class="qr-image">
                </div>

                <div class="order-info">
                    <strong>Ngân hàng:</strong> <span style="color:#00008B; font-weight:bold;">MB BANK</span>
                </div>
                <div class="order-info">
                    <strong>Số tài khoản:</strong> <span style="font-size:18px; font-weight:bold;">0915907623</span>
                </div>
                <div class="order-info">
                    <strong>Chủ tài khoản:</strong> NGO TRUONG TIN
                </div>
                <div class="order-info">
                    <strong>Nội dung chuyển khoản:</strong> 
                    <span style="color:#cd1818; font-weight:bold;">
                        DH<?php echo str_pad((string)$order_id, 6, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
                <p style="margin-top:10px; font-style:italic; color:red;">* Vui lòng chuyển khoản đúng nội dung để đơn hàng được xác nhận tự động.</p>
            </div>
        <?php endif; ?>
        
        <?php if ($order['payment_method'] == 'momo'): ?>
            <div class="order-details">
                <h3><i class="fa-solid fa-wallet"></i> Thông tin Ví MoMo</h3>
                
                <div class="qr-section">
                    <p>Quét mã để thanh toán MoMo:</p>
                    <img src="qr_momo.jpg" alt="Mã QR MoMo" class="qr-image">
                </div>

                <div class="order-info">
                    <strong>Số điện thoại:</strong> 0915907623
                </div>
                <div class="order-info">
                    <strong>Chủ tài khoản:</strong> NGO TRUONG TIN
                </div>
                <div class="order-info">
                    <strong>Nội dung:</strong> 
                    <span style="color:#cd1818; font-weight:bold;">
                        DH<?php echo str_pad((string)$order_id, 6, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <div class="buttons no-print">
            <a href="index.php" class="button">Tiếp tục mua sắm</a>
            <button onclick="window.print()" class="button button-secondary">
                <i class="fa-solid fa-print"></i> In đơn hàng
            </button>
        </div>

        <p class="no-print" style="margin-top: 30px; color: #777; font-size: 14px;">
            Mọi thắc mắc xin vui lòng liên hệ Hotline: <strong>1900 3636</strong>
        </p>
    </div>

</body>
</html>