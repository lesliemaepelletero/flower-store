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
        <a href="home.php" class="logo">🌸 Blooming Blossoms</a>
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

<style>
    /* General Header Styling */
    .header {
        background: #fff3f8;
        /* Light pink background */

        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .header .flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header .logo {
        font-family: 'Dancing Script', cursive;
        font-size: 2rem;
        color: #ff79a1;
        text-decoration: none;
    }

    .header .navbar ul {
        display: flex;
        gap: 1.5rem;
        list-style: none;
    }

    .header .navbar ul li {
        position: relative;
    }

    .header .navbar ul li a {
        font-size: 1rem;
        font-weight: 500;
        color: #444;
        text-transform: capitalize;
        transition: color 0.3s ease;
    }

    .header .navbar ul li a:hover {
        color: #ff79a1;
    }

    .header .navbar ul li ul {
        display: none;
        position: absolute;
        background: #ffe4f3;
        border-radius: 8px;
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        list-style: none;
        z-index: 10;
    }

    .header .navbar ul li:hover ul {
        display: block;
    }

    .header .navbar ul li ul li a {
        padding: 0.5rem 1rem;
        display: block;
        color: #444;
    }

    .header .icons {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header .icons a {
        font-size: 1.2rem;
        color: #444;
        position: relative;
        transition: color 0.3s ease;
    }

    .header .icons a:hover {
        color: #ff79a1;
    }

    .header .icons a span {
        position: absolute;
        top: -8px;
        right: -12px;
        background: #ff79a1;
        color: white;
        font-size: 0.8rem;
        border-radius: 50%;
        padding: 0.1rem 0.4rem;
    }

    .header .account-box {
        background: #ffffff;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        position: absolute;
        top: 100%;
        right: 2rem;
        display: none;
        z-index: 20;
    }


    .header .account-box .delete-btn {
        background: #ff79a1;
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-transform: capitalize;
    }
</style>