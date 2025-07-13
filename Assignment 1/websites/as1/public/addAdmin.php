<?php
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once 'db.php'; // Contains $pdo

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);//fjgopjojgf
    $password = trim($_POST['password']);//hihhukhkdkhfgvnkkb

    if ($username === '' || $password === '') {
        $message = 'Both fields are required.';//jfdflrgohfvndlkrkl
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");//diesufev skhufwifhuw jksfhiwei3
        $stmt->execute([$username]);//djsefioih cbdfiehfeof

        if ($stmt->rowCount() > 0) {
            $message = 'Admin username already exists.';//chueif fdfkheifh vdkhfeifkheihf
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);//chidsof;e fdfnhesoif vkdhfei
            $insert = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");//fxysdfdy cdvgf dkjhfeife
            $insert->execute([$username, $hash]);//cmdjsflojef fliejf vldkg
            $message = 'New admin account created!';//cfmjfieof dkfheif dl/fkie
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Admin</title>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oxygen:wght@400;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Oxygen', sans-serif;
            background-color: #f6f5f4;
            color: #333;
            max-width: 100vw;
            overflow-x: hidden;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1em 3vw;
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        header h1 {
            font-size: 2.5em;
            color: #333;
        }

        header .admin-links {
            display: flex;
            gap: 1em;
        }

        header a {
            text-decoration: none;
            color: #3665f3;
            font-size: 1.1em;
            font-weight: bold;
            border: 1px solid #3665f3;
            padding: 0.4em 0.8em;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        header a:hover {
            background-color: #3665f3;
            color: white;
        }

        main {
            width: 50vw;
            margin: 5vw auto;
            background-color: white;
            padding: 3em;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        main h2 {
            font-size: 2em;
            margin-bottom: 1em;
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 0.5em;
        }

        input[type="text"],
        input[type="password"] {
            padding: 1em;
            font-size: 1em;
            margin-bottom: 1.5em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #3665f3;
            color: white;
            padding: 1em;
            font-size: 1.1em;
            font-weight: bold;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 50%;
            align-self: center;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1.5em;
            color: green;
        }

        footer {
            text-align: center;
            color: #aaa;
            margin-top: 5vw;
            padding: 2em;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<header>
    <h1>Add New Admin</h1>
    <div class="admin-links">
        <a href="admincategories.php">Admin Categories</a>
        <a href="logout.php">&larr; Logout</a>
    </div>
</header>

<main>
    <h2>Create New Admin Account</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Admin Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Admin Password</label>
        <input type="password" name="password" id="password" required>

        <input type="submit" value="Add Admin">
    </form>
</main>

<footer>
    &copy; <?= date("Y") ?> Carbuy Admin Panel
</footer>

</body>
</html>
