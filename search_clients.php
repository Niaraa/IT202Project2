
<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["repID"])) {
    header("Location: index.php");
    exit;
}

$repID = $_SESSION["repID"];

$sql = "SELECT
            c.ClientID,
            c.FirstName,
            c.LastName,
            p.StreetNumber,
            p.StreetName,
            p.City,
            p.State,
            p.ZipCode,
            p.Phone
        FROM Clients c
        INNER JOIN RepresentativeClients rc
            ON c.ClientID = rc.ClientID
        LEFT JOIN ClientPersonalInfo p
            ON c.ClientID = p.ClientID
        WHERE rc.RepID = ?
        ORDER BY c.LastName, c.FirstName";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $repID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moving On Up - Client Accounts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f1ff;
            padding: 30px;
        }

        h1, h2 {
            color: #7436a6;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #7436a6;
            color: white;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #7436a6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>

<h1>Moving On Up</h1>
<h2>My Assigned Client Accounts</h2>

<table>
    <tr>
        <th>Client ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Address</th>
        <th>Phone</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($client = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars((string)$client["ClientID"]) ?></td>
                <td><?= htmlspecialchars($client["FirstName"]) ?></td>
                <td><?= htmlspecialchars($client["LastName"]) ?></td>
                <td>
                    <?= htmlspecialchars(trim(
                        ($client["StreetNumber"] ?? "") . " " .
                        ($client["StreetName"] ?? "") . ", " .
                        ($client["City"] ?? "") . ", " .
                        ($client["State"] ?? "") . " " .
                        ($client["ZipCode"] ?? "")
                    )) ?>
                </td>
                <td><?= htmlspecialchars($client["Phone"] ?? "") ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="5">No assigned clients found.</td>
        </tr>
    <?php endif; ?>
</table>

<a class="back" href="dashboard.php">Back to Dashboard</a>

</body>
</html>

<?php
mysqli_stmt_close($stmt);
?>
