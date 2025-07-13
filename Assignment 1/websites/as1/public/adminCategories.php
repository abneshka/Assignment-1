<?php
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Handle login form submission
    if (isset($_POST['admin_login'])) {
        define('ADMIN_USERNAME', 'abneshka');//abneshkfdf
        define('ADMIN_PASSWORD', 'abneshka@');//sndsknddfcnsd

        $username = $_POST['username'] ?? '';//dsnhdslkncsofjesl
        $password = $_POST['password'] ?? '';//sldinkslcnfsolefjfoje

        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_loggedin'] = true;//slkcns,ckdslndxcdk
            header('Location: admincategories.php');//jksbbdc ckhdckc
            exit;//dcnds dkdndlfe
        } else {
            $error = "Invalid admin username or password.";//gguy jugvjugu
        }
    }
} else {
    // Handle logout
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        unset($_SESSION['admin_loggedin']);
        header('Location: index.php');
        exit;
    }
}

require 'db.php';

$error = $error ?? ''; // Ensure $error is always defined
$isAdmin = $_SESSION['admin_loggedin'] ?? false;

$categories = [];
if ($isAdmin) {
    try {
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Error fetching categories: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Categories</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        body {
            font-family: 'Oxygen-Regular', sans-serif;
            background-color: #f6f5f4;
            max-width: 100vw;
            overflow-x: hidden;
        }
        main {
            padding: 3vw;
            display: block;
            overflow: hidden;
            width: 80vw;
            margin: 3em auto;
            border-left: 2px solid #ddd;
            border-right: 2px solid #ddd;
            background: white;
            border-radius: 8px;
        }
        h1 {
            font-weight: normal;
            font-size: 2.5em;
            margin-bottom: 2em;
            text-align: center;
        }

        .logout-btn {
            display: block;
            width: 150px;
            margin: 0 auto 2em auto;
            padding: 0.5em;
            font-size: 1.2em;
            background-color: #3665f3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #2a4ad9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 1em;
            border-bottom: 1px solid #ddd;
            text-align: left;
            font-size: 1.2em;
        }
        table th {
            background-color: #f0f0f0;
        }
        a.action-link {
            margin-right: 1em;
            color: #3665f3;
            text-decoration: none;
            font-weight: bold;
        }
        a.action-link:hover {
            text-decoration: underline;
        }
        .add-new {
            display: inline-block;
            margin-bottom: 1em;
            padding: 0.7em 1.5em;
            background-color: #3665f3;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.2em;
        }
        .add-new:hover {
            background-color: #2a4ad9;
        }
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 1em;
            text-align: center;
        }

        form.login-form {
            max-width: 400px;
            margin: 4em auto;
            padding: 2em;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        form.login-form label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
            font-size: 1.1em;
        }
        form.login-form input[type=text],
        form.login-form input[type=password] {
            width: 100%;
            padding: 0.6em;
            margin-bottom: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        form.login-form input[type=submit] {
            background-color: #3665f3;
            color: white;
            border: none;
            padding: 0.7em 1.5em;
            font-size: 1.1em;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        form.login-form input[type=submit]:hover {
            background-color: #2a4ad9;
        }
    </style>
</head>
<body>

<main>
    <?php if (!$isAdmin): ?>
        <form method="post" class="login-form">
            <h2>Admin Login</h2>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required autofocus />
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required />
            <input type="submit" name="admin_login" value="Log In" />
        </form>
    <?php else: ?>
        <h1>Manage Categories</h1>

        <a href="admincategories.php?action=logout" class="logout-btn">Logout</a>
        <a href="addCategory.php" class="add-new">+ Add New Category</a>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($categories): ?>
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td>
                                <a href="editCategory.php?id=<?= $cat['id'] ?>" class="action-link">Edit</a>
                                <a href="deleteCategory.php?id=<?= $cat['id'] ?>" class="action-link" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center;">No categories found.</p>
        <?php endif; ?>
    <?php endif; ?>
</main>

<footer style="text-align:center; margin-top: 4em; border-top: 1px solid #ddd; padding: 1em;">
    &copy; Carbuy <?= date('Y') ?>
</footer>

</body>
</html>
