<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<header class="header">

   <div class="flex">

      <a href="#" class="logo">KaruShop</a>

      <nav class="navbar">
         <a href="admin.php"> <i class="fas fa-store"></i>My shop</a>
         <a href="products.php"> <i class="fas fa-home"></i>Home Page</a>
      </nav>

      <?php
      
      $select_rows = mysqli_query($conn, "SELECT * FROM `cart`") or die('query failed');
      $row_count = mysqli_num_rows($select_rows);

      ?>

      <a href="cart.php" class="cart">cart <span><?php echo $row_count; ?></span> </a>

      <div id="menu-btn" class="fas fa-bars"></div>

   </div>

</header>