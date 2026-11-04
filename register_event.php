
<?php
session_start();
require 'db.php';

// Only logged-in students can register
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["event_id"])) {
    header("Location: events.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];
$event_id = filter_var(
    $_POST["event_id"],
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($event_id === false || $event_id === null) {
    exit("Invalid event selected. <a href='events.php'>Return to events</a>");
}

// Check whether the event exists and get its capacity
$stmt = $conn->prepare(
    "SELECT id, capacity FROM events WHERE id = ?"
);
$stmt->bind_param("i", $event_id); 
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    exit("Event not found. <a href='events.php'>Return to events</a>");
}

// Prevent the same student from registering twice
$stmt = $conn->prepare(
    "SELECT id FROM registrations WHERE user_id = ? AND event_id = ?"
);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$already_registered = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($already_registered) {
    exit("You are already registered for this event. <a href='my_events.php'>View your events</a>");
}

// Count current registrations and check available spaces
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM registrations WHERE event_id = ?"
);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$total = (int) $stmt->get_result()->fetch_assoc()["total"];
$stmt->close();

if ($total >= (int) $event["capacity"]) {
    exit("Sorry, this event is full. <a href='events.php'>Return to events</a>");
}

// Save the registration
$stmt = $conn->prepare(
    "INSERT INTO registrations (user_id, event_id) VALUES (?, ?)"
);
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: my_events.php?registered=1");
    exit;
}

$stmt->close();
exit("Registration could not be completed. Please try again.");
?>

