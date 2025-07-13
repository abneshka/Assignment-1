<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');//cbcsm ccbkjc dcdjc dscbdsj
    exit;//svjsdn cnvshcn cnhdsvc bdnc nhds
}
require 'db.php';//sxvshn sncvshnbc sdnhcv cmsdj

// Ensure admin login
if (!isset($_SESSION['admin_loggedin']) || !$_SESSION['admin_loggedin']) {
    header('Location: adminCategories.php');//cdbc mdbjkvmnvnbkjdxv 
    exit;
}

$categoryId = $_GET['id'] ?? null;//vjscvsnchvdsjchdj,chvdsncvdc d
if (!$categoryId) {
    die("Category ID not specified.");//xsvjsn chmsdvc xdnhvcmhdsnbc ndxvh dnchd
}

$message = '';//xsbjx scnsdhbcnmc djsbcdcjd
try {
    // Fetch existing category
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");//sncsmc d,bjkc cdmbcjdcnsk
    $stmt->execute([$categoryId]);//xsmbm xcsh,vjc xbsjmnc sm
    $category = $stmt->fetch();//xsmb smcbsjc scbsjcsbmj

    if (!$category) {
        die("Category not found.");//sd msjbc snmcbxjcm scjksj
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newName = trim($_POST['name'] ?? '');//scn,sc n,dnc cmxdbjcm ,d
        if ($newName === '') {
            $message = '❌ Category name cannot be empty.';//ssjbc smcbsjcm scmjhbsmj
        } else {
            $update = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");//sbsmnc msjbcxn sjcsk
            $update->execute([$newName, $categoryId]);//snskndskibxs,jgsd,jwu
            header('Location: adminCategories.php');//zagsjhdn ncvhsdw
            exit;//dwjgwnbdhjn s
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category - Carbuy Admin</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        main {
            width: 50vw;
            margin: 5vw auto;
            padding: 3vw;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 2vw;
        }

        label, input[type="text"] {
            display: block;
            width: 100%;
            font-size: 1.2em;
            margin-bottom: 1.2em;
        }

        input[type="submit"] {
            background-color: #3665f3;
            color: white;
            padding: 0.5em 1em;
            font-size: 1.2em;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .logout {
            float: right;
            margin-top: 1em;
        }

        .message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Edit Category</h1>
        <a class="logout" href="index.php">Logout</a>
    </header>

    <main>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Category Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            <input type="submit" value="Update Category">
        </form>
    </main>

    <footer>
        <p style="text-align:center;">&copy; Carbuy <?= date('Y') ?></p>
    </footer>
</body>
</html>
