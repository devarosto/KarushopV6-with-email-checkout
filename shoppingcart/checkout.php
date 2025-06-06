<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

@include 'config.php';

if(isset($_POST['order_btn'])){

   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $method = $_POST['method'];
   $city = $_POST['city'];

   $cart_query = mysqli_query($conn, "SELECT * FROM `cart`");
   $price_total = 0;
   if(mysqli_num_rows($cart_query) > 0){
      while($product_item = mysqli_fetch_assoc($cart_query)){
         $product_name[] = $product_item['name'] .' ('. $product_item['quantity'] .') ';
         $product_price = number_format($product_item['price'] * $product_item['quantity']);
         $price_total += $product_price;
      };
   };

   $total_product = implode(', ',$product_name);
   $detail_query = mysqli_query($conn, "INSERT INTO `orders` (name, number, email, method, city, total_products, total_price) VALUES('$name','$number','$email','$method','$city','$total_product','$price_total')") or die('query failed');

   if($cart_query && $detail_query){

      // Prepare email content
      $to = $email;
      $subject = "Your KaruShop Order Details";
      $body = "
         <div style='font-family: Arial, sans-serif; color: #333;'>
            <h2 style='color: #4CAF50;'>Thank you for your order!</h2>
            <p>Here are your order details:</p>
            <table style='width: 100%; border-collapse: collapse;'>
               <tr>
                  <td style='padding: 8px; border: 1px solid #ddd;'><strong>Products</strong></td>
                  <td style='padding: 8px; border: 1px solid #ddd;'>$total_product</td>
               </tr>
               <tr>
                  <td style='padding: 8px; border: 1px solid #ddd;'><strong>Total</strong></td>
                  <td style='padding: 8px; border: 1px solid #ddd;'>$$price_total</td>
               </tr>
               <tr>
                  <td style='padding: 8px; border: 1px solid #ddd;'><strong>Name</strong></td>
                  <td style='padding: 8px; border: 1px solid #ddd;'>$name</td>
               </tr>
               <tr>
                  <td style='padding: 8px; border: 1px solid #ddd;'><strong>Number</strong></td>
                  <td style='padding: 8px; border: 1px solid #ddd;'>$number</td>
               </tr>
               <tr>
                  <td style='padding: 8px; border: 1px solid #ddd;'><strong>Email</strong></td>
                  <td style='padding: 8px; border: 1px solid #ddd;'>$email</td>
               </tr>
               <tr>
                  <td style='padding: 8px; border: 1px solid #ddd;'><strong>Address</strong></td>
                  <td style='padding: 8px; border: 1px solid #ddd;'>$city</td>
               </tr>
               <tr>
                  <td style='padding: 8px; border: 1px solid #ddd;'><strong>Payment Method</strong></td>
                  <td style='padding: 8px; border: 1px solid #ddd;'>$method</td>
               </tr>
            </table>
            <p style='margin-top:20px;'>We appreciate your business!<br>KaruShop Team</p>
         </div>
      ";

      // PHPMailer setup
      $mail = new PHPMailer(true);
      try {
         $mail->isSMTP();
         $mail->Host       = 'smtp.gmail.com';
         $mail->SMTPAuth   = true;
         $mail->Username   = 'karushopstore@gmail.com'; // Your Gmail address
         $mail->Password   = 'kafb eelm ocnr wrsa';    // Gmail App Password
         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->Port       = 587;

         $mail->setFrom('karushopstore@gmail.com', 'KaruShop');
         $mail->addAddress($to, $name);

         $mail->isHTML(true);
         $mail->Subject = $subject;
         $mail->Body    = $body;

         $mail->send();
         $email_status = "<p style='color:green;'>Order details have been sent to your email!</p>";
      } catch (Exception $e) {
         $email_status = "<p style='color:red;'>Email could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
      }

      echo "
      <div class='order-message-container'>
      <div class='message-container'>
         <h3>These are your order details</h3>
         <div class='order-detail'>
            <span>".$total_product."</span>
            <span class='total'> total : $".$price_total."/-  </span>
         </div>
         <div class='customer-details'>
            <p> your name : <span>".$name."</span> </p>
            <p> your number : <span>".$number."</span> </p>
            <p> your email : <span>".$email."</span> </p>
            <p> your address : <span> ".$city.", </span> </p>
            <p> your payment mode : <span>".$method."</span> </p>
            <p>(*there is no cash payment allowed*)</p>
            $email_status
         </div>
            <a href='products.php' class='btn'>continue shopping</a>
         </div>
      </div>
      ";
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'header.php'; ?>

<div class="container">

<section class="checkout-form">

   <h1 class="heading">complete your order</h1>

   <form action="" method="post">

   <div class="display-order">
      <?php
         $select_cart = mysqli_query($conn, "SELECT * FROM `cart`");
         $total = 0;
         $grand_total = 0;
         if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = number_format($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total = $total += $total_price;
      ?>
      <span><?= $fetch_cart['name']; ?>(<?= $fetch_cart['quantity']; ?>)</span>
      <?php
         }
      }else{
         echo "<div class='display-order'><span>your cart is empty!</span></div>";
      }
      ?>
      <span class="grand-total"> grand total : $<?= $grand_total; ?>/- </span>
   </div>

      <div class="flex">
         <div class="inputBox">
            <span>your name</span>
            <input type="text" placeholder="enter your name" name="name" required>
         </div>
         <div class="inputBox">
            <span>your number</span>
            <input type="number" placeholder="start with:+2547" name="number" required>
         </div>
         <div class="inputBox">
            <span>your email</span>
            <input type="email" placeholder="enter valid email" name="email" required>
         </div>
         <div class="inputBox">
            <span>payment method</span>
            <select name="method">
               <option value="cash on delivery" selected>cash on devlivery</option>
               <option value="Mpesa Global pay">Mpesa Global Pay</option>
               <option value="Mpesa">Mpesa</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Pick up point</span>
            <input type="text" placeholder="e.g. Karatina Town" name="city" required>
         </div>
      </div>
      <input type="submit" value="order now" name="order_btn" class="btn">
   </form>

</section>

</div>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>