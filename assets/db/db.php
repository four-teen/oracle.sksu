<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbase = "oracle_db";

    $conn = mysqli_connect($servername, $username, $password,$dbase);

    if (!$conn) {
        die("Connection failed: ask me" . $conn->connect_error);
    }

?> 
