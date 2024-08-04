<?php
// Include necessary files and start the session
include('db_connection.php');
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if the request ID is provided in the URL
if (!isset($_GET['id'])) {
    header('Location: friend_requests.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$request_id = $_GET['id'];

// Retrieve friend request details
$stmt_request = $conn->prepare("SELECT * FROM friend_requests WHERE id = ?");
$stmt_request->execute([$request_id]);
$request = $stmt_request->fetch(PDO::FETCH_ASSOC);

// Check if the request exists and is pending
if (!$request || $request['to_user_id'] !== $user_id || $request['status'] !== 'Pending') {
    header('Location: friend_requests.php');
    exit();
}

// Update the friend request status to 'Denied'
$stmt_deny = $conn->prepare("UPDATE friend_requests SET status = 'Denied' WHERE id = ?");
$stmt_deny->execute([$request_id]);

header('Location: friend_requests.php');
exit();
?>
