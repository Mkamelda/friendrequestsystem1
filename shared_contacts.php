<?php
session_start();

// Include database connection
include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve shared contacts for the user
$stmt_shared_contacts = $conn->prepare("SELECT sc.contact_id, c.name AS contact_name, u.username AS friend_name
FROM shared_contacts sc
JOIN contacts c ON sc.contact_id = c.id
JOIN friend_list f ON sc.friend_id = f.friend_id
JOIN users u ON f.friend_id = u.id
WHERE sc.user_id = ?");
$stmt_shared_contacts->execute([$user_id]); // Execute the prepared statement
$shared_contacts = $stmt_shared_contacts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Contacts</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        color: #333;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
    }

    h1 {
        text-align: center;
        color: #007bff;
    }

    ul {
        list-style: none;
        padding: 0;
    }

    li {
        background-color: #fff;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    }

    a:hover {
        text-decoration: underline;
    }
</style>

</head>
<body>

<h1>Shared Contacts</h1>

<?php if (empty($shared_contacts)): ?>
    <p>No shared contacts yet.</p>
<?php else: ?>
    <ul>
        <?php foreach ($shared_contacts as $shared_contact): ?>
            <li>
                <?php echo $shared_contact['contact_name']; ?> (Shared with <?php echo $shared_contact['friend_name']; ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="dashboard.php">Home</a></p>

</body>
</html>
