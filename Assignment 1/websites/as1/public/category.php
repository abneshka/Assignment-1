<?php
session_start();
require 'db.php';

$categoryName = $_GET['category'] ?? '';//snc.ksc k.hdcbscjls
if ($categoryName === '') {
    // Redirect to home page if no category specified
    header('Location: index.php');//sgxsjabhn cvhdvcjsj
    exit;
}

try {
    // Fetch all categories for nav
    $catStmt = $pdo->query("SELECT name FROM categories ORDER BY name");//hsxdkshksbsicsks
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);//skdsjcjn chdskcnsdl

    // Get auctions in this category
    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS category_name,
        (SELECT MAX(amount) FROM bids WHERE auctionId = a.id) AS current_bid
        FROM auctions a
        JOIN categories c ON a.categoryId = c.id
        WHERE c.name = ?
        ORDER BY a.endDate ASC
    ");
    $stmt->execute([$categoryName]);//xbskbsj sckjsbcklc
    $auctions = $stmt->fetchAll();//cdkjsbcd cdbcskdcnhsks

} catch (Exception $e) {
    $error = $e->getMessage();//afahysf xhfsy xsvxyus
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($categoryName) ?> - CarBuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        /* Add side-by-side layout for auction list items */
        ul.carList li {
            display: grid;
            grid-template-columns: 300px 1fr;
            grid-gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #ddd;
            align-items: start;
        }
        ul.carList li img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        ul.carList li article h2 {
            margin-top: 0;
            font-size: 2em;
            color: #222;
        }
        ul.carList li article h3 {
            font-weight: normal;
            color: #555;
            margin: 0.25rem 0 1rem;
        }
        ul.carList li article p {
            font-size: 1.1em;
            color: #333;
        }
        ul.carList li article .price {
            color: red;
            font-weight: bold;
            font-size: 1.3em;
            margin-top: 1rem;
        }
        ul.carList li article .more {
            display: inline-block;
            margin-top: 1rem;
            background-color: #3665f3;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        ul.carList li article .more:hover {
            background-color: #2547b0;
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
        <input type="text" name="search" placeholder="Search for a car">
        <input type="submit" value="Search">
    </form>
</header>

<nav>
    <ul>
        <li><a class="categoryLink" href="index.php">Home</a></li>
        <?php foreach ($categories as $cat): ?>
            <li><a class="categoryLink" href="category.php?category=<?= urlencode($cat) ?>"><?= htmlspecialchars($cat) ?></a></li>
        <?php endforeach; ?>

        <?php if (isset($_SESSION['username'])): ?>
            <li><a class="categoryLink" href="addAuction.php">Add Auction</a></li>
            <li><a class="categoryLink" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="categoryLink" href="login.php">Login</a></li>
            <li><a class="categoryLink" href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<main>
    <h1>Category: <?= htmlspecialchars($categoryName) ?></h1>

    <?php if (!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php elseif (empty($auctions)): ?>
        <p>No auctions found in this category.</p>
    <?php else: ?>
        <ul class="carList">
            <?php foreach ($auctions as $auction): ?>
                <li>
                    <img src="<?= htmlspecialchars($auction['imagePath']) ?>" alt="<?= htmlspecialchars($auction['title']) ?>">
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
        </ul>
    <?php endif; ?>
</main>

<footer>
    &copy; Carbuy <?= date('Y') ?>
</footer>
</body>
</html>
