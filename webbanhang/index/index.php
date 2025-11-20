
<!DOCTYPE html>
<html>
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE-edge">
      <meta name="viewport" content="width-device-width,initial-scale=1.0">
      <link rel="stylesheet" href="./fontawesome-free-6.6.0-web/css/all.min.css">
      <link rel="stylesheet" href="interface.css">
      <title>Trang Chủ - LaptopDz</title>
    </head>
    <body>
      <nav>
        <div class=".container">
          <ul>
            <li><a href="index.php"><i class="fa-solid fa-laptop"></i>LaptopDz</a></li>
            <li><input type="text" placeholder="Search ..."><i style="color: black;" class="fa-solid fa-magnifying-glass"></i></li>
            <li><a  href="../login/login.php"><i class="fa-solid fa-user"></i>Đăng xuất </a>     
            </li>
            <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i>Giỏ Hàng</a></li>
            <li><a href=""><i class="fa-solid fa-headphones"></i>Hotline</a></li>
          </ul>
        </div>
      </nav>
      <section class="menu-bar">
        <div class="container">
          <div class="menu-bar-content">
          <?php    
              include('connect.php'); 
       
              $kq_list_hang1 = mysqli_query($connect, "SELECT MAHANG, TENHANG FROM hang WHERE loai = 1");
              $kq_list_hang2 = mysqli_query($connect, "SELECT MAHANG, TENHANG FROM hang WHERE loai = 2");
              $kq_list_hang3 = mysqli_query($connect, "SELECT MAHANG, TENHANG FROM hang WHERE loai = 3");
              $kq_list_hang4 = mysqli_query($connect, "SELECT MAHANG, TENHANG FROM hang WHERE loai = 4");
              $kq_list_hang5 = mysqli_query($connect, "SELECT MAHANG, TENHANG FROM hang WHERE loai = 5");
              

              $list_category = [];   

              if ($kq_list_hang1) {  
                  while ($row = mysqli_fetch_assoc($kq_list_hang1)) {  
                      $list_category[] = $row;   
                  }  
              }
              mysqli_close($connect); 
          ?>
              <ul >
                <li><a href=""><i class="fa-solid fa-laptop"></i>LAPTOP <i style="margin-left: 5px;" class="fa-solid fa-sort-down"></i></a>
                  <div class="submenu">
                    <ul>
                      <?php foreach($kq_list_hang1 as $key => $value) {?>
                        <li><a href="?page=1&category=<?=$value['MAHANG']?>"><?=$value['TENHANG']?></a></li>
                      <?php }?>
                    </ul>
                  </div>
                </li>
                <li><a href=""><i class="fa-solid fa-headphones"></i>Phụ Kiện <i style="margin-left: 5px;" class="fa-solid fa-sort-down"></i></a>
                  <div class="submenu">
                    <ul>
                      <?php foreach($kq_list_hang2 as $key => $value) {?>
                        <li><a href="?page=1&category=<?=$value['MAHANG']?>"><?=$value['TENHANG']?></a></li>
                      <?php }?>
                    </ul>
                  </div>
                </li>
                <li><a href=""><i class="fa-solid fa-desktop"></i>PC <i style="margin-left: 5px;" class="fa-solid fa-sort-down"></i></a>
                  <div class="submenu">
                    <ul>
                    <?php foreach($kq_list_hang3 as $key => $value) {?>
                        <li><a href="?page=1&category=<?=$value['MAHANG']?>"><?=$value['TENHANG']?></a></li>
                      <?php }?>
                    </ul>
                  </div>
                </li>
                <li><a href=""><i class="fa-regular fa-clock"></i>SMARTWATCH <i style="margin-left: 5px;" class="fa-solid fa-sort-down"></i></a>
                  <div class="submenu">
                    <ul>
                    <?php foreach($kq_list_hang4 as $key => $value) {?>
                        <li><a href="?page=1&category=<?=$value['MAHANG']?>"><?=$value['TENHANG']?></a></li>
                      <?php }?>
                  </div>
                </li>
                <li><a href=""><i class="fa-solid fa-mobile-screen-button"></i>Điên thoại <i style="margin-left: 5px;" class="fa-solid fa-sort-down"></i></a>
                  <div class="submenu">
                    <ul>
                    <?php foreach($kq_list_hang5 as $key => $value) {?>
                        <li><a href="?page=1&category=<?=$value['MAHANG']?>"><?=$value['TENHANG']?></a></li>
                      <?php }?>
                    </ul>
                  </div>
              </ul>
          </div>
        </div>
      </section>
      <?php    
          include('connect.php'); 
          if(!empty($_GET['category'])) {
              $result = mysqli_query($connect, "SELECT COUNT(*) AS total FROM san_pham WHERE MAHANG = ".$_GET['category']);
          }
          else {
            $result = mysqli_query($connect, "SELECT COUNT(*) AS total FROM san_pham");
          }
          $totalProducts = mysqli_fetch_assoc($result)['total'];  
          $productsPerPage = 12;   
          $totalPages = ceil($totalProducts / $productsPerPage);  

          if (isset($_GET['page']) && is_numeric($_GET['page'])) {  
              $currentPage = (int)$_GET['page'];  
          } else {  
              $currentPage = 1;  
          }  

          $start = ($currentPage - 1) * $productsPerPage;   
          if(!empty($_GET['category'])) {
            $kq = mysqli_query($connect, "SELECT MASP, TENSP, ANHSP, GIATHANH FROM san_pham WHERE MAHANG = ".$_GET['category']." LIMIT $start, $productsPerPage");  
            $kq_hang = mysqli_query($connect, "SELECT TENHANG FROM hang WHERE MAHANG = ".$_GET['category']." LIMIT 1");  
          }
          else {
            $kq = mysqli_query($connect, "SELECT MASP, TENSP, ANHSP, GIATHANH FROM san_pham LIMIT $start, $productsPerPage");  
          }  
          $products = [];   

          if ($kq) {  
              while ($row = mysqli_fetch_assoc($kq)) {  
                  $products[] = $row;   
              }  
          }
          $category = '';  
          if (!empty($kq_hang)) {  
            while ($row = mysqli_fetch_assoc($kq_hang)) {  
                $category = $row['TENHANG'];   
            }  
        }  

          mysqli_close($connect); 
      ?>
      <h1><?=!empty($category) ? $category : 'Sản Phẩm Hot'?></h1>  
      <div class="product">  
    <?php  
      foreach ($products as $product) {  
        echo '<div class="product-card">';
        echo '<a href="detail.php?id=' . $product['MASP'] . '" target="_self">';
        echo '<p class="product-name">' . htmlspecialchars($product['TENSP']) . '</p>';   
        echo '<a href="detail.php?id=' . $product['MASP'] . '"><img src="' . htmlspecialchars($product['ANHSP']) . '" alt="Hình ảnh sản phẩm"></a>';  
        echo '</a>'; 
        echo '<p class="product-price">Giá: ' . htmlspecialchars($product['GIATHANH']) . ' vnđ</p>';   
        echo '<a href="detail.php?id=' . $product['MASP'] . '"> xem chi tiết</a>';
        echo '</div>';  
    } 
    ?>  
</div>
      <div class="pagination">  
          <?php if ($currentPage > 1): ?>  
              <a href="?page=<?php echo $currentPage - 1; ?><?=!empty($_GET['category']) ? ('&category=' . $_GET['category']) : ''?>">« Trước</a>  
          <?php endif; ?>  

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>  
              <a href="?page=<?php echo $i; ?><?=!empty($_GET['category']) ? ('&category=' . $_GET['category']) : ''?>" class="<?php echo $i == $currentPage ? 'active' : ''; ?>">  
                  <?php echo $i; ?>  
              </a>  
          <?php endfor; ?>  

          <?php if ($currentPage < $totalPages): ?>  
              <a href="?page=<?php echo $currentPage + 1; ?><?=!empty($_GET['category']) ? ('&category=' . $_GET['category']) : ''?>">Tiếp »</a>  
          <?php endif; ?>  
      </div>    
    </body>
</html>