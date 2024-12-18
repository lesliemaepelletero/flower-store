<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
};

if (isset($_POST['order'])) {

    $first_name = pg_escape_string($conn, $_POST['first_name']);
    $last_name = pg_escape_string($conn, $_POST['last_name']);
    $number = pg_escape_string($conn, $_POST['phone_number']);
    $email = pg_escape_string($conn, $_POST['email']);
    $method = pg_escape_string($conn, $_POST['method']);
    $address = pg_escape_string($conn, 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code']);
    $placed_on = date('d-M-Y');

    $cart_total = 0;
    $cart_products = [];

    $cart_query = pg_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'") or die('query failed');
    if (pg_num_rows($cart_query) > 0) {
        while ($cart_item = pg_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode(', ', $cart_products);

    $order_query = pg_query($conn, "SELECT * FROM orders WHERE first_name = '$first_name' AND last_name = '$last_name' AND phone_number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

    if ($cart_total == 0) {
        $message[] = 'your cart is empty!';
    } elseif (pg_num_rows($order_query) > 0) {
        $message[] = 'order placed already!';
    } else {
        pg_query($conn, "INSERT INTO orders(user_id, first_name, last_name, phone_number, email, method, address, total_products, total_price, placed_on) VALUES('$user_id', '$first_name', '$last_name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") or die('query failed');
        pg_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'") or die('query failed');
        $message[] = 'order placed successfully!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom admin CSS file link -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php @include 'header.php'; ?>

    <section class="heading">
        <h3>Checkout Order</h3>
        <p> <a href="home.php">home</a> / checkout </p>
    </section>

    <section class="display-order">
        <?php
        $grand_total = 0;
        $select_cart = pg_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'") or die('query failed');
        if (pg_num_rows($select_cart) > 0) {
            while ($fetch_cart = pg_fetch_assoc($select_cart)) {
                $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
                $grand_total += $total_price;
        ?>
                <p> <?php echo $fetch_cart['name'] ?> <span>(<?php echo '₱' . $fetch_cart['price'] . '/-' . ' x ' . $fetch_cart['quantity']  ?>)</span> </p>
        <?php
            }
        } else {
            echo '<p class="empty">Your cart is empty</p>';
        }
        ?>
        <div class="grand-total">Grand Total: <span>₱<?php echo $grand_total; ?>/-</span></div>
    </section>

    <section class="checkout">

        <form action="" method="POST">

            <h3>Place Your Order</h3>

            <div class="flex">
                <div class="inputBox">
                    <span>Your First Name:</span>
                    <input type="text" name="first_name" placeholder="Enter your first name" required>
                </div>
                <div class="inputBox">
                    <span>Your Last Name:</span>
                    <input type="text" name="last_name" placeholder="Enter your last name" required>
                </div>
                <div class="inputBox">
                    <span>Your Number:</span>
                    <input type="number" name="number" min="0" placeholder="Enter your number" required>
                </div>
                <div class="inputBox">
                    <span>Your Email:</span>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="inputBox">
                    <span>Payment Method:</span>
                    <select name="method" required>
                        <option value="cash on delivery">Cash on Delivery</option>
                        <option value="credit card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="paytm">Paytm</option>
                    </select>
                </div>
                <div class="inputBox">
                    <span>Address Line 01:</span>
                    <input type="text" name="flat" placeholder="e.g. Flat No." required>
                </div>
                <div class="inputBox">
                    <span>Address Line 02:</span>
                    <input type="text" name="street" placeholder="e.g. Street Name" required>
                </div>
                <div class="inputBox">
                    <span>City:</span>
                    <input type="text" name="city" placeholder="e.g. Butuan" required>
                </div>
                <div class="inputBox">
                    <span>Province:</span>
                    <input type="text" name="state" placeholder="e.g. Agusan Del Norte" required>
                </div>
                <div class="inputBox">
                    <span>Country:</span>
                    <input type="text" name="country" placeholder="e.g. Philippines" required>
                </div>
                <div class="inputBox">
                    <span>Pin Code:</span>
                    <input type="number" min="0" name="pin_code" placeholder="e.g. 8600" required>
                </div>
            </div>

            <input type="submit" name="order" value="Order Now" class="btn">

        </form>

    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>