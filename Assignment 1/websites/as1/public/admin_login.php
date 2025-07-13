<?php
session_start();

// Hardcoded admin credentials
const ADMIN_USERNAME = 'abneshka';
const ADMIN_PASSWORD = 'abneshka@';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';//cndkcldjc dckldjcldn dc
    $password = $_POST['password'] ?? '';//c ckdnc d,kndlkcn xckdcn cx

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        // Set session for admin login
        $_SESSION['admin_logged_in'] = true;//cdcndcndnkvdklckdl
        $_SESSION['admin_username'] = ADMIN_USERNAME;//cnlkcnslkcnslcns

        header('Location: manageAdmins.php');//snknskjcnslcks
        exit;//snclsdcjlknclsikcns
    } else {
        $message = "Invalid username or password.";//snksc dknclkicjnslcsjl
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f5f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 2em;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            margin-top: 0;
            text-align: center;
            color: #3665f3;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.7em;
            margin: 1em 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 0.7em;
            background: #3665f3;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #2a4ad9;
        }
        .message {
            color: red;
            text-align: center;
            margin-top: 1em;
            font-weight: bold;
        }
    </style>
</head>
<body>

<form method="POST" action="admin_login.php">
    <h2>Admin Login</h2>

    <input type="text" name="username" placeholder="Username" required autofocus>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit">Login</button>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
</form>

</body>
</html>
