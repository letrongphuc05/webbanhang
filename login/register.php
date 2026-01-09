<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="interface.css">
    <title>register</title>
</head>
<body>
    <div id="wrapper">  
        <form id="form-register" onsubmit="return checkform();" action="active_register.php" method="post">  
            <h1 class="form-heading">Đăng ký</h1>  
            <div class="form-group">  
                <input type="text" class="form-input" name="loginname" id="loginname" value="" placeholder="Nhập tên đăng nhập">  
            </div>  
            <div class="form-group">  
                <input type="number" class="form-input" name="phone" id="phone" value="" placeholder="Nhập số điện thoại">  
            </div>  
            <div class="form-group">
                <input type="email" class="form-input" name="email" id="email" value="" placeholder="Nhập email">
            </div>
            <div class="form-group">  
                <input type="password" class="form-input" name="password" id="password" value="" placeholder="Nhập mật Khẩu">  
            </div>  
            <div class="form-group">  
                <input type="password" class="form-input" name="checkpassword" id="checkpassword" value="" placeholder="Nhập lại mật Khẩu">  
            </div>
            <div class="form-submit">  
                <input type="submit" name="submit" value="Đăng ký">  
            </div>  
            <div class="login">  
            Bạn đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>  
            </div> 
            <script src="checkform.js"></script>
        </form>  
    </div> 
</body>
</html>