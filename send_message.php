<?php
// Include necessary files and start the session
include('db_connection.php');
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if the friend ID is provided
if (!isset($_GET['friend_id'])) {
    header('Location: friend_list.php');
    exit();
}

$friend_id = $_GET['friend_id'];

// Retrieve the friend's details
$stmt_friend = $conn->prepare("SELECT * FROM contacts WHERE user_id = ?");
$stmt_friend->execute([$friend_id]);
$friend = $stmt_friend->fetch(PDO::FETCH_ASSOC);

// Check if the friend exists
if (!$friend) {
    header('Location: friend_list.php');
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    // Validate and sanitize input if needed

    // Insert the message into the database
    $stmt_insert_message = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt_insert_message->execute([$_SESSION['user_id'], $friend_id, $message]);

    header('Location: friend_list.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
</head>
<body>

<h1>Send Message to <?php echo $friend['name']; ?></h1>

<form method="post" action="">
    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="4" cols="50" required></textarea><br>

    <input type="submit" value="Send Message">
</form>

</body>
</html>
