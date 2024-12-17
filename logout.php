<?php
session_start();
session_destroy(); // Destroy the session
header('Location: login.html'); // Redirect to Sign-In page
exit;
?>
