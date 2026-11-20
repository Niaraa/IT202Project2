
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

    if (!$movingID) {
        $message = "Please select a valid moving event.";
    } else {
        $stmt = mysqli_prepare($conn,
            "DELETE me FROM MovingEvents me
             INNER JOIN RepresentativeClients rc
                 ON me.ClientID = rc.ClientID
             WHERE me.MovingID = ? AND rc.RepID = ?"
        );

        mysqli_stmt_bind_param($stmt, "ii", $movingID, $repID);

        try {
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "Moving event canceled successfully!";
                $success = true;
            } else {
                $message = "Moving event not found or not assigned to you.";
            }
        } catch (mysqli_sql_exception $e) {
            $message = "Unable to cancel this moving event.";
        }

        mysqli_stmt_close($stmt);
    }
}

$stmt = mysqli_prepare($conn,
    "SELECT DISTINCT me.MovingID, me.MovingDate,
            c.FirstName, c.LastName
     FROM MovingEvents me
     INNER JOIN Clients c ON me.ClientID = c.ClientID
     INNER JOIN RepresentativeClients rc
         ON c.ClientID = rc.ClientID
     WHERE rc.RepID = ?
     ORDER BY me.MovingDate"
);

mysqli_stmt_bind_param($stmt, "i", $repID);
mysqli_stmt_execute($stmt);
$events = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cancel Moving Event</title>
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
    label { display: block; margin-top: 15px; font-weight: bold; }
    select {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
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
    <h1>Cancel a Moving Event</h1>

    <?php if ($message !== ""): ?>
        <p class="message <?= $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST"
          onsubmit="return confirm('Are you sure you want to cancel this moving event?');">

        <label>Select Moving Event</label>
        <select name="movingID" required>
            <option value="">Choose an event</option>
            <?php while ($event = mysqli_fetch_assoc($events)): ?>
                <option value="<?= (int)$event["MovingID"] ?>">
                    <?= htmlspecialchars(
                        $event["FirstName"] . " " .
                        $event["LastName"] . " - " .
                        $event["MovingDate"] . " (ID: " .
                        $event["MovingID"] . ")"
                    ) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Cancel Moving Event</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
