<?php

@include 'config.php';

session_start();

if (isset($_POST['submit'])) {

   // Sanitize and escape input
   $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $email = pg_escape_string($conn, $filter_email);
   $filter_pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
   $pass = pg_escape_string($conn, md5($filter_pass));

   // Prepare the query
   $query = "SELECT * FROM users WHERE email = '$email' AND password = '$pass'";
   $result = pg_query($conn, $query);

   if (!$result) {
       die("Query failed: " . pg_last_error($conn));
   }

   if (pg_num_rows($result) > 0) {

      $row = pg_fetch_assoc($result);
      $user_id = $row['id']; // Fetch user ID for logging

      if ($row['user_type'] == 'admin') {
         $_SESSION['admin_name'] = $row['name'];
         $_SESSION['admin_email'] = $row['email'];
         $_SESSION['admin_id'] = $row['id'];

         // Log admin login action
         $action = pg_escape_string($conn, 'Login');
         $description = pg_escape_string($conn, 'Admin logged in successfully');
         $log_query = "INSERT INTO activity_log (user_id, action, description) VALUES ($user_id, '$action', '$description')";
         $log_result = pg_query($conn, $log_query);

         if (!$log_result) {
             error_log("Activity log insert failed: " . pg_last_error($conn));
         }

         header('location:admin_page.php');
      } elseif ($row['user_type'] == 'user') {
         $_SESSION['user_name'] = $row['name'];
         $_SESSION['user_email'] = $row['email'];
         $_SESSION['user_id'] = $row['id'];

         // Log user login action
         $action = pg_escape_string($conn, 'Login');
         $description = pg_escape_string($conn, 'User logged in successfully');
         $log_query = "INSERT INTO activity_log (user_id, action, description) VALUES ($user_id, '$action', '$description')";
         $log_result = pg_query($conn, $log_query);

         if (!$log_result) {
             error_log("Activity log insert failed: " . pg_last_error($conn));
         }

         header('location:home.php');
      } else {
         $message[] = 'User type is invalid!';
      }
   } else {
      $message[] = 'Incorrect email or password!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
      }
   }
   ?>

   <section class="form-container">
      <form action="" method="post">
         <h3>Login Now</h3>
         <input type="email" name="email" class="box" placeholder="Enter your email" required>
         <input type="password" name="pass" class="box" placeholder="Enter your password" required>
         <input type="submit" class="btn" name="submit" value="Login Now">
         <p>Don't have an account? <a href="register.php">Register now</a></p>
      </form>
   </section>

</body>

</html>
