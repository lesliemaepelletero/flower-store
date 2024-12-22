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
   <style>
      /* General Styling */
      body {
         font-family: 'Poppins', sans-serif;
         margin: 0;
         padding: 0;
         background: #f9f5f0;
         /* Light floral background */
         color: #444;
      }

      a {
         text-decoration: none;
      }

      h1,
      h3 {
         color: #333;
         text-align: center;
      }



      /* Home Section */
      .home {
         display: flex;
         align-items: center;
         justify-content: center;
         background: url('images/bg-home.png') no-repeat center/cover;
         height: 70vh;
         color: white;
         text-align: center;
      }

      .home .content h3 {
         font-size: 2.5rem;
         text-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
      }

      .home .content p {
         margin: 1rem 0;
         font-size: 1.2rem;
      }

      .home .content .btn {
         background: #ff79a1;
         color: white;
         padding: 0.8rem 1.5rem;
         border-radius: 25px;
         transition: background 0.3s;
      }

      .home .content .btn:hover {
         background: #ff5a87;
      }

      /* Products Section */
      .products {
         padding: 4rem 2rem;
         background: linear-gradient(135deg, #fff6f0, #ffd1e0);
      }

      .products .title {
         font-size: 2rem;
         margin-bottom: 2rem;
         text-transform: uppercase;
         color: #ff79a1;
      }

      /* Product Box */
      .box {
         background: white;
         border-radius: 12px;
         box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
         padding: 1.5rem;
         overflow: hidden;
         transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .box:hover {
         transform: translateY(-10px);
         box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
      }

      .box .image {
         width: 80%;
         height: auto;
         margin: 0 auto;
         display: block;
         border-radius: 10px;
         margin-bottom: 1rem;
      }

      .box .name {
         font-size: 1.2rem;
         font-weight: 600;
         margin: 0.5rem 0;
         text-align: center;
         color: #555;
      }

      .box .price {
         font-size: 1rem;
         font-weight: bold;
         color: #ff5a87;
         text-align: center;
      }

      /* Buttons */
      .option-btn,
      .btn {
         display: block;
         width: 100%;
         text-align: center;
         margin: 0.5rem 0;
         padding: 0.8rem;
         font-size: 1rem;
         border: none;
         border-radius: 25px;
         cursor: pointer;
         transition: background 0.3s, color 0.3s;
      }

      .option-btn {
         background: #ffe4f3;
         color: #ff79a1;
      }

      .option-btn:hover {
         background: #ff79a1;
         color: white;
      }

      .btn {
         background: #ff79a1;
         color: white;
      }

      .btn:hover {
         background: #ff5a87;
      }

      /* Home Contact */
      .home-contact {
         padding: 4rem 2rem;
         background: #fff3f8;
         text-align: center;
      }

      .home-contact .content h3 {
         font-size: 2rem;
         margin-bottom: 1rem;
         color: #ff79a1;
      }

      .home-contact .content p {
         margin-bottom: 1.5rem;
         font-size: 1.1rem;
         color: #666;
      }
   </style>

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
         $select_products = pg_query($conn, "SELECT * FROM available_products");

         if (pg_num_rows($select_products) > 0) {
            while ($fetch_products = pg_fetch_assoc($select_products)) {
               // Get the full image path
               $image_path = 'uploaded_img/' . $fetch_products['product_image']; // Corrected to use product_image

               // Render the product details
         ?>
               <form action="" method="POST" class="box">
                  <a href="view_page.php?pid=<?php echo $fetch_products['product_id']; ?>" class="fas fa-eye"></a>
                  <div class="price">₱<?php echo $fetch_products['product_price']; ?>/-</div>
                  <img src="<?php echo $image_path; ?>" alt="Product Image" class="image">
                  <div class="name"><?php echo $fetch_products['product_name']; ?></div>
                  <input type="number" name="product_quantity" value="1" min="1" class="qty">
                  <input type="hidden" name="product_id" value="<?php echo $fetch_products['product_id']; ?>">
                  <input type="hidden" name="product_name" value="<?php echo $fetch_products['product_name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['product_price']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_products['product_image']; ?>">
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