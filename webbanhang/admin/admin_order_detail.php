<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_login'])) { header("Location: admin_login.php"); exit(); }

if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);

    $sql_order = "SELECT * FROM orders WHERE order_id = $order_id";
    $query_order = mysqli_query($conn, $sql_order);
    $order = mysqli_fetch_assoc($query_order);

    if (!$order) {
        echo "<script>alert('Đơn hàng không tồn tại!'); window.location.href='admin_orders.php';</script>";
        exit();
    }

    $sql_details = "SELECT od.*, sp.TENSP, sp.ANHSP 
                    FROM order_details od 
                    JOIN san_pham sp ON od.product_id = sp.MASP 
                    WHERE od.order_id = $order_id";
    $query_details = mysqli_query($conn, $sql_details);
} else {
    header("Location: admin_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?php echo $order_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; display: flex; background-color: #f4f6f9; }
        .sidebar { width: 250px; background: #343a40; color: white; height: 100vh; position: fixed; padding-top: 20px; }
        .sidebar a { display: block; padding: 15px 20px; color: #c2c7d0; text-decoration: none; border-bottom: 1px solid #4b545c; }
        .sidebar a:hover { background: #494e53; color: white; }
        .content { margin-left: 250px; padding: 20px; width: calc(100% - 250px); }
        
        .info-box { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .info-box h3 { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 0; color: #007bff; }
        .info-row { margin-bottom: 10px; font-size: 15px; }
        .info-row strong { display: inline-block; width: 150px; }

        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
        th { background-color: #28a745; color: white; }
        .img-mini { width: 50px; height: 50px; object-fit: contain; border: 1px solid #eee; }
        
        .btn-back { display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-bottom: 15px; }
        .btn-print { background: #17a2b8; float: right; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3 style="text-align:center; color:#ffc107">LaptopDz</h3>
        <a href="admin_products.php">📦 Quản lý Sản phẩm</a>
        <a href="admin_orders.php" style="background: #28a745; color: white;">📝 Quản lý Đơn hàng</a>
        <a href="admin_users.php">👤 Quản lý Khách hàng</a>
        <a href="admin_logout.php" style="background: #dc3545; margin-top: 20px;">🚪 Đăng xuất</a>
    </div>

    <div class="content">
        <a href="admin_orders.php" class="btn-back">← Quay lại danh sách</a>
        <a href="#" onclick="window.print()" class="btn-back btn-print">🖨️ In hóa đơn</a>

        <div class="info-box">
            <h3>📋 Thông tin đơn hàng #<?php echo $order_id; ?></h3>
            <div class="info-row"><strong>Khách hàng:</strong> <?php echo $order['customer_name']; ?></div>
            <div class="info-row"><strong>Số điện thoại:</strong> <?php echo $order['phone']; ?></div>
            <div class="info-row"><strong>Email:</strong> <?php echo $order['email']; ?></div>
            <div class="info-row"><strong>Địa chỉ giao hàng:</strong> <?php echo $order['address']; ?></div>
            <div class="info-row"><strong>Ngày đặt:</strong> <?php echo date("d/m/Y H:i", strtotime($order['order_date'])); ?></div>
            <div class="info-row"><strong>Phương thức:</strong> <span style="text-transform: uppercase; font-weight: bold; color: #d35400;"><?php echo $order['payment_method']; ?></span></div>
        </div>

        <div class="info-box">
            <h3>🛒 Sản phẩm đã mua</h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%">STT</th>
                        <th width="10%">Ảnh</th>
                        <th width="40%">Tên sản phẩm</th>
                        <th width="15%">Đơn giá</th>
                        <th width="10%">Số lượng</th>
                        <th width="20%">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $stt = 1;
                    $total_check = 0;
                    while ($row = mysqli_fetch_assoc($query_details)) { 
                        $subtotal = $row['price'] * $row['quantity'];
                        $total_check += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo $stt++; ?></td>
                        <td><img src="<?php echo $row['ANHSP']; ?>" class="img-mini"></td>
                        <td><?php echo $row['TENSP']; ?></td>
                        <td><?php echo number_format($row['price']); ?> đ</td>
                        <td style="text-align: center;"><?php echo $row['quantity']; ?></td>
                        <td style="font-weight: bold; color: #d35400;"><?php echo number_format($subtotal); ?> đ</td>
                    </tr>
                    <?php } ?>
                    
                    <tr style="background-color: #fff3cd;">
                        <td colspan="5" style="text-align: right; font-weight: bold; font-size: 16px;">TỔNG THANH TOÁN:</td>
                        <td style="font-weight: bold; color: red; font-size: 18px;"><?php echo number_format($total_check); ?> đ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>