
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
    $instructions = trim($_POST["instructions"] ?? "");

    if (!$movingID || $instructions === "") {
        $message = "Please select a moving event and enter instructions.";
    } else {
        // Verify the event belongs to an assigned client.
        $stmt = mysqli_prepare($conn,
            "SELECT me.MovingID
             FROM MovingEvents me
             INNER JOIN RepresentativeClients rc
                 ON me.ClientID = rc.ClientID
             WHERE me.MovingID = ? AND rc.RepID = ?"
        );

        mysqli_stmt_bind_param($stmt, "ii", $movingID, $repID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $authorized = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);

        if (!$authorized) {
            $message = "This moving event is not assigned to you.";
        } else {
            // Add instructions or update existing instructions.
            $stmt = mysqli_prepare($conn,
                "INSERT INTO SpecialInstructions
                 (MovingID, Instructions)
                 VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE
                 Instructions = VALUES(Instructions)"
            );

            mysqli_stmt_bind_param(
                $stmt, "is", $movingID, $instructions
            );

            try {
                mysqli_stmt_execute($stmt);
                $message = "Moving services request saved successfully!";
                $success = true;
            } catch (mysqli_sql_exception $e) {
                $message = "Unable to save moving services request.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}

// Retrieve assigned moving events.
$stmt = mysqli_prepare($conn,
    "SELECT DISTINCT
            me.MovingID,
            me.MovingDate,
            c.FirstName,
            c.LastName,
            si.Instructions
     FROM MovingEvents me
     INNER JOIN Clients c
         ON me.ClientID = c.ClientID
     INNER JOIN RepresentativeClients rc
         ON c.ClientID = rc.ClientID
     LEFT JOIN SpecialInstructions si
         ON me.MovingID = si.MovingID
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
<title>Moving Services</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f8f1ff;
        padding: 25px;
    }
    .container {
        background: white;
        max-width: 700px;
        margin: auto;
        padding: 30px;
        border-radius: 15px;
    }
    h1 { color: #7436a6; text-align: center; }
    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
    }
    select, textarea {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        box-sizing: border-box;
    }
    textarea { min-height: 130px; }
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
    .message {
        font-weight: bold;
        text-align: center;
    }
    .success { color: green; }
    .error { color: #b00020; }
</style>
</head>
<body>
    <?php include "navbar.php"; ?>
<div class="container">
    <h1>Moving Services</h1>

    <p>
        Request moving services, add special instructions,
        or update an existing moving services request.
    </p>

    <?php if ($message !== ""): ?>
        <p class="message <?= $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Select Moving Event</label>
        <select name="movingID" required>
            <option value="">Choose a moving event</option>

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

        <label>Moving Services / Special Instructions</label>
        <textarea name="instructions"
                  placeholder="Example: Two movers needed, packing assistance, fragile furniture, elevator access..."
                  required></textarea>

        <button type="submit">Save Moving Services Request</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
