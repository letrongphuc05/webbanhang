<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_login'])) { header("Location: admin_login.php"); exit(); }

$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; display: flex; background-color: #f4f6f9; }
        .sidebar { width: 250px; background: #343a40; color: white; height: 100vh; position: fixed; padding-top: 20px; }
        .sidebar a { display: block; padding: 15px 20px; color: #c2c7d0; text-decoration: none; border-bottom: 1px solid #4b545c; }
        .sidebar a:hover { background: #494e53; color: white; }
        .content { margin-left: 250px; padding: 20px; width: calc(100% - 250px); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: middle; }
        th { background-color: #28a745; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        .btn-view { background-color: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; display: inline-block; }
        .btn-view:hover { background-color: #0056b3; }
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
        <h2>📝 Danh sách Đơn hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>SĐT</th>
                    <th>Tổng tiền</th>
                    <th>Ngày đặt</th>
                    <th>Thanh toán</th>
                    <th>Hành động</th> </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td>#<?php echo $row['order_id']; ?></td>
                    <td style="font-weight:bold;"><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td style="color:red; font-weight:bold;"><?php echo number_format($row['total_amount']); ?> đ</td>
                    <td><?php echo date("d/m/Y H:i", strtotime($row['order_date'])); ?></td>
                    <td style="text-transform: uppercase;"><?php echo $row['payment_method']; ?></td>
                    <td>
                        <a href="admin_order_detail.php?id=<?php echo $row['order_id']; ?>" class="btn-view">
                            <i class="fa fa-eye"></i> Xem chi tiết
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>