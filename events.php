
<?php
session_start();
require 'db.php';

// Make sure the student is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM events ORDER BY date ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Events</title>
</head>
<body>
    <h1>School Event Booking System</h1>

    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
    </p>

    <h2>Available Events</h2>

    <p><a href="my_events.php">My Registered Events</a></p>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($event = $result->fetch_assoc()): ?>
            <div>
                <h3><?php echo htmlspecialchars($event['title']); ?></h3>

                <p>
                    <?php echo htmlspecialchars($event['description'] ?? ''); ?>
                </p>

                <p>Date: <?php echo htmlspecialchars($event['date']); ?></p>
                <p>Time: <?php echo htmlspecialchars($event['time']); ?></p>
                <p>Venue: <?php echo htmlspecialchars($event['venue']); ?></p>
                <p>Capacity: <?php echo htmlspecialchars($event['capacity']); ?></p>

                <form action="register_event.php" method="POST">
                    <input
                        type="hidden"
                        name="event_id"
                        value="<?php echo (int) $event['id']; ?>"
                    >
                    <button type="submit">Register for Event</button>
                </form>
            </div>

            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No events are available yet.</p>
    <?php endif; ?>

    <p><a href="logout.php">Log Out</a></p>
</body>
</html>

