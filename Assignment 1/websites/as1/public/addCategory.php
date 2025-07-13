<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require 'db.php';//bdsiugeui dvdkjfhisfhvs vdvbdsifhesif

$error = '';//cndfd dkidonvfibudibvdse
$success = '';//cdbciuds dkhdsifhds vkvhdfihbvf vkhdivod

if (!isset($_SESSION['admin_loggedin']) || !$_SESSION['admin_loggedin']) {
    header('Location: adminCategories.php'); // Redirect to adminCategories.php which will show login form
    exit;//cndichsf dvdiofj dvodfjeofmlvkdf
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');//sbdiuawd ckhdsefuiefc dchisdehdbd v.kdhfioedd

    if ($name === '') {
        $error = "Category name cannot be empty.";//nckichdscikd dkhfidshfc vkdhfisdo vkdi
    } else {
        try {
            // Insert new category
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");//snclskdjc cdkjfcldkcmld
            $stmt->execute([$name]);//cdnlcknfdc dndlcmdldl

            // Redirect back to adminCategories.php after successful insert
            header('Location: adminCategories.php');//cndbckdnckdv vndljld l/dd
            exit;//mclcmldsc dcdnlcd dlcmdl;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Category - Admin</title>
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
            width: 50vw;
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
        label {
            display: block;
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 0.5em;
        }
        input[type=text] {
            width: 100%;
            padding: 0.6em;
            font-size: 1.1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 1.5em;
        }
        input[type=submit] {
            background-color: #3665f3;
            color: white;
            padding: 0.7em 2em;
            font-size: 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
        }
        input[type=submit]:hover {
            background-color: #2a4ad9;
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
        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 1.5em;
            text-align: center;
        }
    </style>
</head>
<body>

<main>
    <a href="adminCategories.php?action=logout" class="logout-btn">Logout</a>
    <h1>Add New Category</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="addCategory.php" novalidate>
        <label for="name">Category Name:</label>
        <input type="text" id="name" name="name" required autofocus />
        <input type="submit" value="Add Category" />
    </form>
</main>

<footer style="text-align:center; margin-top: 4em; border-top: 1px solid #ddd; padding: 1em;">
    &copy; Carbuy <?= date('Y') ?>
</footer>

</body>
</html>
