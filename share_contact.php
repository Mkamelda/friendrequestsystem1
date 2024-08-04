<?php
session_start();

// Include database connection
include('db_connection.php');


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];


$stmt_friends = $conn->prepare("SELECT f.friend_id, u.username AS friend_name FROM friend_list f
                                JOIN users u ON (f.friend_id = u.id or f.user_id= u.id)
                                WHERE f.user_id = ? or f.friend_id=?");

$stmt_friends->execute([$user_id, $user_id]);
if (!$stmt_friends) {
    die('Query failed: ' . $conn->errorInfo()[2]);
}

$friends = $stmt_friends->fetchAll(PDO::FETCH_ASSOC);





$stmt_contacts = $conn->prepare("SELECT id, name, phone_number FROM contacts WHERE user_id = :user_id");


$stmt_contacts->bindParam(':user_id', $user_id, PDO::PARAM_INT);


$stmt_contacts->execute();


$contacts = $stmt_contacts->fetchAll(PDO::FETCH_ASSOC);






if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['share_contact'])) {
        $friend_id = $_POST['friend_id'];
        $contact_id = $_POST['contact_id'];

        
        $stmt_check_share = $conn->prepare("
            SELECT 1
            FROM shared_contacts sc
            WHERE sc.user_id = ? AND sc.friend_id = ? AND sc.contact_id = ?
        ");
        $stmt_check_share->execute([$user_id, $friend_id, $contact_id]);
        $already_shared = $stmt_check_share->fetch(PDO::FETCH_COLUMN);

        if (!$already_shared) {
        
            $stmt_share_contact = $conn->prepare("
                INSERT INTO shared_contacts (user_id, friend_id, contact_id) VALUES (?, ?, ?)
            ");
            $stmt_share_contact->execute([$user_id, $friend_id, $contact_id]);
        }

        
        header('Location: shared_contacts.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Contact</title>
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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        label {
            margin-bottom: 5px;
        }

        select {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
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



<form method="post" action="">
<h1>Share Contact</h1>

    <label for="contact_id">Select Contact:</label>
    <select id="contact_id" name="contact_id" required>
        <?php foreach ($contacts as $contact): ?>
            <option value="<?php echo $contact['id']; ?>"><?php echo $contact['name']; ?></option>
        <?php endforeach; ?>
    </select><br>

    <label for="friend_id">Select Friend:</label>
    <select id="friend_id" name="friend_id" required>
        <?php if (!empty($friends)): ?>
            <option value="" disabled selected>Select a friend</option>
            <?php foreach ($friends as $friend): ?>
                <option value="<?php echo $friend['friend_id']; ?>"><?php echo $friend['friend_name']; ?></option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" disabled>No friends available</option>
        <?php endif; ?>
    </select><br>

    <input type="submit" name="share_contact" value="Share Contact">
</form>

<p><a href="dashboard.php">Home</a></p>

</body>
</html>
