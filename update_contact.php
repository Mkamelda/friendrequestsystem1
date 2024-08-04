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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['new_name'];
    $new_email = $_POST['new_email'];
    $new_phone_number = $_POST['new_phone_number'];

    // Update the contact in the database
    $stmt_update_contact = $conn->prepare("UPDATE contacts SET name = ?, email = ?, phone_number = ? WHERE id = ? AND user_id = ?");
    $stmt_update_contact->execute([$new_name, $new_email, $new_phone_number, $contact_id, $_SESSION['user_id']]);

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
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

h1 {
    text-align: center;
        color: #007bff;
        
}

form {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 16px;
    width: 300px;
}

label {
    display: block;
    margin-bottom: 8px;
}

input {
    width: 100%;
    padding: 8px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #3498db;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #1a5276;
}
p {
        text-align: center;
    }

    a {
        display: block;
        text-align: center;
        margin-top: 16px;
        text-decoration: none;
        color: #3498db;
        padding-top: 40px;
        padding-left:100px;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
    <title>Update Contact</title>
</head>
<body>



<form method="post" action="">
<h1>Update Contact</h1>
    <label for="new_name">New Name:</label>
    <input type="text" id="new_name" name="new_name" value="<?php echo $contact['name']; ?>" required><br>

    <label for="new_email">New Email:</label>
    <input type="email" id="new_email" name="new_email" value="<?php echo $contact['email']; ?>" required><br>

    <label for="new_phone_number">New Phone Number:</label>
    <input type="text" id="new_phone_number" name="new_phone_number" value="<?php echo $contact['phone_number']; ?>" required><br>

    <input type="submit" value="Update Contact">
</form>
<p><a href="dashboard.php">Home</a><p>

</body>
</html>
