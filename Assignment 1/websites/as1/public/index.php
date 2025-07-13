<?php
session_start();
require 'db.php';

$auctions = [];
$categories = [];

try {
    // Fetch categories for navigation bar
    $catStmt = $pdo->query("SELECT name FROM categories ORDER BY name");
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

    $searchTerm = '';
    $categoryFilter = $_GET['category'] ?? null;
    $sort = $_GET['sort'] ?? 'endDate';

    $sortQuery = ($sort === 'price') ? 'current_bid DESC' : 'a.endDate ASC';

    if (isset($_GET['search']) && trim($_GET['search']) !== '') {
        $searchTerm = '%' . $_GET['search'] . '%';
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.description, a.endDate, a.imagePath, a.categoryId, c.name AS category_name,
                               (SELECT MAX(amount) FROM bids WHERE auctionId = a.id) AS current_bid
                               FROM auctions a
                               JOIN categories c ON a.categoryId = c.id
                               WHERE a.title LIKE ? OR a.description LIKE ?
                               ORDER BY $sortQuery
                               LIMIT 10");
        $stmt->execute([$searchTerm, $searchTerm]);
    } elseif ($categoryFilter) {
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.description, a.endDate, a.imagePath, a.categoryId, c.name AS category_name,
                               (SELECT MAX(amount) FROM bids WHERE auctionId = a.id) AS current_bid
                               FROM auctions a
                               JOIN categories c ON a.categoryId = c.id
                               WHERE c.name = ?
                               ORDER BY $sortQuery
                               LIMIT 10");
        $stmt->execute([$categoryFilter]);
    } else {
        $stmt = $pdo->query("SELECT a.id, a.title, a.description, a.endDate, a.imagePath, a.categoryId, c.name AS category_name,
                             (SELECT MAX(amount) FROM bids WHERE auctionId = a.id) AS current_bid
                             FROM auctions a
                             JOIN categories c ON a.categoryId = c.id
                             ORDER BY $sortQuery
                             LIMIT 10");
    }

    $auctions = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "❌ Error fetching auctions: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        .carList li {
            display: flex;
            align-items: flex-start;
            gap: 2vw;
            padding: 2vw;
            border-bottom: 1px solid #ddd;
        }

        .carList li img {
            max-width: 250px;
            width: 100%;
            height: auto;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .carList li article {
            flex: 1;
        }

        .carList li article h2 {
            font-size: 2em;
            margin-bottom: 0.3em;
        }

        .carList li article h3 {
            font-size: 1.2em;
            margin-bottom: 0.8em;
            color: #3665f3;
        }

        .carList li article p {
            font-size: 1.1em;
            line-height: 1.4em;
            margin-bottom: 1em;
        }

        .carList li article .price {
            font-size: 1.5em;
            font-weight: bold;
            color: red;
            margin-bottom: 1em;
            text-align: left;
        }

        .carList li article .countdown {
            font-weight: bold;
            margin-bottom: 1em;
        }

        .carList li article a.more {
            display: inline-block;
            background-color: #3665f3;
            color: white;
            font-size: 1.3em;
            padding: 0.3em 0.8em;
            border-radius: 5px;
            text-decoration: none;
        }

        .carList li article a.more:hover {
            background-color: #2a4ad9;
        }
    </style>
    <script>
        function updateCountdowns() {
            const timers = document.querySelectorAll('.countdown');
            timers.forEach(timer => {
                const endTime = new Date(timer.dataset.end);
                const now = new Date();
                const diff = endTime - now;

                if (diff <= 0) {
                    timer.textContent = 'Ended';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((diff / (1000 * 60)) % 60);
                const seconds = Math.floor((diff / 1000) % 60);

                timer.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
            });
        }

        setInterval(updateCountdowns, 1000);
        window.onload = updateCountdowns;
    </script>
</head>

<body>
<header>
    <h1>
        <span class="C">C</span><span class="a">a</span><span class="r">r</span>
        <span class="b">b</span><span class="u">u</span><span class="y">y</span>
    </h1>

    <form action="index.php" method="GET">
        <input type="text" name="search" placeholder="Search for a car" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
        <select name="sort">
            <option value="endDate" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'endDate') ? 'selected' : '' ?>>Ending Soon</option>
            <option value="price" <?= ($_GET['sort'] ?? '') == 'price' ? 'selected' : '' ?>>Highest Price</option>
        </select>
        <input type="submit" name="submit" value="Search" />
    </form>
</header>

<nav>
    <ul>
        <li><a class="categoryLink" href="index.php">Home</a></li>

        <?php foreach ($categories as $cat): ?>
            <li><a class="categoryLink" href="category.php?category=<?= urlencode($cat) ?>"><?= htmlspecialchars($cat) ?></a></li>
        <?php endforeach; ?>

        <!-- Always show these links, regardless of login status -->
        <li><a class="categoryLink" href="login.php">Login</a></li>
        <li><a class="categoryLink" href="register.php">Register</a></li>
        <li><a class="categoryLink" href="addAuction.php">Add Auction</a></li>
        <li><a class="categoryLink" href="addadmin.php">Admin</a></li>
    </ul>
</nav>

<img src="banners/1.jpg" alt="Banner" style="width: 100%; max-height: 300px; object-fit: cover;" />

<main>
    <h1>
        <?= isset($_GET['search']) ? 'Search Results' :
           (isset($_GET['category']) ? 'Category: ' . htmlspecialchars($_GET['category']) :
            'Ending Soon – Top 10 Auctions') ?>
    </h1>

    <ul class="carList">
        <?php if (!empty($auctions)): ?>
            <?php foreach ($auctions as $auction): ?>
                <li>
                    <img src="<?= htmlspecialchars($auction['imagePath']) ?>" alt="<?= htmlspecialchars($auction['title']) ?>" />
                    <article>
                        <h2><?= htmlspecialchars($auction['title']) ?></h2>
                        <h3><?= htmlspecialchars($auction['category_name']) ?></h3>
                        <p><?= nl2br(htmlspecialchars(substr($auction['description'], 0, 300))) ?>...</p>
                        <p class="price">Current bid: £<?= number_format($auction['current_bid'] ?? 0, 2) ?></p>
                        <p class="countdown" data-end="<?= $auction['endDate'] ?>">Loading...</p>
                        <a href="auction.php?id=<?= $auction['id'] ?>" class="more auctionLink">More &gt;&gt;</a>
                    </article>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No auctions found.</p>
        <?php endif; ?>
    </ul>
</main>

<footer>
    &copy; Carbuy <?= date('Y') ?>
</footer>
</body>
</html>
