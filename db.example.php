
<?php
// Example database connection.
// Replace placeholders locally. Never commit real credentials.

$servername = "sql1.njit.edu";
$username = "YOUR_NJIT_UCID";
$password = "YOUR_DATABASE_PASSWORD";
$dbname = "YOUR_DATABASE_NAME";

$conn = mysqli_connect(
    $servername,
    $username,
    $password,
    $dbname
);

if (!$conn) {
    die("Database connection failed.");
}
?>
