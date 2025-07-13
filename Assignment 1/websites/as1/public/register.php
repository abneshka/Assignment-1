<?php
session_start();
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $message = "❌ Password must be at least 6 characters.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $message = "❌ Username already taken.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $hashed_password]);

                $_SESSION['register_success'] = "✅ Registration successful. Please log in.";
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            $message = "❌ DB Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - CarBuy</title>
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

        .login-link {
            margin-top: 2em;
            font-weight: bold;
        }

        .login-link a {
            color: #3665f3;
            text-decoration: none;
        }

        .login-link a:hover {
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
        <h1>Register a New Account</h1>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>

            <input type="submit" value="Register">
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </main>

    <footer>
        &copy; Carbuy <?= date('Y') ?>
    </footer>
</body>
</html>
