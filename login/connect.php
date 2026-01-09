<?php
    $servername = "localhost";
    $username = "root";
    $password = "chotien22";
    $dbname = "webbanhang";

    $connect = mysqli_connect($servername, $username, $password, $dbname);

    if (!$connect) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
    mysqli_set_charset($connect, 'UTF8');
?>