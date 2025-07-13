<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");//cndkfh dkvlidl vfckdnvldrjlfdlfd
    exit;//cndlfv vljfdgrvvfdf
}

$message = '';//cnsdciesv fdjrnv fldjgrdjl
$categories = [];//vfmv;lfmv vfdlgkmfl

try {
    $stmt = $pdo->query("SELECT id, name FROM categories");//cnbdhfv hdirnv .ilfdjrgn vf.kdr
    $categories = $stmt->fetchAll();//shilesf dkhrig vfdilghrihgndrgb fbkidrgr
} catch (PDOException $e) {
    $message = "❌ Error loading categories: " . $e->getMessage();//cndhivd vdjvdliovgdrhtg
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);//cndkfi bkvhfdkf kvld
    $description = trim($_POST['description']);//descriptionnn
    $endDate = $_POST['endDate'];//enddatebgbf
    $categoryId = $_POST['category'];//nhscds cdknvdlfjldfr
    $imagePath = '';//vnkf v.dfnvdlfn fd bdlf

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'as1/banners/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $imagePath = $uploadPath;
        } else {
            $message = "❌ Failed to upload image.";
        }
    }

    // Insert into database
    if ($imagePath && $title && $description && $endDate && $categoryId) {
        try {
            $stmt = $pdo->prepare("INSERT INTO auctions (title, description, endDate, imagePath, categoryId, userId)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $endDate, $imagePath, $categoryId, $_SESSION['user_id']]);//cnhldsifc dckdsef d.chksifhei
            $message = "✅ Auction added successfully!";//vlnvdv cnvfdov cl/vnofd
        } catch (PDOException $e) {
            $message = "❌ Error saving auction: " . $e->getMessage();//cndbkchd dvodslmv vldjfp;kd
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Auction - CarBuy</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        form { background: white; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 10px; margin-top: 5px; }
        input[type="submit"] {
            background: #28a745; color: white; border: none; margin-top: 20px;
            cursor: pointer; font-size: 16px;
        }
        input[type="submit"]:hover { background: #218838; }
        .message {
            text-align: center; margin-top: 20px; font-weight: bold;
            color: <?= strpos($message, '✅') !== false ? 'green' : 'red' ?>;
        }
    </style>
</head>
<body>

<h2>Add Auction</h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Description:</label>
    <textarea name="description" required></textarea>

    <label>End Date:</label>
    <input type="datetime-local" name="endDate" required>

    <label>Category:</label>
    <select name="category" required>
        <option value="">-- Select Category --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Upload Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <input type="submit" value="Add Auction">
</form>

</body>
</html>
