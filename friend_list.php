<?php
session_start();

include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve accepted friendships for the user
$stmt_friends = $conn->prepare("SELECT u.* FROM friend_requests fr
                                JOIN users u ON (fr.from_user_id = u.id OR fr.to_user_id = u.id)
                                WHERE (fr.from_user_id = ? OR fr.to_user_id = ?) AND fr.status = 'Accepted' and not u.id = ?");
$stmt_friends->execute([$user_id, $user_id, $user_id]);
$friends = $stmt_friends->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <title>Friends List</title>
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
        margin-bottom: 20px;
        padding-right: 90px;
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
        padding-top: 40px;
        padding-left:100px;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

</head>
<body>

<h1>Friends List</h1>

<?php if (empty($friends)): ?>
    <p>No friends yet.</p>
<?php else: ?>
    <ul>
        <?php foreach ($friends as $friend): ?>
            <li>
                <?php echo $friend['username']; ?> (<?php echo $friend['email']; ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<p><a href="dashboard.php">Home</a><p>

</body>
</html>
