<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_login'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql_delete = "DELETE FROM san_pham WHERE MASP = '$id'";
    if (mysqli_query($conn, $sql_delete)) {
        echo "<script>alert('Đã xóa sản phẩm thành công!'); window.location.href='admin_products.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa: " . mysqli_error($conn) . "');</script>";
    }
}

$sql = "SELECT * FROM san_pham ORDER BY (MASP + 0) ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; display: flex; background-color: #f4f6f9; }
        .sidebar { width: 250px; background: #343a40; color: white; height: 100vh; position: fixed; padding-top: 20px; }
        .sidebar a { display: block; padding: 15px 20px; color: #c2c7d0; text-decoration: none; border-bottom: 1px solid #4b545c; }
        .sidebar a:hover { background: #494e53; color: white; }
        .content { margin-left: 250px; padding: 20px; width: calc(100% - 250px); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background-color: #007bff; color: white; text-align: center;}
        tr:nth-child(even) { background-color: #f9f9f9; }
        .img-product { width: 60px; height: 60px; object-fit: contain; border: 1px solid #eee; background: #fff; display: block; margin: 0 auto;}
        .detail-box { max-height: 100px; overflow-y: auto; font-size: 12px; color: #555; white-space: pre-line; background: #fff; padding: 5px; border: 1px solid #eee; }
        
        .header-page { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-action { padding: 5px 10px; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; margin-right: 5px; display: inline-block; margin-top: 2px;}
        .btn-edit { background: #ffc107; color: black; } 
        .btn-delete { background: #dc3545; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 style="text-align:center; color:#ffc107">LaptopDz</h3>
        <a href="admin_products.php" style="background: #007bff; color: white;">📦 Quản lý Sản phẩm</a>
        <a href="admin_orders.php">📝 Quản lý Đơn hàng</a>
        <a href="admin_users.php">👤 Quản lý Khách hàng</a>
        <a href="admin_logout.php" style="background: #dc3545; margin-top: 20px;">🚪 Đăng xuất</a>
    </div>

    <div class="content">
        <div class="header-page">
            <h2 style="margin: 0;">📦 Danh sách Sản phẩm</h2>
            <a href="admin_product_add.php" class="btn-add">+ Thêm mới</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="20%">Tên sản phẩm</th>
                    <th width="8%">Ảnh</th>
                    <th width="10%">Giá</th>
                    <th width="35%">Chi tiết cấu hình</th>
                    <th width="10%">Hãng/Loại</th>
                    <th width="12%">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td style="text-align:center"><b><?php echo $row['MASP']; ?></b></td>
                    <td style="font-weight:bold; color:#333"><?php echo $row['TENSP']; ?></td>
                    <td><img src="<?php echo $row['ANHSP']; ?>" class="img-product"></td>
                    <td style="color:red; font-weight:bold; text-align:right"><?php echo number_format($row['GIATHANH']); ?> đ</td>
                    <td><div class="detail-box"><?php echo $row['ChiTiet']; ?></div></td>
                    <td style="font-size: 13px; text-align:center"><?php echo $row['MAHANG']; ?> / <?php echo $row['MALOAI']; ?></td>
                    <td style="text-align:center">
                        <a href="admin_product_edit.php?id=<?php echo $row['MASP']; ?>" class="btn-action btn-edit">Sửa</a>
                        <a href="admin_products.php?delete_id=<?php echo $row['MASP']; ?>" class="btn-action btn-delete" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>