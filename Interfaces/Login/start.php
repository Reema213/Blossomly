<?php
// Reema Aljaber
session_start();
session_unset();
session_destroy();

// Clear past purchases cookie
setcookie("past_purchases", "", time() - 3600, "/");

// Clear order history cookie
setcookie("order_history", "", time() - 3600, "/");

header("Location: ../Home_Page/Home.php");
exit();
?>