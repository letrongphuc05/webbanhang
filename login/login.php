<?php
session_start();
include 'connect.php'; // Gọi file connect.php vừa tạo ở Bước 1

$error = ''; 

if (isset($_POST['btn_login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // Sử dụng Prepared Statement để bảo mật
    $sql = "SELECT * FROM taikhoan WHERE username = ?";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, "s", $u);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Kiểm tra mật khẩu
        if (password_verify($p, $row['password'])) {
            $_SESSION['user'] = $u; // Lưu session
            header("Location: ../index/index.php"); // Chuyển hướng về trang chủ (lùi lại 1 cấp thư mục rồi vào index)
            exit();
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập Khách hàng</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-color: #FFFFFF; /* Nền trắng tinh */
            margin: 0;
        }
        .login-box { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.15); 
            width: 350px; 
            text-align: center;
            border: 1px solid #f0f0f0;
        }
        h2 { text-align: center; margin-bottom: 25px; color: #333; }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            box-sizing: border-box; 
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #cd1818; /* Nút màu đỏ */
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold;
            margin-top: 20px;
            font-size: 16px;
        }
        button:hover { background: #a81313; }
        .error { color: #cd1818; font-size: 14px; text-align: center; display: block; margin-bottom: 10px; }
        .link { margin-top: 20px; font-size: 14px; }
        .link a { color: #333; text-decoration: none; }
        .link a:hover { color: #cd1818; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Đăng Nhập</h2>
        <?php if(!empty($error)) echo "<span class='error'>$error</span>"; ?>
        
        <form method="post">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" name="btn_login">Đăng nhập</button>
        </form>
        
        <div class="link">
            <a href="active_register.php">Chưa có tài khoản? Đăng ký ngay!</a>
        </div>
    </div>
</body>
</html>