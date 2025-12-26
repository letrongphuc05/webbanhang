<?php   
if (isset($_POST['submit'])) {   
    // Đảm bảo kết nối CSDL được thiết lập, dùng $connect như trong connect.php
    $servername = "localhost";  
    $username = "root";  
    $password = ""; 
    $database = "webbanhang";  

    $connect = mysqli_connect($servername, $username, $password, $database);  
    
    if (!$connect) {  
        die("Kết nối thất bại: " . mysqli_connect_error());  
    }  

    $loginname = $_POST["loginname"];  
    $phone = $_POST["phone"];  
    $email = $_POST["email"];  
   
    $password = $_POST["password"];  
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);   
    
    // Thêm người dùng (role mặc định 0 nếu có cột role)
    $success_insert = $connect->query("INSERT INTO taikhoan (`username`, `phone`, `email`, `password`) 
                                      VALUES ('$loginname', '$phone', '$email', '$hashed_password')");  
    
    if ($success_insert) {  
        echo ("<script>alert('Đăng ký thành công');</script>");
        header("Location: login.php");  
    } else {  
        echo ("<script>alert('Đăng ký thất bại');</script>");  
    }  

    $connect->close();  
}  
?>  

<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký Tài khoản</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-color: #FFFFFF; /* NỀN TRẮNG TINH */
        }
        .register-box { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
            width: 380px; 
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
            background: #28a745; /* Nút XANH lá cho Đăng ký */
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold;
            margin-top: 20px;
            font-size: 16px;
        }
        button:hover { background: #1e7e34; }
        .error { color: #cd1818; font-size: 14px; text-align: center; }
        .link { margin-top: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>Đăng Ký Tài Khoản</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="loginname" placeholder="Tên đăng nhập" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="phone" placeholder="Số điện thoại" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit" name="submit">Đăng ký</button>
        </form>
        <div class="link">
            <a href="login.php">Đã có tài khoản? Đăng nhập ngay!</a>
        </div>
    </div>
</body>
</html>