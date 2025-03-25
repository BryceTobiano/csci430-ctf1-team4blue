
<?php
//logout updated for new cookie: connor
// Safely check for the cookie
if (!isset($_COOKIE["user"])) {
    echo "Not logged in";
} else {
    $user = $_COOKIE["user"];
    echo "Logging out user: $user<br>";

    // Unset the cookie by setting expiration in the past
    setcookie("user", "", time()-3600);
    unset($_COOKIE["user"]);
    echo "Logged out";
}
?>
