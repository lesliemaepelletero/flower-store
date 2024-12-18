<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:login.php');
   exit;
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   // Prepare and execute the delete query for PostgreSQL
   $delete_query = "DELETE FROM users WHERE id = $1";
   $delete_result = pg_query_params($conn, $delete_query, array($delete_id));

   if ($delete_result) {
      header('location:admin_users.php');
      exit;
   } else {
      echo "Error deleting user!";
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom admin CSS file link -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>

   <?php @include 'admin_header.php'; ?>

   <section class="users">

      <h1 class="title">User Accounts</h1>

      <div class="box-container">
         <?php
         // Query to select all users from the PostgreSQL database
         $select_users_query = "SELECT * FROM users";
         $result = pg_query($conn, $select_users_query);

         if (pg_num_rows($result) > 0) {
            while ($fetch_users = pg_fetch_assoc($result)) {
         ?>
               <div class="box">
                  <p>user id : <span><?php echo $fetch_users['id']; ?></span></p>
                  <p>username : <span><?php echo $fetch_users['name']; ?></span></p>
                  <p>email : <span><?php echo $fetch_users['email']; ?></span></p>
                  <p>user type : <span style="color:<?php if ($fetch_users['user_type'] == 'admin') {
                                                         echo 'var(--orange)';
                                                      } ?>"><?php echo $fetch_users['user_type']; ?></span></p>
                  <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('Delete this user?');" class="delete-btn">Delete</a>
               </div>
         <?php
            }
         } else {
            echo "No users found.";
         }
         ?>
      </div>

   </section>

   <script src="js/admin_script.js"></script>

</body>

</html>