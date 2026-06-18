<?php
session_start();

// Unset all active student session matrices
$_SESSION = array();

// Completely destroy the cookie session footprint
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session container locally
session_destroy();

// Redirect instantly back to your student login page
header("Location: login.php");
exit();
?>