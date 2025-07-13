<?php
session_start();
require 'db.php';

$auctionId = $_GET['id'] ?? null;
if (!$auctionId) {
    die("Auction ID not specified.");
}

$message = '';
$reviews = [];

try {
    $stmt = $pdo->prepare("SELECT a.*, c.name AS category_name, u.id AS author_id, u.username AS author_username
                           FROM auctions a
                           JOIN categories c ON a.categoryId = c.id
                           JOIN users u ON a.userId = u.id
                           WHERE a.id = ?");
    $stmt->execute([$auctionId]);
    $auction = $stmt->fetch();

    if (!$auction) {
        die("Auction not found.");
    }

    $bidCountStmt = $pdo->prepare("SELECT COUNT(*) FROM bids WHERE auctionId = ?");//cndclkdsncd cd,kscndks
    $bidCountStmt->execute([$auctionId]);//sdjcd;sfsdlfe;
    $bidCount = $bidCountStmt->fetchColumn();///cdlnfdk.nvdlk

    $highestBidStmt = $pdo->prepare("SELECT MAX(amount) FROM bids WHERE auctionId = ?");//ckhcskhicdjsdgjsdg
    $highestBidStmt->execute([$auctionId]);//dkuwdbjsbcdskjdcbdkckhds
    $highestBid = $highestBidStmt->fetchColumn() ?? 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['submit_bid']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $bidAmount = floatval($_POST['bidAmount']);
            if ($bidAmount <= $highestBid) {
                $message = "Your bid must be higher than the current highest bid ($highestBid).";
            } else {
                $insertBid = $pdo->prepare("INSERT INTO bids (auctionId, userId, amount, bidTime) VALUES (?, ?, ?, NOW())");
                $insertBid->execute([$auctionId, $_SESSION['user_id'], $bidAmount]);
                $message = "Your bid of $$bidAmount has been placed successfully!";
                $bidCountStmt->execute([$auctionId]);
                $bidCount = $bidCountStmt->fetchColumn();
                $highestBidStmt->execute([$auctionId]);
                $highestBid = $highestBidStmt->fetchColumn();
            }
        }

        if (isset($_POST['submit_review']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
            $reviewText = trim($_POST['reviewText']);
            if ($reviewText !== '') {
                $insertReview = $pdo->prepare("INSERT INTO review (reviewText, reviewed_user_id, reviewer_id, created_at)
                                               VALUES (?, ?, ?, NOW())");
                $insertReview->execute([$reviewText, $auction['author_id'], $_SESSION['user_id']]);
                $message = "Review submitted successfully.";
            }
        }
    }

    $reviewStmt = $pdo->prepare("SELECT r.reviewText, r.created_at, u.username AS reviewer_name
                                 FROM review r
                                 JOIN users u ON r.reviewer_id = u.id
                                 WHERE r.reviewed_user_id = ?
                                 ORDER BY r.created_at DESC");
    $reviewStmt->execute([$auction['author_id']]);
    $reviews = $reviewStmt->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($auction['title']) ?> - Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
    <style>
        .auction-details {
            max-width: 900px;
            margin: 20px auto;
            display: flex;
            gap: 20px;
        }
        .auction-image {
            width: 350px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid #ddd;
        }
        .auction-info {
            flex: 1;
        }
        .form-section, .reviews {
            max-width: 900px;
            margin: 30px auto;
        }
        .form-section form, .reviews form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input, textarea, button {
            padding: 10px;
            font-size: 1em;
        }
        button {
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1a4ed0;
        }
        .message {
            max-width: 900px;
            margin: 20px auto;
            color: red;
            font-weight: bold;
        }
        .review {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }
        .review .reviewer {
            font-weight: bold;
            color: #2575fc;
        }
        .review .date {
            font-size: 0.85em;
            color: #666;
        }
        header {
            background-color: white;
            padding: 1em;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }
        nav {
            margin-top: 10px;
            font-size: 1.1em;
        }
        nav a {
            text-decoration: none;
            color: #2575fc;
            font-weight: bold;
            margin: 0 10px;
        }
        footer {
            margin-top: 40px;
            text-align: center;
            padding: 20px;
            color: #777;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<header>
    <h1>Carbuy Auctions</h1>
    <nav>
        <a href="index.php">Home</a> |
        <a href="category.php">Categories</a>
        <?php if (!empty($_SESSION['loggedin'])): ?>
            | <a href="addAuction.php">Add Auction</a>
            <?php if ($_SESSION['user_id'] == $auction['author_id']): ?>
                | <a href="editAuction.php?id=<?= $auctionId ?>">Edit Auction</a>
            <?php endif; ?>
            | <a href="logout.php">Logout</a>
        <?php else: ?>
            | <a href="login.php">Login</a>
            | <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <div class="auction-details">
        <img src="<?= htmlspecialchars($auction['imagePath']) ?>" class="auction-image" />
        <div class="auction-info">
            <h2><?= htmlspecialchars($auction['title']) ?></h2>
            <p><strong>Category:</strong> <?= htmlspecialchars($auction['category_name']) ?></p>
            <p><strong>Ends:</strong> <?= htmlspecialchars($auction['endDate']) ?></p>
            <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
            <p><strong>Author:</strong> <?= htmlspecialchars($auction['author_username']) ?></p>
            <p><strong>Total Bids:</strong> <?= $bidCount ?></p>
            <p><strong>Highest Bid:</strong> $<?= number_format($highestBid, 2) ?></p>
        </div>
    </div>

    <?php if (!empty($_SESSION['loggedin'])): ?>
        <div class="form-section">
            <form method="post">
                <h3>Place a Bid</h3>
                <input type="number" name="bidAmount" step="0.01" min="<?= $highestBid + 0.01 ?>" placeholder="Enter your bid..." required>
                <button type="submit" name="submit_bid">Submit Bid</button>
            </form>
        </div>

        <div class="form-section">
            <form method="post">
                <h3>Leave a Review for <?= htmlspecialchars($auction['author_username']) ?></h3>
                <textarea name="reviewText" placeholder="Write your review here..." required></textarea>
                <button type="submit" name="submit_review">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <p style="text-align:center;">You must <a href="login.php">log in</a> to place a bid or write a review.</p>
    <?php endif; ?>

    <div class="reviews">
        <h3>Reviews</h3>
        <?php if ($reviews): ?>
            <?php foreach ($reviews as $r): ?>
                <div class="review">
                    <p class="reviewer"><?= htmlspecialchars($r['reviewer_name']) ?> says:</p>
                    <p><?= nl2br(htmlspecialchars($r['reviewText'])) ?></p>
                    <p class="date"><?= htmlspecialchars($r['created_at']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; Carbuy <?= date('Y') ?></p>
</footer>

</body>
</html>
