<?php
// Include necessary files and start the session
include('db_connection.php');
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Validate and sanitize input if needed

    // Insert the new contact
    $stmt_insert_contact = $conn->prepare("INSERT INTO contacts (user_id, name, email, phone_number) VALUES (?, ?, ?, ?)");
    $stmt_insert_contact->execute([$user_id, $name, $email, $phone_number]);

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
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            color: #333;
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
            max-width: 400px;
            width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
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
    <title>Create New Contact</title>
</head>
<body>



<form method="post" action="">
<h1>Create New Contact</h1>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="phone_number">Phone Number:</label>
    <input type="tel" id="phone_number" name="phone_number" required><br>

    <input type="submit" value="Create Contact">
</form>
<p><a href="dashboard.php">Home</a></p>


</body>
</html>
