<?php
session_start();
session_unset();
session_destroy();

// Clear cookies
setcookie("user_id", "", time() - 3600, "/");
setcookie("user_name", "", time() - 3600, "/");

header("Location: login.html");
exit();
?>
