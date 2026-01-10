<?php 
    
    $servername = "localhost";  
    $username = "root";   
    $password = "123456";  
    $dbname = "webbanhang";    
    
     
    $connect = mysqli_connect($servername, $username, $password, $dbname);   
    
    if (!$connect) {  
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
?>