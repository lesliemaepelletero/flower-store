<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

    <?php @include 'admin_header.php'; ?>

    <section class="dashboard">

        <h1 class="title">dashboard</h1>

        <div class="box-container">

            <!-- Total Pending Orders -->
            <div class="box">
    <?php
    // Query for pending payments
    $select_pendings = pg_query($conn, "SELECT total_revenue FROM orders_summary WHERE payment_status = 'pending'");

    if ($select_pendings) {
        $fetch_pendings = pg_fetch_assoc($select_pendings);
        $total_pendings = $fetch_pendings['total_revenue'] ?? 0; // Default to 0 if null
    } else {
        // Query failed, log error for debugging
        error_log('Query failed for pending payments: ' . pg_last_error($conn));
        $total_pendings = 0; // Default value for display
    }
    ?>
    <h3>$<?php echo $total_pendings; ?>/-</h3>
    <p>total pendings</p>
</div>

<div class="box">
    <?php
    // Query for completed payments
    $select_completes = pg_query($conn, "SELECT total_revenue FROM orders_summary WHERE payment_status = 'completed'");

    if ($select_completes) {
        $fetch_completes = pg_fetch_assoc($select_completes);
        $total_completes = $fetch_completes['total_revenue'] ?? 0; // Default to 0 if null
    } else {
        // Query failed, log error for debugging
        error_log('Query failed for completed payments: ' . pg_last_error($conn));
        $total_completes = 0; // Default value for display
    }
    ?>
    <h3>$<?php echo $total_completes; ?>/-</h3>
    <p>completed payments</p>
</div>


            <!-- Total Orders Placed -->
            <div class="box">
                <?php
                $select_orders = pg_query($conn, "SELECT * FROM orders") or die('query failed');
                $number_of_orders = pg_num_rows($select_orders);
                ?>
                <h3><?php echo $number_of_orders; ?></h3>
                <p>orders placed</p>
            </div>

            <!-- Total Products Added -->
            <div class="box">
                <?php
                $select_products = pg_query($conn, "SELECT * FROM products") or die('query failed');
                $number_of_products = pg_num_rows($select_products);
                ?>
                <h3><?php echo $number_of_products; ?></h3>
                <p>products added</p>
            </div>

            <!-- Total Users -->
            <div class="box">
                <?php
                $select_users = pg_query($conn, "SELECT * FROM users WHERE user_type = 'user'") or die('query failed');
                $number_of_users = pg_num_rows($select_users);
                ?>
                <h3><?php echo $number_of_users; ?></h3>
                <p>normal users</p>
            </div>

            <!-- Total Admins -->
            <div class="box">
                <?php
                $select_admin = pg_query($conn, "SELECT * FROM users WHERE user_type = 'admin'") or die('query failed');
                $number_of_admin = pg_num_rows($select_admin);
                ?>
                <h3><?php echo $number_of_admin; ?></h3>
                <p>admin users</p>
            </div>

            <!-- Total Accounts -->
            <div class="box">
                <?php
                $select_account = pg_query($conn, "SELECT * FROM users") or die('query failed');
                $number_of_account = pg_num_rows($select_account);
                ?>
                <h3><?php echo $number_of_account; ?></h3>
                <p>total accounts</p>
            </div>

            <!-- Total New Messages -->
            <div class="box">
                <?php
                $select_messages = pg_query($conn, "SELECT * FROM message") or die('query failed');
                $number_of_messages = pg_num_rows($select_messages);
                ?>
                <h3><?php echo $number_of_messages; ?></h3>
                <p>new messages</p>
            </div>

        </div>

    </section>

    <script src="js/admin_script.js"></script>

</body>

</html>
