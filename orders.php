<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if the cart is not empty
if (!empty($_SESSION['cart'])) {
    // Fetch item details and calculate total price
    $totalPrice = 0;
    $orderSummary = array();

    // Connect to your database (replace dbname, username, password with your actual database credentials)
    $pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

    foreach ($_SESSION['cart'] as $itemID => $quantity) {
        // Fetch item details from the database based on the ItemID
        $query = $pdo->prepare('SELECT * FROM items WHERE ItemID = ?');
        $query->execute([$itemID]);
        $itemDetails = $query->fetch(PDO::FETCH_ASSOC);

        // Calculate total price for each item
        $itemTotal = $itemDetails['Price'] * $quantity;
        $totalPrice += $itemTotal;

        // Create an array for the order summary
        $orderSummary[] = array(
            'ItemName' => $itemDetails['ItemName'],
            'Quantity' => $quantity,
            'ItemTotal' => $itemTotal
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/mystyle.css" rel="stylesheet" />
    <link href="css/search.css" rel="stylesheet" />
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <title>e-market</title>
    
<style>
    /* Additional styles for buttons and header */
    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .order-summary-heading {
        margin: 0; /* Remove default margin for better alignment */
    }

    .view-past-orders-btn,
    .confirm-order-btn {
        background-color: #6a217c;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            text-align: center; 
    }

    .view-past-orders-btn:hover,
    .confirm-order-btn:hover {
        background-color: #4a1664;
    }
</style>
</head>
<body>

<div class="container">
    <a class="back-link" onclick="goBack()">&larr;</a>

    <div class="header-container">
        <h2 class="order-summary-heading">Order Summary</h2>
    </div>

    <?php if (!empty($orderSummary)): ?>
        <ul>
            <?php foreach ($orderSummary as $orderItem): ?>
                <li>
                    <strong><?php echo $orderItem['ItemName']; ?></strong>
                    <span>Quantity: <?php echo $orderItem['Quantity']; ?></span>
                    <span>Total: $<?php echo $orderItem['ItemTotal']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="order-total">
            <p>Total Price: $<?php echo $totalPrice; ?></p>
        </div>
        
        <!-- Display "Confirm Order" button only if there is an order -->
        <button class="confirm-order-btn" onclick="confirmOrder()">Confirm Order</button>
        
    <?php else: ?>
        <p>Your cart is empty. Add items to proceed with the order.</p>
    <?php endif; ?>

    <!-- New button to go to past_orders.php -->
    <a href="past_orders.php"><button class="view-past-orders-btn">View Past Orders</button></a>

</div>


<script>
    function confirmOrder() {
        // Prepare the data to send to the server
        var orderData = {
            user_id: <?php echo $_SESSION['user_id']; ?>,
        };

        // Create a new FormData object and append the orderData
        var formData = new FormData();
        for (var key in orderData) {
            formData.append(key, orderData[key]);
        }

        // Create an XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Configure it as a POST request to process_order.php
        xhr.open("POST", "process_order.php", true);

        // Define a callback function to handle the response
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // If the request is successful, redirect to cart.php and clear the cart
                    alert('Thank you for your order!');
                    window.location.href = 'search.php';
                } else {
                    // If there's an error, you might want to handle it accordingly
                    alert('Order confirmation failed. Please try again.');
                }
            }
        };

        // Send the request with the FormData
        xhr.send(formData);
    }

    function goBack() {
        window.history.back();
    }
</script>

<?php include('navbar.php'); ?>
</body>
</html>