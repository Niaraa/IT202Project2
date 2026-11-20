
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["repID"])) {
    header("Location: index.php");
    exit;
}

$repID = (int) $_SESSION["repID"];
$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $clientID = filter_input(INPUT_POST, "clientID", FILTER_VALIDATE_INT);
    $firstName = trim($_POST["firstName"] ?? "");
    $lastName = trim($_POST["lastName"] ?? "");
    $streetNumber = trim($_POST["streetNumber"] ?? "");
    $streetName = trim($_POST["streetName"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = strtoupper(trim($_POST["state"] ?? ""));
    $zipCode = trim($_POST["zipCode"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if (!$clientID || $firstName === "" || $lastName === "" ||
        $streetNumber === "" || $streetName === "" ||
        $city === "" || !preg_match('/^[A-Z]{2}$/', $state) ||
        $zipCode === "" || $phone === "") {
        $message = "Please complete all fields correctly.";
    } else {
        try {
            mysqli_begin_transaction($conn);

            $stmt = mysqli_prepare($conn,
                "INSERT INTO Clients
                 (ClientID, FirstName, LastName)
                 VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param(
                $stmt, "iss", $clientID, $firstName, $lastName
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn,
                "INSERT INTO ClientPersonalInfo
                 (ClientID, StreetNumber, StreetName,
                  City, State, ZipCode, Phone)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param(
                $stmt, "issssss",
                $clientID, $streetNumber, $streetName,
                $city, $state, $zipCode, $phone
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn,
                "INSERT INTO RepresentativeClients
                 (RepID, ClientID)
                 VALUES (?, ?)"
            );
            mysqli_stmt_bind_param(
                $stmt, "ii", $repID, $clientID
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_commit($conn);
            $success = true;
            $message = "Client account created successfully!";

        } catch (mysqli_sql_exception $e) {
            mysqli_rollback($conn);
            $message = "Unable to create client. The Client ID may already exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Client Account</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f8f1ff;
        padding: 25px;
    }
    .container {
        background: white;
        max-width: 650px;
        margin: auto;
        padding: 30px;
        border-radius: 15px;
    }
    h1 { color: #7436a6; text-align: center; }
    label { display: block; margin-top: 12px; font-weight: bold; }
    input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        box-sizing: border-box;
    }
    button, a {
        display: inline-block;
        background: #7436a6;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        margin-top: 20px;
        text-decoration: none;
        cursor: pointer;
    }
    .message { text-align: center; font-weight: bold; }
    .success { color: green; }
    .error { color: #b00020; }
</style>
</head>
<body>
    <?php include "navbar.php"; ?>
<div class="container">
    <h1>Create a New Client Account</h1>

    <?php if ($message !== ""): ?>
        <p class="message <?= $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Client ID</label>
        <input type="number" name="clientID" min="1" required>

        <label>First Name</label>
        <input name="firstName" required>

        <label>Last Name</label>
        <input name="lastName" required>

        <label>Street Number</label>
        <input name="streetNumber" required>

        <label>Street Name</label>
        <input name="streetName" required>

        <label>City</label>
        <input name="city" required>

        <label>State (2 letters)</label>
        <input name="state" maxlength="2" required>

        <label>ZIP Code</label>
        <input name="zipCode" required>

        <label>Phone Number</label>
        <input name="phone" required>

        <button type="submit">Create Client Account</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
