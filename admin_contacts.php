<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Deleting a message
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    pg_query($conn, "DELETE FROM message WHERE id = '$delete_id'") or die('Query failed');
    header('location:admin_contacts.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php @include 'admin_header.php'; ?>

    <section class="messages">

        <h1 class="title">Messages</h1>

        <div class="box-container">
            <?php
            // Query all messages using the table-valued function
            $query = "SELECT * FROM message";
            $select_message = pg_query($conn, $query) or die('Query failed');

            if (pg_num_rows($select_message) > 0) {
                while ($fetch_message = pg_fetch_assoc($select_message)) {
            ?>
                    <div class="box">
                        <p>User ID: <span><?php echo $fetch_message['user_id']; ?></span></p>
                        <p>First Name: <span><?php echo $fetch_message['first_name']; ?></span></p>
                        <p>Last Name: <span><?php echo $fetch_message['last_name']; ?></span></p>
                        <p>Phone Number: <span><?php echo $fetch_message['phone_number']; ?></span></p>
                        <p>Email: <span><?php echo $fetch_message['email']; ?></span></p>
                        <p>Message: <span><?php echo $fetch_message['message']; ?></span></p>
                        <a href="admin_contacts.php?delete=<?php echo $fetch_message['id']; ?>" onclick="return confirm('Delete this message?');" class="delete-btn">Delete</a>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">You have no messages!</p>';
            }
            ?>
        </div>

    </section>

    <script src="js/admin_script.js"></script>

</body>

</html>
