<?php
session_start();
include 'connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin_login'])) { header("Location: admin_login.php"); exit(); }

// 1. Lấy ID sản phẩm cần sửa
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM san_pham WHERE MASP = '$id'";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
    
    if (!$product) {
        echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='admin_products.php';</script>";
        exit();
    }
} else {
    header("Location: admin_products.php");
    exit();
}

// 2. Lấy danh sách Hãng & Loại để hiển thị
$hang_query = mysqli_query($conn, "SELECT * FROM hang");
$loai_query = mysqli_query($conn, "SELECT * FROM loai_san_pham");

// 3. Xử lý Cập nhật
if (isset($_POST['submit'])) {
    $tensp = $_POST['tensp'];
    $gia = $_POST['gia'];
    $anhsp = $_POST['anhsp'];
    $chitiet = $_POST['chitiet'];
    $maloai = $_POST['maloai'];
    
    // XỬ LÝ HÃNG SẢN XUẤT (Logic thêm mới)
    $mahang = $_POST['mahang'];
    if ($mahang == 'other') {
        $new_hang_name = trim($_POST['new_hang']);
        if (!empty($new_hang_name)) {
            $max_hang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(CAST(MAHANG AS UNSIGNED)) as max FROM hang"));
            $new_mahang = $max_hang['max'] + 1;
            $stmt_h = mysqli_prepare($conn, "INSERT INTO hang (MAHANG, TENHANG) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_h, "ss", $new_mahang, $new_hang_name);
            if(mysqli_stmt_execute($stmt_h)) $mahang = $new_mahang;
        } else { $error = "Nhập tên hãng mới!"; }
    }

    // XỬ LÝ LOẠI SẢN PHẨM (Logic thêm mới)
    if ($maloai == 'other') {
        $new_loai_name = trim($_POST['new_loai']);
        if (!empty($new_loai_name)) {
            $max_loai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(CAST(MALOAI AS UNSIGNED)) as max FROM loai_san_pham"));
            $new_maloai = $max_loai['max'] + 1;
            $stmt_l = mysqli_prepare($conn, "INSERT INTO loai_san_pham (MALOAI, TENLOAI) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_l, "ss", $new_maloai, $new_loai_name);
            if(mysqli_stmt_execute($stmt_l)) $maloai = $new_maloai;
        } else { $error = "Nhập tên loại mới!"; }
    }

    if (!isset($error)) {
        $sql_update = "UPDATE san_pham SET 
                       TENSP = ?, MALOAI = ?, MAHANG = ?, GIATHANH = ?, ANHSP = ?, ChiTiet = ? 
                       WHERE MASP = ?";
        
        $stmt = mysqli_prepare($conn, $sql_update);
        mysqli_stmt_bind_param($stmt, "sssssss", $tensp, $maloai, $mahang, $gia, $anhsp, $chitiet, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Cập nhật thành công!'); window.location.href='admin_products.php';</script>";
        } else {
            $error = "Lỗi cập nhật: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px; }
        .form-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; resize: vertical; }
        .btn-submit { background: #ffc107; color: black; border: none; padding: 12px 20px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; border-radius: 4px; }
        .btn-submit:hover { background: #e0a800; }
        .btn-back { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #007bff; }
        .preview-img { width: 100px; margin-top: 10px; border: 1px solid #ddd; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
        .new-input { display: none; margin-top: 10px; border-color: #28a745; background-color: #f0fff4; }
    </style>
    <script>
        function toggleInput(selectObj, inputId) {
            var inputDiv = document.getElementById(inputId);
            if (selectObj.value === 'other') {
                inputDiv.style.display = 'block';
                inputDiv.required = true;
            } else {
                inputDiv.style.display = 'none';
                inputDiv.required = false;
            }
        }
    </script>
</head>
<body>

    <a href="admin_products.php" class="btn-back">← Quay lại danh sách</a>

    <div class="form-container">
        <h2>Sửa Sản Phẩm: <?php echo $product['MASP']; ?></h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="post">
            <div class="form-group">
                <label>Tên sản phẩm:</label>
                <input type="text" name="tensp" required value="<?php echo htmlspecialchars($product['TENSP']); ?>">
            </div>

            <div class="form-group">
                <label>Link hình ảnh (URL):</label>
                <input type="text" name="anhsp" required value="<?php echo htmlspecialchars($product['ANHSP']); ?>">
                <img src="<?php echo $product['ANHSP']; ?>" class="preview-img">
            </div>

            <div class="form-group">
                <label>Giá thành (VNĐ):</label>
                <input type="number" name="gia" required value="<?php echo $product['GIATHANH']; ?>"> 
            </div>

            <div class="form-group">
                <label>Hãng sản xuất:</label>
                <select name="mahang" onchange="toggleInput(this, 'new_hang_input')">
                    <?php 
                    // Phải chạy lại query vì biến $hang_query đã bị dùng hết ở phần logic PHP trên
                    $hang_query = mysqli_query($conn, "SELECT * FROM hang");
                    while($h = mysqli_fetch_assoc($hang_query)) { ?>
                        <option value="<?php echo $h['MAHANG']; ?>" <?php if($h['MAHANG'] == $product['MAHANG']) echo 'selected'; ?>>
                            <?php echo $h['TENHANG']; ?>
                        </option>
                    <?php } ?>
                    <option value="other" style="font-weight:bold; color:green;">+ Thêm hãng khác...</option>
                </select>
                <input type="text" name="new_hang" id="new_hang_input" class="new-input" placeholder="Nhập tên hãng mới vào đây...">
            </div>

            <div class="form-group">
                <label>Loại sản phẩm:</label>
                <select name="maloai" onchange="toggleInput(this, 'new_loai_input')">
                    <?php 
                    // Phải chạy lại query
                    $loai_query = mysqli_query($conn, "SELECT * FROM loai_san_pham");
                    while($l = mysqli_fetch_assoc($loai_query)) { ?>
                        <option value="<?php echo $l['MALOAI']; ?>" <?php if($l['MALOAI'] == $product['MALOAI']) echo 'selected'; ?>>
                            <?php echo $l['TENLOAI']; ?>
                        </option>
                    <?php } ?>
                    <option value="other" style="font-weight:bold; color:green;">+ Thêm loại khác...</option>
                </select>
                <input type="text" name="new_loai" id="new_loai_input" class="new-input" placeholder="Nhập tên loại mới...">
            </div>

            <div class="form-group">
                <label>Chi tiết cấu hình:</label>
                <textarea name="chitiet" required><?php echo htmlspecialchars($product['ChiTiet']); ?></textarea>
            </div>

            <button type="submit" name="submit" class="btn-submit">Cập nhật sản phẩm</button>
        </form>
    </div>

</body>
</html>