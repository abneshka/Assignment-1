<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');//xsbs cxjskcm,c nxkcnsks
    exit;//cjbcddjbcmjcbkjcs
}
require 'db.php';//xsxjs xs hcshbsjbjbs

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || !$_SESSION['admin_loggedin']) {
    header('Location: adminCategories.php'); // redirect to adminCategories.php which will show login
    exit;
}

$categoryId = $_GET['id'] ?? null;//sxbk.sjx cmsjbc xcsjkcnmcbdj

if (!$categoryId) {
    die("Category ID not specified.");//xsbksjb csbcdksncslkcs
}

try {
    // Delete category by id
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);

    // Redirect back to adminCategories page
    header('Location: adminCategories.php');
    exit;
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
