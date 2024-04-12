<!-- admin.php -->
<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Not an admin or not logged in.";
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

// Handle user deletion
if (isset($_GET['confirmed_delete_user'])) {
    $userIdToDelete = $_GET['confirmed_delete_user'];

    $deleteUserSql = "DELETE FROM users WHERE UserID = :userIdToDelete";
    $deleteUserStatement = $pdo->prepare($deleteUserSql);

    try {
        $deleteUserStatement->execute(['userIdToDelete' => $userIdToDelete]);
        echo "User deleted successfully.";
    } catch (Exception $e) {
        echo "Failed to delete user. Error: " . $e->getMessage();
    }
}

// Handle item deletion
if (isset($_GET['confirmed_delete_item'])) {
    $itemIdToDelete = $_GET['confirmed_delete_item'];

    $deleteItemSql = "DELETE FROM items WHERE ItemID = :itemIdToDelete";
    $deleteItemStatement = $pdo->prepare($deleteItemSql);

    try {
        $deleteItemStatement->execute(['itemIdToDelete' => $itemIdToDelete]);
        echo "Item deleted successfully.";
    } catch (Exception $e) {
        echo "Failed to delete item. Error: " . $e->getMessage();
    }
}

// Handle marking an order as done
if (isset($_POST['mark_order'])) {
    $userIdToMark = $_POST['mark_order'];

    // Add code to update order status and delete order details
    $markOrderSql = "UPDATE orders SET Status = 'done' WHERE UserID = :userIdToMark";
    $deleteOrderDetailsSql = "DELETE FROM orderdetails WHERE OrderID IN (SELECT OrderID FROM orders WHERE UserID = :userIdToMark)";
    
    $markOrderStatement = $pdo->prepare($markOrderSql);
    $deleteOrderDetailsStatement = $pdo->prepare($deleteOrderDetailsSql);

    $pdo->beginTransaction();

    try {
        $markOrderStatement->execute(['userIdToMark' => $userIdToMark]);
        $deleteOrderDetailsStatement->execute(['userIdToMark' => $userIdToMark]);
        $pdo->commit();
        echo "Order marked as done successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to mark order as done. Error: " . $e->getMessage();
    }
}

// Fetch all users with their most recent order details
$sql = "SELECT users.UserID, users.Username, MAX(orders.OrderDate) AS RecentOrderDate, GROUP_CONCAT(items.ItemID) AS ItemIDs, GROUP_CONCAT(items.ItemName) AS ItemNames, GROUP_CONCAT(orders.OrderID) AS OrderIDs
        FROM users
        LEFT JOIN orders ON users.UserID = orders.UserID
        LEFT JOIN orderdetails ON orders.OrderID = orderdetails.OrderID
        LEFT JOIN items ON orderdetails.ItemID = items.ItemID
        GROUP BY users.UserID, users.Username";
