<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve user ID from the session
$userID = $_SESSION['user_id'];

// Check if the cart is not empty
if (!empty($_SESSION['cart'])) {
    // Connect to your database (replace dbname, username, password with your actual database credentials)
    $pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

    // Insert the order into the orders table
    $orderDate = date('Y-m-d H:i:s'); // Current date and time

    $insertOrder = $pdo->prepare('INSERT INTO orders (UserID, OrderDate) VALUES (?, ?)');
    $insertOrder->execute([$userID, $orderDate]);
    $orderID = $pdo->lastInsertId();

    // Insert order items into the orderdetails table
    foreach ($_SESSION['cart'] as $itemID => $quantity) {
        $insertOrderDetail = $pdo->prepare('INSERT INTO orderdetails (OrderID, ItemID, Quantity) VALUES (?, ?, ?)');
        $insertOrderDetail->execute([$orderID, $itemID, $quantity]);
    }
}

// Clear the cart after processing the order
unset($_SESSION['cart']);
?>
