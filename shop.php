<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
};

if (isset($_POST['add_to_wishlist'])) {

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    $check_wishlist_numbers = pg_query($conn, "SELECT * FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'");

    $check_cart_numbers = pg_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'");

    if (pg_num_rows($check_wishlist_numbers) > 0) {
        $message[] = 'already added to wishlist';
    } elseif (pg_num_rows($check_cart_numbers) > 0) {
        $message[] = 'already added to cart';
    } else {
        pg_query($conn, "INSERT INTO wishlist (user_id, pid, name, price, image) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_image')");
        $message[] = 'product added to wishlist';
    }
}

if (isset($_POST['add_to_cart'])) {

    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $check_cart_numbers = pg_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'");

    if (pg_num_rows($check_cart_numbers) > 0) {
        $message[] = 'already added to cart';
    } else {

        $check_wishlist_numbers = pg_query($conn, "SELECT * FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'");

        if (pg_num_rows($check_wishlist_numbers) > 0) {
            pg_query($conn, "DELETE FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'");
        }

        pg_query($conn, "INSERT INTO cart (user_id, pid, name, price, quantity, image) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')");
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
    <title>Shop</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .heading {
            background: linear-gradient(135deg, #ffd1e0, #fff6f0);
            padding: 2rem 1rem;
            text-align: center;
        }

        .heading h3 {
            font-size: 4rem;
            color: black;
        }

        .heading p {
            font-weight: bold;
            color: black;
        }

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

        /* Header Styling */
        .header .logo {
            font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            color: #ff79a1;
        }

        .navbar a {
            font-size: 1.1rem;
            color: #444;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #ff79a1;
        }

        /* Home Section */
        .home {
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('images/flower-bg.jpg') no-repeat center/cover;
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

        .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
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
            font-size: 1.6rem;
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

    <section class="heading">
        <h3>Shop</h3>
        <p><a href="home.php">Home</a> / Shop</p>
    </section>

    <section class="products">
        <h1 class="title">Latest Products</h1>
        <div class="box-container">
            <?php
            $select_products = pg_query($conn, "SELECT * FROM products");

            if (pg_num_rows($select_products) > 0) {
                while ($fetch_products = pg_fetch_assoc($select_products)) {
                    $image_path = 'uploaded_img/' . $fetch_products['image'];
            ?>
                    <form action="" method="POST" class="box">
                        <a href="view_page.php?pid=<?php echo $fetch_products['id']; ?>" class="fas fa-eye"></a>
                        <div class="price">₱<?php echo $fetch_products['price']; ?>/-</div>
                        <img src="<?php echo $image_path; ?>" alt="Product Image" class="image">
                        <div class="name"><?php echo $fetch_products['name']; ?></div>
                        <input type="number" name="product_quantity" value="1" min="1" class="qty">
                        <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                        <input type="submit" value="Add to Wishlist" name="add_to_wishlist" class="option-btn">
                        <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
                    </form>
            <?php
                }
            } else {
                echo '<p class="empty">No products added yet!</p>';
            }
            ?>
        </div>
    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>