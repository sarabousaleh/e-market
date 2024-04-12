<!-- edit_item.php -->
<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Not an admin or not logged in.";
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

// Check if the item ID is provided in the query parameter
if (isset($_GET['edit_item'])) {
    $itemIdToEdit = $_GET['edit_item'];

    // Fetch item information from the database and display the edit form
    $itemSql = "SELECT * FROM items WHERE ItemID = :itemIdToEdit";
    $itemStatement = $pdo->prepare($itemSql);

    if ($itemStatement->execute(['itemIdToEdit' => $itemIdToEdit])) {
        $itemData = $itemStatement->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Failed to fetch item details. Error: " . implode(" ", $itemStatement->errorInfo());
    }

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update item in the database
        $updatedItemName = $_POST['updated_item_name'];
        $updatedPrice = $_POST['updated_price'];
        $updatedCategoryId = $_POST['updated_category_id'];
        $updatedImageUrl = $_POST['updated_image_url'];

        $updateItemSql = "UPDATE items SET 
                           ItemName = :updatedItemName, 
                           Price = :updatedPrice, 
                           CategoryID = :updatedCategoryId, 
                           image_url = :updatedImageUrl 
                           WHERE ItemID = :itemIdToEdit";

        $updateItemStatement = $pdo->prepare($updateItemSql);
        $updateItemStatement->bindParam(':updatedItemName', $updatedItemName);
        $updateItemStatement->bindParam(':updatedPrice', $updatedPrice);
        $updateItemStatement->bindParam(':updatedCategoryId', $updatedCategoryId);
        $updateItemStatement->bindParam(':updatedImageUrl', $updatedImageUrl);
        $updateItemStatement->bindParam(':itemIdToEdit', $itemIdToEdit);

        if ($updateItemStatement->execute()) {
            echo "Item updated successfully.";
        } else {
            echo "Failed to update item. Error: " . implode(" ", $updateItemStatement->errorInfo());
        }
    }
} else {
    // Handle the case where no item ID is provided
    echo "Item ID not provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/mystyle.css">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <title>Edit Item</title>
</head>
<body>
    <div class="container">
        <h2>Edit Item</h2>
        <form method="post" action="edit_item.php?edit_item=<?php echo $itemIdToEdit; ?>">
            <!-- Add input fields for updating item attributes -->
            <label for="updated_item_name">Item Name:</label>
            <input type="text" name="updated_item_name" value="<?php echo $itemData['ItemName']; ?>" required>

            <label for="updated_price">Price:</label>
            <input type="number" name="updated_price" value="<?php echo $itemData['Price']; ?>" required>

            <label for="updated_category_id">Category ID:</label>
            <input type="number" name="updated_category_id" value="<?php echo $itemData['CategoryID']; ?>" required>

            <label for="updated_image_url">Image URL:</label>
            <input type="text" name="updated_image_url" value="<?php echo $itemData['image_url']; ?>" required>

            <button type="submit">Update Item</button>
        </form>
    </div>
</body>
</html>
