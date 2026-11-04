
<?php
session_start();

// Remove all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Return the student to the login page
header("Location: login.php");
exit;
?>

