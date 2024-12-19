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

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
    $stmt->bind_param("si", $product_name, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Checking if the product is already in wishlist or cart
    $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $check_cart_numbers->bind_param("si", $product_name, $user_id);
    $check_cart_numbers->execute();
    $cart_result = $check_cart_numbers->get_result();

    if ($result->num_rows > 0) {
        $message[] = 'already added to wishlist';
    } elseif ($cart_result->num_rows > 0) {
        $message[] = 'already added to cart';
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO `wishlist` (user_id, pid, name, price, image) VALUES (?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("iisss", $user_id, $product_id, $product_name, $product_price, $product_image);
        $stmt_insert->execute();
        $message[] = 'product added to wishlist';
    }
}

if (isset($_POST['add_to_cart'])) {

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    // Using prepared statements to prevent SQL injection
    $stmt_check_cart = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
    $stmt_check_cart->bind_param("si", $product_name, $user_id);
    $stmt_check_cart->execute();
    $cart_result = $stmt_check_cart->get_result();

    if ($cart_result->num_rows > 0) {
        $message[] = 'already added to cart';
    } else {

        $stmt_check_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $stmt_check_wishlist->bind_param("si", $product_name, $user_id);
        $stmt_check_wishlist->execute();
        $wishlist_result = $stmt_check_wishlist->get_result();

        if ($wishlist_result->num_rows > 0) {
            $stmt_delete = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
            $stmt_delete->bind_param("si", $product_name, $user_id);
            $stmt_delete->execute();
        }

        $stmt_insert_cart = $conn->prepare("INSERT INTO `cart` (user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert_cart->bind_param("iissis", $user_id, $product_id, $product_name, $product_price, $product_quantity, $product_image);
        $stmt_insert_cart->execute();
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
            $query = "SELECT * FROM products WHERE id = $1";
            $result = pg_prepare($conn, "get_product", $query);
            $result = pg_execute($conn, "get_product", array($pid));

            if ($result && pg_num_rows($result) > 0) {
                while ($fetch_products = pg_fetch_assoc($result)) {
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