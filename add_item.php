<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Not an admin or not logged in.";
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve new item details from the form
    $newItemName = $_POST['new_item_name'];
    $price = $_POST['price'];
    $categoryID = $_POST['category_id'];
    $image_url = $_POST['image_url'];

    // Add code to insert the new item into the database
    $addItemSql = "INSERT INTO items (ItemName, Price, CategoryID, image_url) VALUES (:newItemName, :price, :categoryID, :image_url)";
    $addItemStatement = $pdo->prepare($addItemSql);

    if ($addItemStatement->execute(['newItemName' => $newItemName, 'price' => $price, 'categoryID' => $categoryID, 'image_url' => $image_url])) {
        echo "Item added successfully.";
    } else {
        echo "Failed to add item. Error: " . implode(" ", $addItemStatement->errorInfo());
    }
}
?>

<!-- The HTML form for adding a new item -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <!-- Add your stylesheets or other meta tags here -->
</head>
<body>
    <h2>Add New Item</h2>
    
    <form action="add_item.php" method="post">
        <label for="new_item_name">Item Name:</label>
        <input type="text" name="new_item_name" required>

        <label for="price">Price:</label>
        <input type="number" name="price" required>

        <label for="category_id">Category ID:</label>
        <input type="number" name="category_id" required>

        <label for="image_url">Image URL:</label>
        <input type="text" name="image_url" required>

        <!-- Add other fields as needed -->

        <button type="submit">Add Item</button>
    </form>
    <a href="admin.php">Back to Admin Dashboard</a>

</body>
</html>