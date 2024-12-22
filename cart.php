<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

// Log activity function
function log_activity($conn, $user_id, $action, $description)
{
    $query = "INSERT INTO activity_log (user_id, action, description) VALUES ($1, $2, $3)";
    pg_query_params($conn, $query, [$user_id, $action, $description]) or die('Log activity failed');
}

// Delete a specific item from the cart
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_query = pg_query($conn, "DELETE FROM cart WHERE id = '$delete_id'") or die('query failed');
    if ($delete_query) {
        log_activity($conn, $user_id, 'DELETE', "Deleted item with cart ID: $delete_id");
    }
    header('location:cart.php');
    exit();
}

// Delete all items from the cart
if (isset($_GET['delete_all'])) {
    $delete_all_query = pg_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'") or die('query failed');
    if ($delete_all_query) {
        log_activity($conn, $user_id, 'DELETE', "Deleted all items from the cart for user ID: $user_id");
    }
    header('location:cart.php');
    exit();
}

// Update the quantity of a specific cart item
if (isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = $_POST['cart_quantity'];
    $update_query = pg_query($conn, "UPDATE cart SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
    if ($update_query) {
        log_activity($conn, $user_id, 'UPDATE', "Updated quantity of item with cart ID: $cart_id to $cart_quantity");
    }
    $message[] = 'Cart quantity updated!';
}

// Fetch the grand total using the PostgreSQL function
$result = pg_query($conn, "SELECT calculate_cart_total($user_id) AS grand_total") or die('query failed');
$grand_total = 0;
if ($row = pg_fetch_assoc($result)) {
    $grand_total = $row['grand_total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom admin CSS file link -->
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

        .shopping-cart {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #fff6f0, #ffd1e0);
        }

        .shopping-cart .title {
            font-size: 2.5rem;
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


        .box .name {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0.5rem 0;
            text-align: center;
            color: #555;
        }

        .box .price,
        .sub-total,
        .cart-total {
            font-size: 1rem;
            font-weight: bold;
            color: #ff5a87;
            text-align: center;
        }

        /* Buttons */
        .option-btn,
        .btn,
        .more-btn {
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
        <h3>Shopping Cart</h3>
        <p> <a href="home.php">home</a> / cart </p>
    </section>

    <section class="shopping-cart">
        <h1 class="title">Products Added</h1>
        <div class="box-container">
            <?php
            $select_cart = pg_query($conn, "SELECT * FROM cart_view WHERE user_id = '$user_id'") or die('query failed');
            if (pg_num_rows($select_cart) > 0) {
                while ($fetch_cart = pg_fetch_assoc($select_cart)) {
            ?>
                    <div class="box">
                        <a href="cart.php?delete=<?php echo $fetch_cart['cart_id']; ?>" class="fas fa-times" onclick="return confirm('delete this from cart?');"></a>
                        <a href="view_page.php?pid=<?php echo $fetch_cart['pid']; ?>" class="fas fa-eye"></a>
                        <img src="uploaded_img/<?php echo $fetch_cart['product_image']; ?>" alt="Product Image" class="image">
                        <div class="name"><?php echo $fetch_cart['cart_item_name']; ?></div>
                        <div class="price">₱<?php echo $fetch_cart['cart_item_price']; ?>/-</div>
                        <form action="" method="post">
                            <input type="hidden" value="<?php echo $fetch_cart['cart_id']; ?>" name="cart_id">
                            <input type="number" min="1" value="<?php echo $fetch_cart['cart_quantity']; ?>" name="cart_quantity" class="qty">
                            <input type="submit" value="update" class="option-btn" name="update_quantity">
                        </form>
                        <div class="sub-total"> sub-total : <span>₱<?php echo $fetch_cart['subtotal']; ?>/-</span> </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">Your cart is empty</p>';
            }
            ?>
        </div>

        <div class="more-btn">
            <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled' ?>" onclick="return confirm('delete all from cart?');">Delete All</a>
        </div>

        <div class="cart-total">
            <p>Grand Total: <span>₱<?php echo $grand_total; ?>/-</span></p>
            <a href="shop.php" class="option-btn">Continue Shopping</a>
            <a href="checkout.php" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled' ?>">Proceed to Checkout</a>
        </div>
    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>
</body>

</html>