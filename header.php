<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>


<header class="header">
    <div class="flex">
        <a href="home.php" class="logo">Blooming Blossoms</a>
        <nav class="navbar">
            <ul>
                <li><a href="home.php">home</a></li>
                <li><a href="#">pages +</a>
                    <ul>
                        <li><a href="about.php">about</a></li>
                        <li><a href="contact.php">contact</a></li>
                    </ul>
                </li>
                <li><a href="shop.php">shop</a></li>
                <li><a href="orders.php">orders</a></li>
                <li><a href="#">account +</a>
                    <ul>
                        <li><a href="login.php">login</a></li>
                        <li><a href="register.php">register</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="search_page.php" class="fas fa-search"></a>
            <div id="user-btn" class="fas fa-user"></div>
            <?php
            $select_wishlist_count = pg_query_params($conn, "SELECT COUNT(*) AS count FROM wishlist WHERE user_id = $1", array($user_id));
            $wishlist_num_rows = pg_fetch_result($select_wishlist_count, 0, 'count');
            ?>
            <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?php echo $wishlist_num_rows; ?>)</span></a>
            <?php
            $select_cart_count = pg_query_params($conn, "SELECT COUNT(*) AS count FROM cart WHERE user_id = $1", array($user_id));
            $cart_num_rows = pg_fetch_result($select_cart_count, 0, 'count');
            ?>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?php echo $cart_num_rows; ?>)</span></a>
        </div>
        <div class="account-box">
            <p>username : <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></p>
            <p>email : <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span></p>
            <a href="logout.php" class="delete-btn">logout</a>
        </div>
    </div>
</header>