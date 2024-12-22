<?php

@include 'config.php';

if (isset($_POST['submit'])) {

    $filter_name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $name = pg_escape_string($conn, $filter_name);
    $filter_email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $email = pg_escape_string($conn, $filter_email);
    $filter_pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
    $pass = pg_escape_string($conn, md5($filter_pass));
    $filter_cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);
    $cpass = pg_escape_string($conn, md5($filter_cpass));
    $filter_region = filter_var($_POST['region'], FILTER_SANITIZE_STRING);
    $region = pg_escape_string($conn, $filter_region);
    $filter_province = filter_var($_POST['province'], FILTER_SANITIZE_STRING);
    $province = pg_escape_string($conn, $filter_province);
    $filter_city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
    $city = pg_escape_string($conn, $filter_city);
    $filter_barangay = filter_var($_POST['barangay'], FILTER_SANITIZE_STRING);
    $barangay = pg_escape_string($conn, $filter_barangay);
    $user_type = $_POST['user_type']; // Add user type (admin/user)

    // Check if the user already exists
    $select_users = pg_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (pg_num_rows($select_users) > 0) {
        $message[] = 'user already exists!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'confirm password does not match!';
        } else {
            // Insert new user into the database
            $insert_user = pg_query($conn, "INSERT INTO users (name, email, password, region, province, city, barangay, user_type) 
                                         VALUES ('$name', '$email', '$pass', '$region', '$province', '$city', '$barangay', '$user_type')");

            if ($insert_user) {
                $message[] = 'registered successfully!';
                header('location:login.php');
            } else {
                $message[] = 'registration failed!';
            }
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
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container {
            display: flex;
            justify-content: flex-end;
            padding: 20px;
            background: url('images/bggg.png') no-repeat center/cover;
        }

        form {
            width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            /* Adjusted height */
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* Centers content vertically */
            align-items: center;
            /* Centers content horizontally */
            background: linear-gradient(135deg, #ffd1e0, #fff6f0);
        }

        .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #7e2a53;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #ba71a2;
        }
    </style>
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
        <form action="" method="post" id="registrationForm">
            <h3>register now</h3>
            <input type="text" name="name" class="box" placeholder="username" required>
            <input type="email" name="email" class="box" placeholder="email" required>
            <input type="password" name="pass" class="box" placeholder="enter password" required>
            <input type="password" name="cpass" class="box" placeholder="confirm password" required>

            <!-- Dropdown for Region with onchange event -->
            <select name="region" id="region" class="box" required onchange="fetch_provinces()">
                <option value="">Select Region</option>
            </select>

            <!-- Dropdown for Province with onchange event -->
            <select name="province" id="province" class="box" required onchange="fetch_cities()">
                <option value="">Select Province</option>
            </select>

            <!-- Dropdown for City with onchange event -->
            <select name="city" id="city" class="box" required onchange="fetch_barangays()">
                <option value="">Select City</option>
            </select>

            <!-- Dropdown for Barangay -->
            <select name="barangay" id="barangay" class="box" required>
                <option value="">Select Barangay</option>
            </select>


            <!-- Checkbox for Terms and Conditions -->
            <label for="terms" style="display: block; margin-top: 10px;">
                <input type="checkbox" id="terms" name="terms" required>
                I agree to the <a href="terms_and_conditions.html" target="_blank">Terms and Conditions</a>
            </label>

            <input type="submit" class="btn" name="submit" value="register now">
            <p>already have an account? <a href="login.php">login now</a></p>
        </form>
    </section>

    <script>
        // The provided JavaScript code
        fetch(`https://psgc.gitlab.io/api/regions/`)
            .then(response => response.json())
            .then(data => {
                const regionsSelect = document.getElementById('region');
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.code;
                    option.textContent = region.name;
                    regionsSelect.appendChild(option);
                });
            });

        function fetch_provinces() {
            const region = document.getElementById('region').value;
            fetch(`https://psgc.gitlab.io/api/regions/` + region + `/provinces/`)
                .then(response => response.json())
                .then(data => {
                    const provincesSelect = document.getElementById('province');
                    provincesSelect.innerHTML = '<option value="">Select Province</option>';
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.code;
                        option.textContent = province.name;
                        provincesSelect.appendChild(option);
                    });
                });
        }

        function fetch_cities() {
            const province = document.getElementById('province').value;
            fetch(`https://psgc.gitlab.io/api/provinces/` + province + `/cities-municipalities/`)
                .then(response => response.json())
                .then(data => {
                    const citiesSelect = document.getElementById('city');
                    citiesSelect.innerHTML = '<option value="">Select City</option>';
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.code;
                        option.textContent = city.name;
                        citiesSelect.appendChild(option);
                    });
                });
        }

        function fetch_barangays() {
            const city = document.getElementById('city').value;
            fetch(`https://psgc.gitlab.io/api/cities-municipalities/` + city + `/barangays/`)
                .then(response => response.json())
                .then(data => {
                    const barangaysSelect = document.getElementById('barangay');
                    barangaysSelect.innerHTML = '<option value="">Select Barangay</option>';
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.code;
                        option.textContent = barangay.name;
                        barangaysSelect.appendChild(option);
                    });
                });
        }

        // JavaScript function to validate the terms and conditions checkbox
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            const termsCheckbox = document.getElementById('terms');
            if (!termsCheckbox.checked) {
                event.preventDefault(); // Prevent form submission
                alert("Please agree to the Terms and Conditions.");
            }
        });
    </script>

</body>

</html>