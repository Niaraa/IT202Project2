
<?php
session_start();

if (!isset($_SESSION["repID"])) {
    header("Location: index.php");
    exit;
}

$repName = $_SESSION["repName"];
$transaction = $_SESSION["transaction"] ?? "";

$pages = [
    "Search A Moving Representative's Accounts" => "search_clients.php",
    "Book a Client's Moving Event" => "book_move.php",
    "Cancel A Client's Moving Event" => "cancel_move.php",
    "Request Moving Services" => "moving_services.php",
    "Request Additional Moving Services" => "moving_services.php",
    "Create A New Client Account" => "create_client.php"
];

$selectedPage = $pages[$transaction] ?? "";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moving On Up - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f1ff;
            text-align: center;
            padding: 50px 20px;
        }

        .dashboard {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 4px 15px #ddd;
        }

        h1 { color: #7436a6; }

        .button {
            display: inline-block;
            background: #7436a6;
            color: white;
            padding: 12px 20px;
            margin: 10px;
            border-radius: 8px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="dashboard">
        <h1>Moving On Up</h1>

        <h2>Welcome,
            <?php echo htmlspecialchars($repName); ?>!
        </h2>

        <p>You have successfully logged in.</p>

        <h3>Selected Transaction:</h3>



<?php if ($selectedPage !== ""): ?>
    <a class="button"
       href="<?= htmlspecialchars($selectedPage) ?>">
        Continue Selected Transaction
    </a>
<?php endif; ?>

        <p><?php echo htmlspecialchars($transaction); ?></p>



<a class="button" href="search_clients.php">
    View My Client Accounts
</a>

<a class="button" href="book_move.php">
    Book a Moving Event
</a>

<a class="button" href="cancel_move.php">
    Cancel a Moving Event
</a>

<a class="button" href="create_client.php">
    Create a New Client Account
</a>



<a class="button" href="moving_services.php">
    Request Moving Services
</a>

<a class="button" href="moving_services.php">
    Request Additional Moving Services
</a>



        <a class="button" href="index.php">
            Return to Login
        </a>
    </div>
</body>
</html>
