
<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$firstName = trim($_POST["firstName"] ?? "");
$lastName = trim($_POST["lastName"] ?? "");
$password = $_POST["password"] ?? "";
$repID = trim($_POST["repID"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$email = trim($_POST["email"] ?? "");
$emailConfirm = isset($_POST["emailConfirm"]);
$transaction = $_POST["transaction"] ?? "";

if ($firstName === "" || $lastName === "" ||
    $password === "" || $repID === "" ||
    $phone === "" || $transaction === "") {
    die("Please complete all required fields.");
}

if ($emailConfirm && $email === "") {
    die("Email is required when email confirmation is checked.");
}

$sql = "SELECT RepID, FirstName, LastName,
               RepPassword, Phone, Email
        FROM MovingRepresentatives
        WHERE RepID = ? AND FirstName = ? AND LastName = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $repID, $firstName, $lastName);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$rep = mysqli_fetch_assoc($result);


if (!$rep) {
    die("CHECK 1 FAILED: Representative information could not be verified.");
}

if (!password_verify($password, $rep["RepPassword"])) {
    $hash = trim($rep["RepPassword"]);

    if (strlen($hash) < 60) {
        die("CHECK 2 FAILED: Stored password hash is too short (" .
            strlen($hash) . " characters).");
    }

    die("CHECK 2 FAILED: Representative information could not be verified.");
}

$enteredPhone = preg_replace(
    '/\D/',
    '',
    preg_split('/\s+ext\.?\s*/i', $phone)[0]
);

$storedPhone = preg_replace('/\D/', '', $rep["Phone"]);

if ($enteredPhone !== $storedPhone) {
    die("CHECK 3 FAILED: Representative information could not be verified.");
}

if ($emailConfirm &&
    strcasecmp($email, $rep["Email"]) !== 0) {
    die("CHECK 4 FAILED: Representative information could not be verified.");
}







session_regenerate_id(true);
$_SESSION["repID"] = $rep["RepID"];
$_SESSION["repName"] = $rep["FirstName"] . " " . $rep["LastName"];
$_SESSION["transaction"] = $transaction;

header("Location: dashboard.php");
exit;
?>
