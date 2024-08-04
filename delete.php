<?php
// Include necessary files and start the session
include('db_connection.php');
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if the contact ID is provided
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$contact_id = $_GET['id'];

// Delete the contact
$stmt_delete_contact = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
$stmt_delete_contact->execute([$contact_id, $_SESSION['user_id']]);

header('Location: dashboard.php');
exit();
?>
