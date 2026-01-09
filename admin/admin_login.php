<?php
session_start();
include 'connect.php';

if (isset($_POST['btn_login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // 1. Tìm user có quyền admin (role=1)
    $sql = "SELECT * FROM taikhoan WHERE username = ? AND role = 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $u);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // 2. Kiểm tra mật khẩu hash
        if (password_verify($p, $row['password'])) {
            $_SESSION['admin_login'] = $u;
            header("Location: admin_products.php");
            exit();
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Tài khoản không tồn tại hoặc không có quyền Admin!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập Admin</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-color: #e0e0e0; /* Nền xám nhẹ */
        }
        .login-box { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; /* Bo góc lớn */
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); /* Bóng đổ rõ nét */
            width: 350px; 
            text-align: center;
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
            background: #cd1818; /* Nút đỏ */
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold;
            margin-top: 20px;
            font-size: 16px;
        }
        button:hover { background: #a81313; }
        .error { color: #cd1818; font-size: 14px; text-align: center; }
        .link { margin-top: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Đăng nhập Admin</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Tài khoản Admin" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" name="btn_login">Đăng nhập</button>
        </form>
    </div>
</body>
</html>