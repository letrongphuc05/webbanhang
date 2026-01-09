<?php
session_start();

// Xóa bỏ session tài khoản người dùng
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

// Nếu muốn xóa sạch cả giỏ hàng khi đăng xuất thì dùng dòng dưới:
// session_destroy(); 

// Quay về trang đăng nhập
header("Location: login.php");
exit();
?>