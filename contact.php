<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_POST['send'])) {
    // Sanitize and validate user inputs
    $name = pg_escape_string($conn, trim($_POST['name']));
    $email = pg_escape_string($conn, trim($_POST['email']));
    $number = pg_escape_string($conn, trim($_POST['number']));
    $msg = pg_escape_string($conn, trim($_POST['message']));

    // Prepare and execute the query to check if the message already exists
    $select_message_query = "SELECT * FROM message WHERE name = $1 AND email = $2 AND number = $3 AND message = $4";
    $result = pg_query_params($conn, $select_message_query, array($name, $email, $number, $msg));

    if (pg_num_rows($result) > 0) {
        $message[] = 'Message already sent!';
    } else {
        // Insert the message into the database
        $insert_message_query = "INSERT INTO message(user_id, name, email, number, message) VALUES($1, $2, $3, $4, $5)";
        $insert_result = pg_query_params($conn, $insert_message_query, array($user_id, $name, $email, $number, $msg));

        if ($insert_result) {
            $message[] = 'Message sent successfully!';
        } else {
            $message[] = 'Error sending message!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom admin CSS file link -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php @include 'header.php'; ?>

    <section class="heading">
        <h3>Contact Us</h3>
        <p> <a href="home.php">Home</a> / Contact </p>
    </section>

    <section class="contact">
        <form action="" method="POST">
            <h3>Send Us a Message!</h3>
            <input type="text" name="name" placeholder="Enter your name" class="box" required>
            <input type="email" name="email" placeholder="Enter your email" class="box" required>
            <input type="number" name="number" placeholder="Enter your number" class="box" required>
            <textarea name="message" class="box" placeholder="Enter your message" required cols="30" rows="10"></textarea>
            <input type="submit" value="Send Message" name="send" class="btn">
        </form>
    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>