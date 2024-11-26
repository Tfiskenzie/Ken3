<?php
session_start();
session_destroy(); // Destroy all session data
header('Location: loginn.php'); // Redirect to login page
exit();
?>
