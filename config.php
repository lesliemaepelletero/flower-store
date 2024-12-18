<?php

$conn = pg_connect("host=localhost dbname=shop_db user=postgres password=admin")
    or die("Connection failed: " . pg_last_error());
