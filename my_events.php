
<?php
session_start();
require 'db.php';

// Make sure the student is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$sql = "SELECT events.id, events.title, events.description,
               events.date, events.time, events.venue
        FROM registrations
        INNER JOIN events ON registrations.event_id = events.id
        WHERE registrations.user_id = ?
        ORDER BY events.date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Registered Events</title>
</head>
<body>
    <h1>My Registered Events</h1>

    <?php if (isset($_GET["registered"])): ?>
        <p>You have successfully registered for the event!</p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($event = $result->fetch_assoc()): ?>
            <div>
                <h2><?php echo htmlspecialchars($event["title"]); ?></h2>

                <p>
                    <?php echo htmlspecialchars($event["description"] ?? ""); ?>
                </p>

                <p>Date: <?php echo htmlspecialchars($event["date"]); ?></p>
                <p>Time: <?php echo htmlspecialchars($event["time"]); ?></p>
                <p>Venue: <?php echo htmlspecialchars($event["venue"]); ?></p>
            </div>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have not registered for any events yet.</p>
    <?php endif; ?>

    <p><a href="events.php">Back to Available Events</a></p>
    <p><a href="logout.php">Log Out</a></p>
</body>
</html>
<?php
$stmt->close();
?>

