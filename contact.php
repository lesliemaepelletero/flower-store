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
    $first_name = pg_escape_string($conn, trim($_POST['first_name']));
    $last_name = pg_escape_string($conn, trim($_POST['last_name']));
    $email = pg_escape_string($conn, trim($_POST['email']));
    $number = pg_escape_string($conn, trim($_POST['phone_number']));
    $msg = pg_escape_string($conn, trim($_POST['message']));

    // Prepare and execute the query to check if the message already exists
    $select_message_query = "SELECT * FROM message WHERE first_name = $1 AND last_name = $2 AND email = $3 AND phone_number = $4 AND message = $5";
    $result = pg_query_params($conn, $select_message_query, array($first_name, $last_name, $email, $number, $msg));

    if ($result && pg_num_rows($result) > 0) {
        $message[] = 'Message already sent!';
    } else {
        // Insert the message into the database
        $insert_message_query = "INSERT INTO message(user_id, first_name, last_name, email, phone_number, message) VALUES($1, $2, $3, $4, $5, $6)";
        $insert_result = pg_query_params($conn, $insert_message_query, array($user_id, $first_name, $last_name, $email, $number, $msg));

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
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: #fdf0f5;
            /* Soft pink background */
            color: #444;
        }

        a {
            text-decoration: none;
            color: #ff79a1;
        }

        a:hover {
            color: #ff5a87;
        }



        /* Heading Section */
        .heading {
            background: linear-gradient(135deg, #ffd1e0, #fff6f0);
            padding: 2rem 1rem;
            text-align: center;
        }

        .heading h3 {
            font-size: 4rem;
            color: black;
        }

        .heading p a {
            font-weight: bold;
        }

        /* Contact Section */
        .contact {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 4rem 1rem;
            background: #fff3f8;
        }

        .contact form {
            background: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .contact form h3 {
            font-size: 2.6rem;
            margin-bottom: 1rem;
            color: #ff5a87;
        }

        .contact form .box {
            width: 100%;
            padding: 0.8rem;
            margin: 0.8rem 0;
            border: 1px solid #ffccd5;
            border-radius: 25px;
            background: #fff6f0;
            color: #555;
            font-size: 1.5rem;
        }

        .contact form .box:focus {
            outline: none;
            border-color: #ff79a1;
            background: #ffe4f3;
        }

        .contact form textarea {
            resize: none;
        }

        .contact form .btn {
            background: #ff79a1;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .contact form .btn:hover {
            background: #ff5a87;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .contact form {
                padding: 1.5rem;
            }
        }
    </style>

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
            <input type="text" name="first_name" placeholder="Enter your first name" class="box" required>
            <input type="text" name="last_name" placeholder="Enter your last name" class="box" required>
            <input type="email" name="email" placeholder="Enter your email" class="box" required>
            <input type="number" name="phone_number" placeholder="Enter your number" class="box" required>
            <textarea name="message" class="box" placeholder="Enter your message" required cols="30" rows="10"></textarea>
            <input type="submit" value="Send Message" name="send" class="btn">
        </form>
    </section>

    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>