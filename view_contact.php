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

// Retrieve the contact details
$stmt_contact = $conn->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
$stmt_contact->execute([$contact_id, $_SESSION['user_id']]);
$contact = $stmt_contact->fetch(PDO::FETCH_ASSOC);

// Check if the contact exists and belongs to the current user
if (!$contact) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
    font-family: 'Arial', sans-serif;
    background-color: whitesmoke;
  opacity: 1;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;

}

h1 {
    text-align: center;
    color: #333;
}

p {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 16px;
    width: 300px;
}

strong {
    font-weight: bold;
}

a {
    display: block;
    text-align: center;
    margin-top: 16px;
    text-decoration: none;
    color: brown;
}

a:hover {
    text-decoration: underline;
}
</style>
    <title>View Contact</title>
</head>
<body>

<h1>View Contact</h1>
<img src="img.png" alt="pfp">

<p><strong>Name:</strong> <?php echo $contact['name']; ?></p>
<p><strong>Email:</strong> <?php echo $contact['email']; ?></p>
<p><strong>Phone Number:</strong> <?php echo $contact['phone_number']; ?></p>

<a href="dashboard.php">return to Home</a>

</body>
</html>
