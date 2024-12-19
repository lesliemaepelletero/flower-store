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
    <title>quick view</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php @include 'header.php'; ?>

    <section class="quick-view">

        <h1 class="title">product details</h1>

        <?php
        if (isset($_GET['pid'])) {
            $pid = $_GET['pid'];
            $stmt = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($fetch_products = $result->fetch_assoc()) {
        ?>
                    <form action="" method="POST">
                        <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="" class="image">
                        <div class="name"><?php echo $fetch_products['name']; ?></div>
                        <div class="price">₱<?php echo $fetch_products['price']; ?>/-</div>
                        <div class="details"><?php echo $fetch_products['details']; ?></div>
                        <input type="number" name="product_quantity" value="1" min="1" class="qty">
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
                echo '<p class="empty">no products details available!</p>';
            }
        }
        ?>

        <div class="more-btn">
            <a href="home.php" class="option-btn">go to home page</a>
        </div>

    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>