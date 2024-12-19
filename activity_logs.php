<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php @include 'admin_header.php'; ?>

    <section class="activity-logs">
        <h1 class="title">Activity Logs</h1>



        <table class="styled-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>User ID</th>
                    <th>Action</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM activity_log ORDER BY action_time DESC";

                // Apply filtering if form is submitted
                if (isset($_POST['filter_logs'])) {
                    $user_id = $_POST['user_id'];
                    $action = $_POST['action'];

                    $query = "SELECT * FROM activity_log WHERE 1=1";

                    if (!empty($user_id)) {
                        $query .= " AND user_id = " . pg_escape_literal($conn, $user_id);
                    }

                    if (!empty($action)) {
                        $query .= " AND action ILIKE '%" . pg_escape_string($conn, $action) . "%'";
                    }

                    $query .= " ORDER BY action_time DESC";
                }

                $logs = pg_query($conn, $query) or die('Query failed');
                while ($log = pg_fetch_assoc($logs)) {
                    echo "<tr>
                  <td>{$log['action_time']}</td>
                  <td>{$log['user_id']}</td>
                  <td>{$log['action']}</td>
                  <td>{$log['description']}</td>
               </tr>";
                }
                ?>
            </tbody>
        </table>

    </section>

    <script src="js/admin_script.js"></script>

</body>

</html>

<style>
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 16px;
        text-align: left;
    }

    .styled-table thead tr {
        background-color: #009879;
        color: #ffffff;
        text-align: left;
        font-weight: bold;
    }

    .styled-table th,
    .styled-table td {
        border: 1px solid #dddddd;
        padding: 12px 15px;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .styled-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #009879;
    }

    .styled-table tbody tr:hover {
        background-color: #f1f1f1;
    }
</style>