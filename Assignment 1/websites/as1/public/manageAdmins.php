<?php
session_start();
require_once 'db.php';

// Check admin login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent deleting currently logged-in admin
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");//xsjhcjmcdxbvjdmnv fdmjvbfj
    $stmt->execute([$id]);//dcndm dbvdjmv cfjfmf
    $admin = $stmt->fetch();//bcdjbc xcmdbcdjm cvbjfd

    if ($admin && $admin['username'] !== $_SESSION['admin_username']) {
        $delete = $pdo->prepare("DELETE FROM admins WHERE id = ?");//sndchksjmc xmbcdjcm xdvmj
        $delete->execute([$id]);//sxmjsb sncbdsjcb dcvmdjb dn
        header("Location: manageAdmins.php");//xsbxmsn xsbcjmc smb,jcj
        exit;//sxbjhsmx xscbjhcn scmsbjc cmsbc
    }
}

// Fetch all admins
$stmt = $pdo->query("SELECT * FROM admins ORDER BY id ASC");//cds mcmd cfdmv dvdnbvmjd
$admins = $stmt->fetchAll();//xbshcsn csnc mvsn svmjvjf
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Admins</title>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oxygen&display=swap');

        body {
            font-family: 'Oxygen', sans-serif;
            background-color: #f6f5f4;
            color: #333;
            margin: 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5em 3vw;
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        header h1 {
            font-size: 2em;
        }

        header a {
            text-decoration: none;
            color: #3665f3;
            font-weight: bold;
            font-size: 1.1em;
        }

        main {
            width: 70vw;
            margin: 4vw auto;
            background-color: white;
            padding: 3em;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 1em;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2em;
        }

        th, td {
            padding: 1em;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #f0f0f0;
        }

        td:last-child {
            text-align: right;
        }

        .btn {
            background-color: #3665f3;
            color: white;
            padding: 0.5em 1em;
            border: none;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-left: 0.5em;
        }

        .btn-danger {
            background-color: #e43137;
        }

        .top-actions {
            text-align: right;
            margin-bottom: 1em;
        }

        footer {
            text-align: center;
            margin-top: 5vw;
            padding: 2em;
            font-size: 0.9em;
            color: #aaa;
        }
    </style>
</head>
<body>

<header>
    <h1>Manage Admins</h1>
    <a href="admin_logout.php">&larr; Logout</a>
</header>

<main>
    <div class="top-actions">
        <a href="addAdmin.php" class="btn">+ Add New Admin</a>
    </div>

    <h2>Current Admin Accounts</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?= htmlspecialchars($admin['id']) ?></td>
                <td><?= htmlspecialchars($admin['username']) ?></td>
                <td>
                    <?php if ($admin['username'] !== $_SESSION['admin_username']): ?>
                        <a href="editAdmin.php?id=<?= $admin['id'] ?>" class="btn">Edit</a>
                        <a href="manageAdmins.php?delete=<?= $admin['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this admin?');">Delete</a>
                    <?php else: ?>
                        <span style="color: gray;">(you)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>

<footer>
    &copy; <?= date("Y") ?> Carbuy Admin Panel
</footer>

</body>
</html>
