<?php
session_start();
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $message = "❌ Invalid username or password.";
        }
    } catch (PDOException $e) {
        $message = "❌ Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - CarBuy</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        main form {
            display: flex;
            flex-wrap: wrap;
            width: 50vw;
        }

        main form label,
        main form input {
            flex-grow: 1;
            width: 20vw;
            margin-bottom: 1em;
            margin-right: 2vw;
            margin-left: 2vw;
        }

        main form input[type=submit] {
            background-color: #3665f3;
            color: white;
            flex-grow: 0;
            margin-left: auto;
            font-size: 1.2em;
            padding: 0.2em;
            cursor: pointer;
            border: 0;
        }

        .message {
            color: red;
            font-weight: bold;
            margin-bottom: 2em;
        }

        .register-link {
            margin-top: 1.5em;
            font-weight: bold;
            margin-left: 2vw;
        }

        .register-link a {
            color: #3665f3;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>
            <span class="C">C</span><span class="a">a</span><span class="r">r</span>
            <span class="b">b</span><span class="u">u</span><span class="y">y</span>
        </h1>
    </header>

    <main>
        <h1>Login to Your Account</h1>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </main>

    <footer>
        &copy; Carbuy <?= date('Y') ?>
    </footer>
</body>
</html>
