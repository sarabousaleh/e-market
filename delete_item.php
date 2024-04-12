<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Not an admin or not logged in.";
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

if (isset($_POST['delete_item'])) {
    $itemIdToDelete = $_POST['delete_item'];
    
    // Add code to delete item from the database
    $deleteItemSql = "DELETE FROM items WHERE ItemID = :itemIdToDelete";
    $deleteItemStatement = $pdo->prepare($deleteItemSql);

    if ($deleteItemStatement->execute(['itemIdToDelete' => $itemIdToDelete])) {
        echo "Item deleted successfully.";
    } else {
        echo "Failed to delete item. Error: " . implode(" ", $deleteItemStatement->errorInfo());
    }
}

// Redirect back to the admin page
header("Location: admin.php");
exit();
?>
