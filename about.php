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
    <title>about</title>

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

        .option-btn,
        .btn,
        .more-btn {

            text-align: center;
            margin: 0.5rem 0;
            padding: 0.8rem;
            font-size: 1rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s, color 0.3s;
        }
    </style>

</head>

<body>

    <?php @include 'header.php'; ?>

    <section class="heading">
        <h3>about us</h3>
        <p> <a href="home.php">home</a> / about </p>
    </section>

    <section class="about">

        <div class="flex">

            <div class="image">
                <img src="images/about-img-1.png" alt="">
            </div>

            <div class="content">
                <h3>why choose us?</h3>
                <p>Choose us for our commitment to freshness, creativity, and personalized service, ensuring your floral experience with us is always exceptional.</p>
                <a href="shop.php" class="btn">shop now</a>
            </div>

        </div>

        <div class="flex">

            <div class="content">
                <h3>what we provide?</h3>
                <p>we provide exquisite floral arrangements crafted with passion and care to brighten every occasion.</p>
                <a href="contact.php" class="btn">contact us</a>
            </div>

            <div class="image">
                <img src="images/about-img-2.jpg" alt="">
            </div>

        </div>

        <div class="flex">

            <div class="image">
                <img src="images/about-img-3.jpg" alt="">
            </div>

            <div class="content">
                <h3>who we are?</h3>
                <p>We are Blooming Blossoms, a dedicated team of floral enthusiasts committed to bringing beauty and joy into people's lives through our exquisite arrangements and exceptional service.</p>
                <a href="#reviews" class="btn">clients reviews</a>
            </div>

        </div>

    </section>

    <section class="reviews" id="reviews">

        <h1 class="title">client's reviews</h1>

        <div class="box-container">

            <div class="box">
                <img src="images/eun.jpg" alt="">
                <p>"Absolutely delighted with the bouquet I ordered from Bloom Blossoms! The flowers were fresh, vibrant, and beautifully arranged. The delivery was prompt, and the customer service was exceptional. I highly recommend Blooming Blossoms for any floral needs!"</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Eun</h3>
            </div>

            <div class="box">
                <img src="images/kath.jpg" alt="">
                <p>I've been a loyal customer of Bloom Blossoms for years, and they never disappoint! Their attention to detail in their arrangements is unmatched, and the variety of flowers they offer is impressive. Whether it's a special occasion or just brightening up my home, Bloom Blossoms is my go-to flower shop.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Kathryn</h3>
            </div>

            <div class="box">
                <img src="images/franc.jpg" alt="">
                <p>Stumbled upon Blooming Blossoms while searching for a last-minute gift, and I'm so glad I did! Despite the short notice, they were able to create a stunning bouquet that exceeded my expectations. The recipient was thrilled, and I was impressed by the quality and affordability. Thank you, Blooming Blossoms</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Francine</h3>
            </div>

            <div class="box">
                <img src="images/suk.jpg" alt="">
                <p>Beautiful flowers and excellent service every time I visit Blooming Blossoms. It's my favorite spot for picking up a thoughtful gift or treating myself to a little floral indulgence.</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Jong Suk</h3>
            </div>

        </div>

    </section>



    <?php @include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>