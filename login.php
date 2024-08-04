<?php
include('db_connection.php');
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user data from the 'users' table
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables and redirect to the dashboard
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
        }

        form {
            background-color: #fff;
            padding: 100px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            color: green;
            box-sizing:border-box ;
            max-width: 600px;
            width: 100%;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: green;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 110%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: green;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: greenyellow;
        }

        p {
            text-align: center;
            margin-top: 16px;
        }

        a {
            color: #fff;
            font-weight: bold;
        }
        p1 {
        text-align: center;
        
    }

    a {
        display: block;
        text-align: center;
        margin-top: 16px;
        text-decoration: none;
        color: green;
        ;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>

    <title>User Login</title>
</head>
<body>


<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" action="">
    <h2>LOGIN</h2>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Login">
    <p1><a href="index.php">Dont already have an account?</a></p1>

</form>



</body>
</html>
