<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_login'])) {
    header("Location: admin_login.php");
    exit();
}

$sql = "SELECT * FROM taikhoan WHERE role = 0 ORDER BY userid ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Khách hàng</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; display: flex; background-color: #f4f6f9; }
        .sidebar { width: 250px; background: #343a40; color: white; height: 100vh; position: fixed; padding-top: 20px; }
        .sidebar a { display: block; padding: 15px 20px; color: #c2c7d0; text-decoration: none; border-bottom: 1px solid #4b545c; }
        .sidebar a:hover { background: #494e53; color: white; }
        .content { margin-left: 250px; padding: 20px; width: calc(100% - 250px); }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #17a2b8; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .badge-user { background: #28a745; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h3 style="text-align:center; color:#ffc107">LaptopDz</h3>
        <a href="admin_products.php">📦 Quản lý Sản phẩm</a>
        <a href="admin_orders.php">📝 Quản lý Đơn hàng</a>
        <a href="admin_users.php" style="background: #17a2b8; color: white;">👤 Quản lý Khách hàng</a>
        <a href="admin_logout.php" style="background: #dc3545; margin-top:20px">🚪 Đăng xuất</a>
    </div>

    <div class="content">
        <h2>👤 Danh sách Khách hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['userid']; ?></td>
                        <td style="font-weight:bold; color:#333;"><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        <td>
                            <span class="badge-user">Khách hàng</span>
                        </td>
                    </tr>
                <?php } 
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Chưa có khách hàng nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>