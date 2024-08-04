<?php
include('db_connection.php');
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: dashboard');
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve user information from the 'users' table
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Retrieve user contacts from the 'contacts' table
$stmt_contacts = $conn->prepare("SELECT * FROM contacts WHERE user_id = ?");
$stmt_contacts->execute([$user_id]);
$contacts = $stmt_contacts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <style>
    body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            color: olive;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            background-color: green;
            overflow: hidden;
        }

        li {
            float: left;
        }

        li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        li a:hover {
            background-color: #ddd;
            color: black;
        }

        h2 {
            color: olive;
        }
        p{
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: green;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
   </style>

    <title>User Dashboard</title>
</head>
<body>

<h1>Welcome, <?php echo $user['username']; ?>!</h1>


<ul>
   
    <li><a href="create_contact.php">Create New Contact</a></li>
    <li><a href="share_contact.php">share contacts</a></li>
    <li><a href="shared_contacts.php">View shared contacts</a></li>
    <li><a href="friend_request.php">Friend requests</a></li>
    <li><a href="friend_list.php">Friend List</a></li>
    <li><a href="add_friends.php">Add Friends</a></li>
    <li><a href="logout.php">Logout</a><li>
</ul>


<h2>User Information</h2>
<p>Username: <?php echo $user['username']; ?></p>
<p>Email: <?php echo $user['email']; ?></p>


<h2>Your Contacts</h2>
<table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Action</th>
            
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contacts as $contact): ?>
            <tr>
                <td><?php echo $contact['name']; ?></td>
                <td><?php echo $contact['email']; ?></td>
                <td><?php echo $contact['phone_number']; ?></td>
                <td>
                    <a href="view_contact.php?id=<?php echo $contact['id']; ?>">View</a> |
                    <a href="update_contact.php?id=<?php echo $contact['id']; ?>">Update</a> |
                    <a href="delete.php?id=<?php echo $contact['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
