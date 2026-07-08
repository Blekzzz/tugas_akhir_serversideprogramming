<?php
session_start();

// Clear all session values
$_SESSION = array();

// Destroy the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Obliterate the session data on the server
session_destroy();

// Redirect back to the login landing page
header("Location: index.php");
exit;