<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="stylesheet" href="interface.css">
    <title>login</title>    
</head>  
<body>  
    <div id="wrapper">  
        <form id="form-login" onsubmit="return checkform();" action="Login_handler.php" method="post">  
            <h1 class="form-heading">Đăng Nhập</h1>  
            <div class="form-group">  
                <input type="text" class="form-input" name="loginname" id="loginname" value="" placeholder="Nhập tên đăng nhập">  
            </div>  
            <div class="form-group">  
                <input type="password" class="form-input" name="password" id="password" value="" placeholder="Nhập mật Khẩu">  
            </div>  
            <div class="form-submit">  
                <input type="submit" value="Đăng Nhập">  
            </div>  
            <div class="register">  
            Bạn chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>  
            </div> 
            <script src="checkform.js"></script>
        </form>  
    </div>  
</body>  
</html> 