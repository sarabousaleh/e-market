<!-- pastorders.php -->
<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Not an admin or not logged in.";
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

// Check if the user ID is provided in the query parameter
if (isset($_POST['past_orders2'])) {
    $userIdForOrders = $_POST['past_orders2'];

    // Fetch past orders with a status of 'done'
    $pastOrdersSql = "SELECT orders.OrderID, orders.OrderDate, orderdetails.OrderDetailID, items.ItemName, orderdetails.Quantity
                      FROM orders
                      JOIN orderdetails ON orders.OrderID = orderdetails.OrderID
                      JOIN items ON orderdetails.ItemID = items.ItemID
                      WHERE orders.UserID = :userIdForOrders AND orders.Status = 'done'";
    $pastOrdersStatement = $pdo->prepare($pastOrdersSql);

    if ($pastOrdersStatement->execute(['userIdForOrders' => $userIdForOrders])) {
        $pastOrdersData = $pastOrdersStatement->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Failed to fetch past orders. Error: " . implode(" ", $pastOrdersStatement->errorInfo());
    }
} else {
    // Handle the case where no user ID is provided
    echo "User ID not provided.";
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
    <title>Past Orders</title>
</head>
<body>
    <div class="container">
        <h2>Past Orders</h2>

        <?php if (isset($pastOrdersData) && !empty($pastOrdersData)): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Order Detail ID</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                </tr>
                <?php foreach ($pastOrdersData as $order): ?>
                    <tr>
                        <td><?php echo $order['OrderID']; ?></td>
                        <td><?php echo $order['OrderDate']; ?></td>
                        <td><?php echo $order['OrderDetailID']; ?></td>
                        <td><?php echo $order['ItemName']; ?></td>
                        <td><?php echo $order['Quantity']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No past orders for the selected user.</p>
        <?php endif; ?>

        <br>
        <a href="admin.php">Back to Admin Dashboard</a>
    </div>
</body>
</html>
