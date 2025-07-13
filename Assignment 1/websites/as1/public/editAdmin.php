<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}
require_once 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Get admin ID to edit
if (!isset($_GET['id'])) {
    echo "No admin selected.";//xbdjcs cdbchdskjcbdmcdjk
    exit;
}

$id = $_GET['id'];
$message = '';

// Fetch admin details
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$id]);//xshvxjshchjcshj
$admin = $stmt->fetch();//nkscns ,cnskcn

if (!$admin) {
    echo "Admin not found.";//zsn,skc mcjnsc ,kcsnk
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['username'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
        $stmt->execute([$newUsername, $hashedPassword, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
        $stmt->execute([$newUsername, $id]);
    }

    $message = "Admin updated successfully!";//xsxbsm xzjkcsjbcsj
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin</title>
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <style>
        body {
            background-color: #f6f5f4;
            font-family: 'Noto Sans', sans-serif;
            max-width: 100vw;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        main {
            margin-top: 5vw;
            width: 50vw;
            background-color: white;
            border: 2px solid #ddd;
            padding: 2em;
            box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2.2em;
            margin-bottom: 1em;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-top: 1em;
        }
        input {
            padding: 0.8em;
            margin-top: 0.5em;
            border: 1px solid #ccc;
            font-size: 1em;
            width: 100%;
        }
        input[type="submit"] {
            background-color: #3665f3;
            color: white;
            border: none;
            margin-top: 2em;
            font-size: 1.1em;
            cursor: pointer;
            width: auto;
            padding: 0.8em 1.2em;
            align-self: flex-end;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-top: 1em;
        }
        a.back-link {
            text-decoration: none;
            background-color: #222;
            color: white;
            padding: 0.5em 1em;
            margin-top: 1em;
            display: inline-block;
            border-radius: 4px;
        }
        a.back-link:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<main>
    <h1>Edit Admin</h1>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= htmlspecialchars($admin['username']) ?>" required>

        <label for="password">New Password (leave blank to keep current)</label>
        <input type="password" name="password" id="password">

        <input type="submit" value="Update Admin">
    </form>

    <a href="manageAdmins.php" class="back-link">← Back to Admins</a>
</main>

</body>
</html>
