<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>orders</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
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
    </style>

</head>

<body>

    <?php @include 'header.php'; ?>

    <section class="heading">
        <h3>your orders</h3>
        <p> <a href="home.php">home</a> / order </p>
    </section>

    <section class="placed-orders">

        <h1 class="title">placed orders</h1>

        <div class="box-container">

            <?php
            // Query the user_orders view filtered by the logged-in user's user_id
            $select_orders = pg_query($conn, "SELECT * FROM user_orders WHERE user_id = '$user_id'");

            if (pg_num_rows($select_orders) > 0) {
                while ($fetch_orders = pg_fetch_assoc($select_orders)) {
            ?>
                    <div class="box">
                        <p> Order ID: <span><?php echo $fetch_orders['order_id']; ?></span> </p>
                        <p> Placed on: <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
                        <p> Name: <span><?php echo $fetch_orders['user_name']; ?></span> </p>
                        <p> Email: <span><?php echo $fetch_orders['user_email']; ?></span> </p>
                        <p> Total price: <span> ₱<?php echo $fetch_orders['total_price']; ?>/-</span> </p>
                        <p> Payment status: <span style="color:<?php echo $fetch_orders['payment_status'] == 'pending' ? 'tomato' : 'green'; ?>">
                                <?php echo $fetch_orders['payment_status']; ?>
                            </span> </p>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">No orders placed yet!</p>';
            }
            ?>
        </div>

    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>