<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

if (isset($_POST['add_to_wishlist'])) {

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    // Prevent SQL injection using pg_escape_string
    $product_name = pg_escape_string($conn, $product_name);

    // Check if the product is already in wishlist or cart
    $check_wishlist_query = "SELECT * FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'";
    $check_cart_query = "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'";

    $result = pg_query($conn, $check_wishlist_query);
    $cart_result = pg_query($conn, $check_cart_query);

    if (pg_num_rows($result) > 0) {
        $message[] = 'already added to wishlist';
    } elseif (pg_num_rows($cart_result) > 0) {
        $message[] = 'already added to cart';
    } else {
        $insert_query = "INSERT INTO wishlist (user_id, pid, name, price, image) VALUES ('$user_id', '$product_id', '$product_name', '$product_price', '$product_image')";
        pg_query($conn, $insert_query);
        $message[] = 'product added to wishlist';
    }
}

if (isset($_POST['add_to_cart'])) {

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    // Prevent SQL injection using pg_escape_string
    $product_name = pg_escape_string($conn, $product_name);

    // Check if the product is already in the cart
    $check_cart_query = "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'";
    $check_wishlist_query = "SELECT * FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'";

    $cart_result = pg_query($conn, $check_cart_query);

    if (pg_num_rows($cart_result) > 0) {
        $message[] = 'already added to cart';
    } else {

        $wishlist_result = pg_query($conn, $check_wishlist_query);

        if (pg_num_rows($wishlist_result) > 0) {
            // Remove from wishlist if exists
            $delete_query = "DELETE FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'";
            pg_query($conn, $delete_query);
        }

        // Add to cart
        $insert_cart_query = "INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES ('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')";
        pg_query($conn, $insert_cart_query);
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
    <title>Quick View</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom Admin CSS file link -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Quick View Page Styling */
        .quick-view {
            background: #fff3f8;
            /* Light pink background */
            padding: 2rem 3rem;
            max-width: 900px;
            margin: 0 auto;

            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .quick-view .title {
            font-size: 2.5rem;
            color: #ff79a1;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .quick-view .product-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .quick-view .product-image {
            width: 300px;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .quick-view .product-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #444;
        }

        .quick-view .product-price {
            font-size: 1.3rem;
            color: #ff79a1;
            font-weight: 500;
        }

        .quick-view .product-details {
            font-size: 2rem;
            color: #666;
            text-align: center;
            max-width: 80%;
        }

        .quick-view .product-quantity {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid #ff79a1;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .quick-view .actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .quick-view .btn {
            padding: 0.7rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
        }

        .quick-view .option-btn {
            background-color: #ff79a1;
            color: white;
        }

        .quick-view .cart-btn {
            background-color: #ff45a1;
            color: white;
        }

        .quick-view .btn:hover {
            background-color: #ff5ab3;
        }

        .quick-view .more-btn {
            text-align: center;
            margin-top: 2rem;
        }

        .quick-view .more-btn .option-btn {
            padding: 0.8rem 2rem;
            background-color: #ff79a1;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .quick-view .more-btn .option-btn:hover {
            background-color: #ff5ab3;
        }

        .quick-view .empty {
            text-align: center;
            color: #ff45a1;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>

    <?php @include 'header.php'; ?>

    <section class="quick-view">
        <h1 class="title">Product Details</h1>

        <?php
        if (isset($_GET['pid'])) {
            $pid = $_GET['pid'];

            // Query the product details
            $product_query = "SELECT * FROM products WHERE id = '$pid'";
            $product_result = pg_query($conn, $product_query);

            if (pg_num_rows($product_result) > 0) {
                while ($fetch_products = pg_fetch_assoc($product_result)) {
        ?>
                    <form action="" method="POST" class="product-form">
                        <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="Product Image" class="product-image">
                        <div class="product-name"><?php echo $fetch_products['name']; ?></div>
                        <div class="product-price">₱<?php echo $fetch_products['price']; ?>/-</div>
                        <div class="product-details"><?php echo $fetch_products['details']; ?></div>
                        <input type="number" name="product_quantity" value="1" min="1" class="product-quantity">
                        <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                        <div class="actions">
                            <input type="submit" value="Add to Wishlist" name="add_to_wishlist" class="btn option-btn">
                            <input type="submit" value="Add to Cart" name="add_to_cart" class="btn cart-btn">
                        </div>
                    </form>
        <?php
                }
            } else {
                echo '<p class="empty">No product details available!</p>';
            }
        }
        ?>

        <div class="more-btn">
            <a href="home.php" class="option-btn">Go to Home Page</a>
        </div>

    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>