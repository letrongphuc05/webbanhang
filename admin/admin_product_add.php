<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['admin_login'])) { header("Location: admin_login.php"); exit(); }

$hang_query = mysqli_query($conn, "SELECT * FROM hang");
$loai_query = mysqli_query($conn, "SELECT * FROM loai_san_pham");

if (isset($_POST['submit'])) {
    $masp = $_POST['masp'];
    $tensp = $_POST['tensp'];
    $gia = $_POST['gia'];
    $anhsp = $_POST['anhsp'];
    $chitiet = $_POST['chitiet'];
    
    $mahang = $_POST['mahang'];
    if ($mahang == 'other') {
        $new_hang_name = trim($_POST['new_hang']);
        if (!empty($new_hang_name)) {
            $max_id_query = mysqli_query($conn, "SELECT MAX(CAST(MAHANG AS UNSIGNED)) as max_id FROM hang");
            $row = mysqli_fetch_assoc($max_id_query);
            $new_mahang = $row['max_id'] + 1;

            $insert_hang = mysqli_prepare($conn, "INSERT INTO hang (MAHANG, TENHANG) VALUES (?, ?)");
            mysqli_stmt_bind_param($insert_hang, "ss", $new_mahang, $new_hang_name);
            mysqli_stmt_execute($insert_hang);
            
            $mahang = $new_mahang;
        } else {
            $error = "Vui lòng nhập tên Hãng mới!";
        }
    }

    $maloai = $_POST['maloai'];
    if ($maloai == 'other') {
        $new_loai_name = trim($_POST['new_loai']);
        if (!empty($new_loai_name)) {
            $max_id_loai = mysqli_query($conn, "SELECT MAX(CAST(MALOAI AS UNSIGNED)) as max_id FROM loai_san_pham");
            $row_loai = mysqli_fetch_assoc($max_id_loai);
            $new_maloai = $row_loai['max_id'] + 1;

            $insert_loai = mysqli_prepare($conn, "INSERT INTO loai_san_pham (MALOAI, TENLOAI) VALUES (?, ?)");
            mysqli_stmt_bind_param($insert_loai, "ss", $new_maloai, $new_loai_name);
            mysqli_stmt_execute($insert_loai);
            
            $maloai = $new_maloai;
        } else {
            $error = "Vui lòng nhập tên Loại sản phẩm mới!";
        }
    }

    if (!isset($error)) {
        $check = mysqli_query($conn, "SELECT * FROM san_pham WHERE MASP = '$masp'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Mã sản phẩm đã tồn tại!";
        } else {
            $sql = "INSERT INTO san_pham (MASP, TENSP, MALOAI, MAHANG, GIATHANH, ANHSP, ChiTiet) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssss", $masp, $tensp, $maloai, $mahang, $gia, $anhsp, $chitiet);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='admin_products.php';</script>";
            } else {
                $error = "Lỗi thêm: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px; }
        .form-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; resize: vertical; }
        .btn-submit { background: #28a745; color: white; border: none; padding: 12px 20px; cursor: pointer; width: 100%; font-size: 16px; border-radius: 4px; }
        .btn-submit:hover { background: #218838; }
        .btn-back { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #007bff; }
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
        <h2>Thêm Sản Phẩm Mới</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="post">
            <div class="form-group">
                <label>Mã sản phẩm (ID):</label>
                <input type="text" name="masp" required placeholder="VD: 100">
            </div>
            
            <div class="form-group">
                <label>Tên sản phẩm:</label>
                <input type="text" name="tensp" required placeholder="VD: Laptop Dell XPS 13">
            </div>

            <div class="form-group">
                <label>Link hình ảnh (URL):</label>
                <input type="text" name="anhsp" required placeholder="https://example.com/image.jpg">
            </div>

            <div class="form-group">
                <label>Giá thành (VNĐ):</label>
                <input type="number" name="gia" required placeholder="VD: 15000000">
            </div>

            <div class="form-group">
                <label>Hãng sản xuất:</label>
                <select name="mahang" onchange="toggleInput(this, 'new_hang_input')">
                    <?php while($h = mysqli_fetch_assoc($hang_query)) { ?>
                        <option value="<?php echo $h['MAHANG']; ?>"><?php echo $h['TENHANG']; ?></option>
                    <?php } ?>
                    <option value="other" style="font-weight:bold; color:green;">+ Thêm hãng khác...</option>
                </select>
                <input type="text" name="new_hang" id="new_hang_input" class="new-input" placeholder="Nhập tên hãng mới...">
            </div>

            <div class="form-group">
                <label>Loại sản phẩm:</label>
                <select name="maloai" onchange="toggleInput(this, 'new_loai_input')">
                    <?php while($l = mysqli_fetch_assoc($loai_query)) { ?>
                        <option value="<?php echo $l['MALOAI']; ?>"><?php echo $l['TENLOAI']; ?></option>
                    <?php } ?>
                    <option value="other" style="font-weight:bold; color:green;">+ Thêm loại khác...</option>
                </select>
                <input type="text" name="new_loai" id="new_loai_input" class="new-input" placeholder="Nhập tên loại sản phẩm mới...">
            </div>

            <div class="form-group">
                <label>Chi tiết cấu hình:</label>
                <textarea name="chitiet" required placeholder="CPU: i5...&#10;RAM: 8GB..."></textarea>
            </div>

            <button type="submit" name="submit" class="btn-submit">Thêm Sản Phẩm</button>
        </form>
    </div>

</body>
</html>