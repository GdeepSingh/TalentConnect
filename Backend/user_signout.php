<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to company login page
header("Location: ../Frontend/User/login.html");
exit;
?>
