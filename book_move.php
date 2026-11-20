
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
    $movingID = filter_input(INPUT_POST, "movingID", FILTER_VALIDATE_INT);
    $clientID = filter_input(INPUT_POST, "clientID", FILTER_VALIDATE_INT);

    $streetNumber = trim($_POST["streetNumber"] ?? "");
    $streetName = trim($_POST["streetName"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = strtoupper(trim($_POST["state"] ?? ""));
    $zipCode = trim($_POST["zipCode"] ?? "");
    $movingDate = trim($_POST["movingDate"] ?? "");

    $weight = filter_input(INPUT_POST, "weight", FILTER_VALIDATE_FLOAT);
    $miles = filter_input(INPUT_POST, "miles", FILTER_VALIDATE_FLOAT);
    $cost = filter_input(INPUT_POST, "cost", FILTER_VALIDATE_FLOAT);

    $date = DateTimeImmutable::createFromFormat("!Y-m-d", $movingDate);

    if (!$movingID || !$clientID ||
        $streetNumber === "" || $streetName === "" ||
        $city === "" || !preg_match('/^[A-Z]{2}$/', $state) ||
        $zipCode === "" ||
        !$date || $date->format("Y-m-d") !== $movingDate ||
        $date < new DateTimeImmutable("today") ||
        $weight === false || $weight <= 0 ||
        $miles === false || $miles < 0 ||
        $cost === false || $cost < 0) {

        $message = "Please enter valid moving information.";

    } else {
        // Confirm this client belongs to the logged-in representative.
        $check = mysqli_prepare($conn,
            "SELECT ClientID
             FROM RepresentativeClients
             WHERE RepID = ? AND ClientID = ?"
        );

        mysqli_stmt_bind_param($check, "ii", $repID, $clientID);
        mysqli_stmt_execute($check);
        $assigned = mysqli_stmt_get_result($check);
        $isAssigned = mysqli_num_rows($assigned) > 0;
        mysqli_stmt_close($check);

        if (!$isAssigned) {
            $message = "This client is not assigned to your account.";
        } else {
            $sql = "INSERT INTO MovingEvents
                    (MovingID, ClientID, StreetNumber, StreetName,
                     City, State, ZipCode, MovingDate,
                     TotalWeight, Miles, TotalCost)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt, "iissssssddd",
                $movingID, $clientID,
                $streetNumber, $streetName, $city,
                $state, $zipCode, $movingDate,
                $weight, $miles, $cost
            );

            try {
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Moving event booked successfully!";
                    $success = true;
                } else {
                    $message = "Unable to book moving event.";
                }
            } catch (mysqli_sql_exception $e) {
                $message = "Unable to book. Check whether the Moving ID already exists.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}

// Retrieve only clients assigned to this representative.
$stmt = mysqli_prepare($conn,
    "SELECT c.ClientID, c.FirstName, c.LastName
     FROM Clients c
     INNER JOIN RepresentativeClients rc
         ON c.ClientID = rc.ClientID
     WHERE rc.RepID = ?
     ORDER BY c.LastName, c.FirstName"
);

mysqli_stmt_bind_param($stmt, "i", $repID);
mysqli_stmt_execute($stmt);
$clients = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Book Moving Event</title>
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
    input, select {
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
    .message { font-weight: bold; text-align: center; }
    .success { color: green; }
    .error { color: #b00020; }
</style>
</head>
<body>
    <?php include "navbar.php"; ?>
<div class="container">
    <h1>Book a Moving Event</h1>

    <?php if ($message !== ""): ?>
        <p class="message <?= $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Moving ID</label>
        <input type="number" name="movingID" min="1" required>

        <label>Client</label>
        <select name="clientID" required>
            <option value="">Select a client</option>
            <?php while ($client = mysqli_fetch_assoc($clients)): ?>
                <option value="<?= (int)$client["ClientID"] ?>">
                    <?= htmlspecialchars(
                        $client["FirstName"] . " " .
                        $client["LastName"] . " (" .
                        $client["ClientID"] . ")"
                    ) ?>
                </option>
            <?php endwhile; ?>
        </select>

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

        <label>Moving Date</label>
        <input type="date" name="movingDate" required>

        <label>Total Weight (lbs)</label>
        <input type="number" name="weight" step="0.01" min="0.01" required>

        <label>Miles</label>
        <input type="number" name="miles" step="0.01" min="0" required>

        <label>Total Cost ($)</label>
        <input type="number" name="cost" step="0.01" min="0" required>

        <button type="submit">Book Moving Event</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
