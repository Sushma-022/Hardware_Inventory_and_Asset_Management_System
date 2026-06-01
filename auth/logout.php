<?php
session_start();

/* destroy session completely */
$_SESSION = [];
session_unset();
session_destroy();

/* destroy session cookie (IMPORTANT) */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

/* redirect */
header("Location: /hardware/index.html?logout=1");
exit;
?>