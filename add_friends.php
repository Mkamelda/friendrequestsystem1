<?php
session_start();

include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle sending friend requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_request'])) {
        $receiver_id = $_POST['send_request'];

        // Check if the request is not sent to oneself
        if ($receiver_id != $user_id) {
            // Check if a friend request doesn't already exist
            $stmt_check_request = $conn->prepare("SELECT id FROM friend_requests WHERE from_user_id = ? AND to_user_id = ?");
            $stmt_check_request->execute([$user_id, $receiver_id]);
            $existing_request = $stmt_check_request->fetchColumn();

            if (!$existing_request) {
                // Send the friend request
                $stmt_send_request = $conn->prepare("INSERT INTO friend_requests (from_user_id, to_user_id, status) VALUES (?, ?, 'Pending')");
                $stmt_send_request->execute([$user_id, $receiver_id]);
            }
        }
    }

    // Redirect to refresh the page and avoid form resubmission
    header('Location: add_friends.php');
    exit();
}

// Retrieve users that are not friends with the current user
$stmt_users = $conn->prepare("SELECT id, username FROM users WHERE id <> ? AND id NOT IN (
                                SELECT user1_id FROM friendships WHERE user2_id = ? 
                                UNION
                                SELECT user2_id FROM friendships WHERE user1_id = ?
                            ) AND id <> ALL (
                                SELECT to_user_id FROM friend_requests WHERE from_user_id = ? AND status = 'Pending'
                            ) AND id <> ALL (
                                SELECT from_user_id FROM friend_requests WHERE to_user_id = ? AND status = 'Pending'
                            )");
$stmt_users->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
$users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Friends</title>
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
            padding-top: 40px;
            padding-right:100px;
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

        form {
            margin-left: 10px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            text-align: center;
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

<h1>Add Friends</h1>

<?php if (empty($users)): ?>
    <p>No users available to add as friends.</p>
<?php else: ?>
    <ul>
        <?php foreach ($users as $user): ?>
            <li>
                <?php echo $user['username']; ?>
                <form method="post" action="">
                    <button type="submit" name="send_request" value="<?php echo $user['id']; ?>">Send Friend Request</button>
                </form>
            </li>
        <?php endforeach; ?>
       
    </ul>
<?php endif; ?>
<p><a href="dashboard.php">Home</a><p>

</body>
</html>
