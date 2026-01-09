<?php  
session_start();  

$servername = "localhost";  
$username = "root";   
$password = "chotien22";  
$dbname = "webbanhang";    

 
$connect = mysqli_connect($servername, $username, $password, $dbname);   

if (!$connect) {  
    die("Kết nối thất bại: " . mysqli_connect_error());  
}  

 
$loginname_input = $_POST['loginname'];  
$password_input = $_POST['password'];  

  
$stmt = $connect->prepare("SELECT * FROM `taikhoan` where username = ? ");  
$stmt->bind_param("s", $loginname_input);  
$stmt->execute();  
$result = $stmt->get_result();  

if ($result->num_rows > 0) {  
    $row = $result->fetch_assoc();  
     
    
    if (password_verify($password_input, $row['password'])) {  
        header("Location: ../index/index.php");  
    } else {  
        header("Location: login.php");  
    }  
}else {  
    header("Location: login.php");   
}  


$stmt->close();  
$connect->close();  
?> 


 
     