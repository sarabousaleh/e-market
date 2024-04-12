<?php
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Connect to the database
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "emarket";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user orders
$sql = "SELECT * FROM orders WHERE UserID = '$user_id'";
$result = $conn->query($sql);

$userOrders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderID = $row['OrderID'];

        // Fetch order details from orderdetails table
        $orderDetailsSql = "SELECT * FROM orderdetails WHERE OrderID = '$orderID'";
        $orderDetailsResult = $conn->query($orderDetailsSql);

        $order = $row;
        $order['items'] = [];

        if ($orderDetailsResult->num_rows > 0) {
            while ($orderDetail = $orderDetailsResult->fetch_assoc()) {
                // Fetch item details for each order detail
                $itemID = $orderDetail['ItemID'];
                $itemSql = "SELECT * FROM items WHERE ItemID = '$itemID'";
                $itemResult = $conn->query($itemSql);

                if ($itemResult->num_rows > 0) {
                    $itemDetails = $itemResult->fetch_assoc();
                    $order['items'][] = [
                        'ItemName' => $itemDetails['ItemName'],
                        'Quantity' => $orderDetail['Quantity'],
                        // Add other item details as needed
                    ];
                }
            }
        }

        $userOrders[] = $order;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <title>Past Orders</title>
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/mystyle.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            /* height: 100vh; */
            background-color: #f0f0f0;
            color: #333;
        }

        .container {
            max-width: 700px;
            width: 100%;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .heading-container {
            margin-bottom: 20px;
        }

        h2 {
            color: #6a217c;
        }

        .result-container {
            display: grid;
            gap: 20px;
        }

        .result-box {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .detail-box {
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        p {
            color: #333;
            margin-bottom: 10px;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 5px;
        }

        strong {
            color: #6a217c;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="heading-container">
            <h2>Past Orders</h2>
        </div>

        <div class="result-container">
            <?php foreach ($userOrders as $order) : ?>
                <div class="result-box">
                    <div class="detail-box">
                        <!-- Display order details -->
                        <p>Order ID: <?php echo $order['OrderID']; ?></p>
                        <p>Order Date: <?php echo $order['OrderDate']; ?></p>
                        <!-- Display item details for the order -->
                        <ul>
                            <?php foreach ($order['items'] as $item) : ?>
                                <li>
                                    <strong><?php echo $item['ItemName']; ?></strong>
                                    <span>Quantity: <?php echo $item['Quantity']; ?></span>
                                    <!-- Add other item details as needed -->
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include('navbar.php'); ?>

</body>

</html>
