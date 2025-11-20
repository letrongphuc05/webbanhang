<?php   
if (isset($_POST['submit'])) {   
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

    $success_insert = $connect->query("INSERT INTO taikhoan (`username`, `phone`, `email`, `password`) VALUES ('$loginname', '$phone', '$email', '$hashed_password')");  
    
    if ($success_insert) {  
        echo ("<script>alert('Đăng ký thành công');</script>");
        header("Location: login.php");  
    } else {  
        echo ("<script>alert('Đăng ký thất bại');</script>");  
    }  

    $connect->close();  
}  
?>  