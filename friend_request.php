<?php
session_start();

include('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle friend requests if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_request'])) {
        $request_id = $_POST['accept_request'];

        // Update the status of the friend request to 'Accepted'
        $stmt_accept_request = $conn->prepare("UPDATE friend_requests SET status = 'Accepted' WHERE id = ?");
        $stmt_accept_request->execute([$request_id]);

        // Retrieve the details of the accepted friend
        $stmt_accepted_friend = $conn->prepare("SELECT u.* FROM friend_requests fr
                                               JOIN users u ON fr.from_user_id = u.id
                                               WHERE fr.id = ?");
        $stmt_accepted_friend->execute([$request_id]);
        $accepted_friend = $stmt_accepted_friend->fetch(PDO::FETCH_ASSOC);

        // Add the accepted friend to the friend list
        $stmt_add_to_friend_list = $conn->prepare("INSERT INTO friend_list (user_id, friend_id) VALUES (?, ?)");
        $stmt_add_to_friend_list->execute([$user_id, $accepted_friend['id']]);
    } elseif (isset($_POST['deny_request'])) {
        $request_id = $_POST['deny_request'];

        // Delete the friend request if denied
        $stmt_deny_request = $conn->prepare("DELETE FROM friend_requests WHERE id = ?");
        $stmt_deny_request->execute([$request_id]);
    }

    // Redirect back to friend_requests.php
    header('Location: friend_request.php');
    exit();
}

// Retrieve incoming friend requests
$stmt_incoming_requests = $conn->prepare("SELECT fr.id, u.username FROM friend_requests fr
                                          JOIN users u ON fr.from_user_id = u.id
                                          WHERE fr.to_user_id = ? AND fr.status = 'Pending'");
$stmt_incoming_requests->execute([$user_id]);
$incoming_requests = $stmt_incoming_requests->fetchAll(PDO::FETCH_ASSOC);
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
    color: #333;
    padding-right: 90px;
}

ul {
    list-style: none;
    padding: 0;
    text-align: center;
}

li {
    margin-bottom: 16px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-right: 40px;
}

button {
    background-color: #3498db;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 8px;
}

button:hover {
    background-color: #1a5276;
}

p {
    text-align: center;
}

a {
    color: #3498db;
    text-decoration: none;
    padding-top: 40px;
        padding-left:100px;
}

a:hover {
    text-decoration: underline;
}

    </style>
    <title>Friend Requests</title>
</head>
<body>

<h1>Friend Requests</h1>

<?php if (empty($incoming_requests)): ?>
    <p>No incoming friend requests.</p>
<?php else: ?>
    <ul>
        <?php foreach ($incoming_requests as $request): ?>
            <li>
                <?php echo $request['username']; ?>
                
                <form method="post" action="friend_request.php">
                    <button type="submit" name="accept_request" value="<?php echo $request['id']; ?>">Accept</button>
                    <button type="submit" name="deny_request" value="<?php echo $request['id']; ?>">Deny</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<p><a href="dashboard.php">Home</a><p>

</body>
</html>
