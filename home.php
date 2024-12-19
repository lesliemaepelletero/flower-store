<?php

@include 'config.php'; // Ensure this file includes the correct PostgreSQL connection setup

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

if (isset($_POST['add_to_wishlist'])) {

   $product_id = $_POST['product_id'];
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];

   // Check if the product is already in the wishlist
   $check_wishlist_numbers = pg_query_params($conn, "SELECT * FROM wishlist WHERE name = $1 AND user_id = $2", array($product_name, $user_id));

   // Check if the product is already in the cart
   $check_cart_numbers = pg_query_params($conn, "SELECT * FROM cart WHERE name = $1 AND user_id = $2", array($product_name, $user_id));

   if (pg_num_rows($check_wishlist_numbers) > 0) {
      $message[] = 'already added to wishlist';
   } elseif (pg_num_rows($check_cart_numbers) > 0) {
      $message[] = 'already added to cart';
   } else {
      pg_query_params($conn, "INSERT INTO wishlist(user_id, pid, name, price, image) VALUES($1, $2, $3, $4, $5)", array($user_id, $product_id, $product_name, $product_price, $product_image));
      $message[] = 'product added to wishlist';
   }
}

if (isset($_POST['add_to_cart'])) {

   $product_id = $_POST['product_id'];
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   // Check if the product is already in the cart
   $check_cart_numbers = pg_query_params($conn, "SELECT * FROM cart WHERE name = $1 AND user_id = $2", array($product_name, $user_id));

   if (pg_num_rows($check_cart_numbers) > 0) {
      $message[] = 'already added to cart';
   } else {

      // Check if the product is in the wishlist
      $check_wishlist_numbers = pg_query_params($conn, "SELECT * FROM wishlist WHERE name = $1 AND user_id = $2", array($product_name, $user_id));

      if (pg_num_rows($check_wishlist_numbers) > 0) {
         pg_query_params($conn, "DELETE FROM wishlist WHERE name = $1 AND user_id = $2", array($product_name, $user_id));
      }

      pg_query_params($conn, "INSERT INTO cart(user_id, pid, name, price, quantity, image) VALUES($1, $2, $3, $4, $5, $6)", array($user_id, $product_id, $product_name, $product_price, $product_quantity, $product_image));
      $message[] = 'product added to cart';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php @include 'header.php'; ?>

   <section class="home">

      <div class="content">
         <h3>welcome to Blooming Blossoms!</h3>
         <p>The beauty of flowers is forever captured in the way floral designers make them a part of our life's memories!</p>
         <a href="about.php" class="btn">discover more</a>
      </div>

   </section>

   <section class="products">

      <h1 class="title">latest products</h1>

      <div class="box-container">

         <?php
         $select_products = pg_query($conn, "SELECT * FROM products LIMIT 6");

         if (pg_num_rows($select_products) > 0) {
            while ($fetch_products = pg_fetch_assoc($select_products)) {
         ?>
               <form action="" method="POST" class="box">
                  <a href="view_page.php?pid=<?php echo $fetch_products['id']; ?>" class="fas fa-eye"></a>
                  <div class="price">₱<?php echo $fetch_products['price']; ?>/-</div>
                  <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="" class="image">
                  <div class="name"><?php echo $fetch_products['name']; ?></div>
                  <input type="number" name="product_quantity" value="1" min="0" class="qty">
                  <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
                  <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                  <input type="submit" value="add to wishlist" name="add_to_wishlist" class="option-btn">
                  <input type="submit" value="add to cart" name="add_to_cart" class="btn">
               </form>
         <?php
            }
         } else {
            echo '<p class="empty">no products added yet!</p>';
         }
         ?>

      </div>

      <div class="more-btn">
         <a href="shop.php" class="option-btn">load more</a>
      </div>

   </section>

   <section class="home-contact">

      <div class="content">
         <h3>have any questions?</h3>
         <p>Just click below.</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </section>

   <?php @include 'footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>