$userOrders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Fetch all items
$itemSql = "SELECT * FROM items";
$items = $pdo->query($itemSql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/mystyle.css" rel="stylesheet" />
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <style>
        /* Add styles specific to the admin page */
        .container {
            text-align: center;
        }

        .user-section,
        .order-section {
            margin-top: 20px;
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            color: black;
        }

        .item-section {
            margin-top: 20px;
            overflow-x: auto;
        }

        .item-table {
            width: 100%;
            white-space: nowrap;
        }

        /* Add User button styling */
        .add-user-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50; /* Green background color */
            color: white; /* White text color */
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        /* Add Item button styling */
        .add-item-button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #008CBA; /* Blue background color */
            color: white; /* White text color */
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        /* Style for confirmation prompt */
        .confirmation-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .confirmation-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .confirmation-buttons {
            margin-top: 10px;
        }

        .confirmation-button {
            padding: 10px 15px;
            margin: 0 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
        }

        .cancel-button {
            background-color: #f44336;
        }
    </style>
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>

        <!-- Add User button -->
        <div class="user-section">
            <a href="add_user.php" class="add-user-button">Add User</a>
        </div>

        <!-- Add Item button -->
        <div class="item-section">
            <a href="add_item.php" class="add-item-button">Add Item</a>
        </div>

        <!-- Display All Users with Most Recent Order Details -->
        <div class="user-section">
            <h3>User Summary</h3>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Recent Order Date</th>
                    <th>Item IDs</th>
                    <th>Item Names</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($userOrders as $userOrder): ?>
                    <tr>
                        <td><?php echo $userOrder['UserID']; ?></td>
                        <td><?php echo $userOrder['Username']; ?></td>
                        <td><?php echo $userOrder['RecentOrderDate']; ?></td>
                        <td><?php echo $userOrder['ItemIDs']; ?></td>
                        <td><?php echo $userOrder['ItemNames']; ?></td>
                        <td>
                            <?php if (isset($userOrder['OrderIDs'])): ?>
                                <form method="post" action="admin.php">
                                    <input type="hidden" name="mark_order" value="<?php echo $userOrder['UserID']; ?>">
                                    <input type="hidden" name="status" value="done">
                                    <button type="button" onclick="showConfirmation('Are you sure you want to mark this order as done?', 'Mark as Done', 'done')">Mark as Done</button>
                                </form>
                                <form method="post" action="admin.php">
                                    <input type="hidden" name="mark_order" value="<?php echo $userOrder['UserID']; ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="button" onclick="showConfirmation('Are you sure you want to reject this order?', 'Reject', 'rejected')">Reject</button>
                                </form>
                                <form method="post" action="admin.php">
                                    <input type="hidden" name="mark_order" value="<?php echo $userOrder['UserID']; ?>">
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="button" onclick="showConfirmation('Are you sure you want to accept this order?', 'Accept', 'accepted')">Accept</button>
                                </form>

                                <!-- Link for updating user -->
                                <a href="update_user.php?user_id_to_update=<?php echo $userOrder['UserID']; ?>">Update User</a>

                                <!-- Form for user deletion -->
                                <button type="button" onclick="showConfirmation('Are you sure you want to delete this user?', 'Delete User', 'delete_user', <?php echo $userOrder['UserID']; ?>)">Delete User</button>
                                
                                <!-- Form for past orders -->
                                <form method="post" action="pastorders.php">
                                    <input type="hidden" name="past_orders2" value="<?php echo $userOrder['UserID']; ?>">
                                    <button type="submit">Past Orders</button>
                                </form>
                            <?php else: ?>
                                No orders available
                                <!-- Link for updating user -->
                                <a href="update_user.php?user_id_to_update=<?php echo $userOrder['UserID']; ?>">Update User</a>

                                <!-- Form for user deletion -->
                                <button type="button" onclick="showConfirmation('Are you sure you want to delete this user?', 'Delete User', 'delete_user', <?php echo $userOrder['UserID']; ?>)">Delete User</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="item-section">
            <h3>Item Details</h3>
            <table class="item-table">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['ItemID']; ?></td>
                        <td><?php echo $item['ItemName']; ?></td>
                        <td><?php echo $item['Price']; ?></td>
                        <td><?php echo $item['CategoryID']; ?></td>
                        <td><?php echo $item['image_url']; ?></td>
                        <td>
                            <!-- Form for deleting item -->
                            <button type="button" onclick="showConfirmation('Are you sure you want to delete this item?', 'Delete Item', 'delete_item', <?php echo $item['ItemID']; ?>)">Delete Item</button>
                            
                            <!-- Link for editing item -->
                            <a href="edit_item.php?edit_item=<?php echo $item['ItemID']; ?>">Edit Item</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

 <!-- Confirmation prompt container -->
<div id="confirmation-container" class="confirmation-container">
    <div class="confirmation-box">
        <p id="confirmation-message"></p>
        <div class="confirmation-buttons">
            <button class="confirmation-button" onclick="confirmAction()">OK</button>
            <button class="confirmation-button cancel-button" onclick="cancelAction()">Cancel</button>
        </div>
    </div>
</div>

<script>
    // Variables to store the action and actionType
    let currentAction = null;
    let currentActionType = null;
    let currentItemId = null;

    // Function to display the confirmation prompt
    function showConfirmation(message, action, actionType, itemId = null) {
        document.getElementById('confirmation-message').innerHTML = message;
        document.getElementById('confirmation-container').style.display = 'flex';

        // Set action and actionType in the global scope for later use
        currentAction = action;
        currentActionType = actionType;
        currentItemId = itemId;
    }

    // Function to confirm the action
    function confirmAction() {
        document.getElementById('confirmation-container').style.display = 'none';

        if (currentActionType === 'delete_user') {
            window.location.href = 'admin.php?confirmed_delete_user=' + currentItemId;
        } else if (currentActionType === 'delete_item') {
            window.location.href = 'admin.php?confirmed_delete_item=' + currentItemId;
        } else {
            document.forms[currentActionType].submit();
        }
    }

    // Function to cancel the action
    function cancelAction() {
        document.getElementById('confirmation-container').style.display = 'none';
    }
</script>


</body>
</html>
