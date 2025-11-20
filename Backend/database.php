<?php
    $db_server = 'localhost'; //server name
    $db_user = "root"; // username
    $db_pass = ""; // pass
    $db_name = "talentconnect"; // database name
    $conn = "";

    // instead of printing big error messages to the user we can use exception try catch block to handle the disconntection
    try{
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    }catch(mysqli_sql_exception){
        echo "<script defer>alert('error');</script>";
    }
?>