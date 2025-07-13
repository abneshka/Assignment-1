<?php
session_start();
require 'db.php';

$auctionId = $_GET['id'] ?? null;
if (!$auctionId) die("Auction ID not specified.");

// Ensure user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit;
}

$message = '';
$auction = null;
$categories = [];

// Fetch categories for dropdown
try {
    $catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $catStmt->fetchAll();
} catch (PDOException $e) {
    die("Error loading categories: " . $e->getMessage());
}

// Load auction data
try {
    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE id = ?");
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch();

    if (!$auction) {
        die("Auction not found.");
    }

    // Check if current user is owner
    if ($auction['userId'] != $_SESSION['user_id']) {
        die("You are not allowed to edit this auction.");
    }

    // Handle update form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $endDate = $_POST['endDate'];
        $categoryId = $_POST['categoryId'];
        $imagePath = trim($_POST['imagePath']);

        if ($title && $description && $endDate && $categoryId) {
            $updateStmt = $pdo->prepare("UPDATE auctions SET title = ?, description = ?, endDate = ?, categoryId = ?, imagePath = ? WHERE id = ?");
            $updateStmt->execute([$title, $description, $endDate, $categoryId, $imagePath, $auctionId]);
            $message = "✅ Auction updated successfully!";
        } else {
            $message = "❌ All fields are required.";
        }
    }

    // Handle delete request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        // First delete related bids
        $pdo->prepare("DELETE FROM bids WHERE auctionId = ?")->execute([$auctionId]);

        // Then delete the auction
        $pdo->prepare("DELETE FROM auctions WHERE id = ?")->execute([$auctionId]);

        header("Location: index.php?deleted=1");
        exit;
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Auction - Carbuy</title>
    <link rel="stylesheet" href="carbuy.css">
    <style>
        main form {
            max-width: 600px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 1em;
        }

        main form input, main form textarea, main form select {
            padding: 0.5em;
            font-size: 1em;
            width: 100%;
        }

        main form button {
            padding: 0.7em;
            font-size: 1.1em;
            cursor: pointer;
        }

        .delete-btn {
            background-color: crimson;
            color: white;
            border: none;
        }

        .update-btn {
            background-color: #3665f3;
            color: white;
            border: none;
        }

        .message {
            text-align: center;
            font-weight: bold;
            color: green;
        }

        header {
            padding: 1em;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        nav ul {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 30px;
            background-color: #f0f0f0;
            padding: 1em;
            margin: 0;
        }

        nav ul li a {
            text-decoration: none;
            font-weight: bold;
            color: #3665f3;
        }

        footer {
            text-align: center;
            margin-top: 3em;
            padding: 1em;
            font-size: 0.9em;
            color: #aaa;
        }
    </style>
</head>
<body>

<header>
    <h1>Edit Auction</h1>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="category.php">Categories</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main>
    <h2>Edit: <?= htmlspecialchars($auction['title']) ?></h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($auction['description']) ?></textarea>

        <label>End Date:</label>
        <input type="datetime-local" name="endDate" value="<?= date('Y-m-d\TH:i', strtotime($auction['endDate'])) ?>" required>

        <label>Category:</label>
        <select name="categoryId" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $auction['categoryId'] == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Image Path (relative):</label>
        <input type="text" name="imagePath" value="<?= htmlspecialchars($auction['imagePath']) ?>">

        <button type="submit" name="update" class="update-btn">Update Auction</button>
        <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this auction?')">Delete Auction</button>
    </form>
</main>

<footer>
    &copy; Carbuy <?= date('Y') ?>
</footer>
</body>
</html>
