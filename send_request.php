<?php
session_start();

include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if friend_id is set in the URL
if (!isset($_GET['friend_id'])) {
    header('Location: error.php');
    exit();
}

$friend_id = $_GET['friend_id'];

// Check if the friend exists
$stmt_check_friend = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_check_friend->execute([$friend_id]);
$friend = $stmt_check_friend->fetch(PDO::FETCH_ASSOC);

if (!$friend) {
    header('Location: error.php');
    exit();
}

// Check if a friend request already exists
$stmt_check_request = $conn->prepare("SELECT * FROM friend_requests WHERE from_user_id = ? AND to_user_id = ?");
$stmt_check_request->execute([$user_id, $friend_id]);
$existing_request = $stmt_check_request->fetch(PDO::FETCH_ASSOC);

if ($existing_request) {
    // Redirect or handle appropriately (e.g., display a message that the request already exists)
    header('Location: error.php');
    exit();
}

// Insert the friend request
$stmt_send_request = $conn->prepare("INSERT INTO friend_requests (from_user_id, to_user_id) VALUES (?, ?)");
$stmt_send_request->execute([$user_id, $friend_id]);

// Redirect or handle success
header('Location: friend_list.php');
exit();
?>
