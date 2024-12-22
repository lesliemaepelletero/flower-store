<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

// Redirect to login if the user is not logged in
if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

// Handle "Add to Cart" functionality
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = 1;

    // Check if the product is already in the cart
    $check_cart_query = "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'";
    $check_cart_result = pg_query($conn, $check_cart_query) or die('query failed');

    if (pg_num_rows($check_cart_result) > 0) {
        $message[] = 'Product is already in your cart.';
    } else {
        // Remove the product from wishlist if it exists
        $check_wishlist_query = "SELECT * FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'";
        $check_wishlist_result = pg_query($conn, $check_wishlist_query) or die('query failed');

        if (pg_num_rows($check_wishlist_result) > 0) {
            $delete_wishlist_query = "DELETE FROM wishlist WHERE name = '$product_name' AND user_id = '$user_id'";
            pg_query($conn, $delete_wishlist_query) or die('query failed');
        }

        // Add the product to the cart
        $insert_cart_query = "INSERT INTO cart(user_id, pid, name, price, quantity, image) 
                              VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')";
        pg_query($conn, $insert_cart_query) or die('query failed');
        $message[] = 'Product added to cart.';
    }
}

// Handle "Delete from Wishlist" functionality
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_query = "DELETE FROM wishlist WHERE id = '$delete_id'";
    pg_query($conn, $delete_query) or die('query failed');
    header('location:wishlist.php');
    exit;
}

// Handle "Delete All from Wishlist" functionality
if (isset($_GET['delete_all'])) {
    $delete_all_query = "DELETE FROM wishlist WHERE user_id = '$user_id'";
    pg_query($conn, $delete_all_query) or die('query failed');
    header('location:wishlist.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>

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

        .wishlist {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #fff6f0, #ffd1e0);
        }

        .wishlist .title {
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

        .box .price,
        .sub-total,
        .cart-total {
            font-size: 1rem;
            font-weight: bold;
            color: #ff5a87;
            text-align: center;
        }

        .box .name {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0.5rem 0;
            text-align: center;
            color: #555;
        }

        .option-btn,
        .btn,
        .more-btn {
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

        .option-btn,
        .more-btn {
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
    </style>

</head>

<body>

    <?php @include 'header.php'; ?>

    <section class="heading">
        <h3>Your Wishlist</h3>
        <p><a href="home.php">Home</a> / Wishlist</p>
    </section>

    <section class="wishlist">

        <h1 class="title">Products in Wishlist</h1>

        <div class="box-container">
            <?php
            $grand_total = 0;

            // Fetch the user's wishlist
            $wishlist_query = "SELECT * FROM user_wishlist_cart WHERE user_id = '$user_id'";
            $wishlist_result = pg_query($conn, $wishlist_query) or die('query failed');

            if (pg_num_rows($wishlist_result) > 0) {
                while ($wishlist_item = pg_fetch_assoc($wishlist_result)) {
            ?>
                    <form action="" method="POST" class="box">
                        <a href="wishlist.php?delete=<?php echo $wishlist_item['wishlist_id']; ?>" class="fas fa-times" onclick="return confirm('Delete this item from wishlist?');"></a>
                        <a href="view_page.php?pid=<?php echo $wishlist_item['pid']; ?>" class="fas fa-eye"></a>
                        <img src="uploaded_img/<?php echo htmlspecialchars($wishlist_item['wishlist_item_image']); ?>" alt="" class="image">
                        <div class="name"><?php echo htmlspecialchars($wishlist_item['wishlist_item_name']); ?></div>
                        <div class="price">₱<?php echo htmlspecialchars($wishlist_item['wishlist_item_price']); ?>/-</div>
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($wishlist_item['pid']); ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($wishlist_item['wishlist_item_name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($wishlist_item['wishlist_item_price']); ?>">
                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($wishlist_item['wishlist_item_image']); ?>">
                        <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
                    </form>
            <?php
                    $grand_total += $wishlist_item['wishlist_item_price'];
                }
            } else {
                echo '<p class="empty">Your wishlist is empty.</p>';
            }
            ?>
        </div>

        <div class="wishlist-total">
            <p>Grand Total: <span>₱<?php echo $grand_total; ?>/-</span></p>
            <a href="shop.php" class="option-btn">Continue Shopping</a>
            <a href="wishlist.php?delete_all" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>" onclick="return confirm('Delete all items from wishlist?');">Delete All</a>
        </div>

    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>