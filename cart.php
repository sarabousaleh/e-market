<?php
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Check if the addItem parameter is set in the URL
if (isset($_GET['addItem'])) {
    // Retrieve the ItemID from the URL
    $itemID = $_GET['addItem'];

    // Initialize the cart as an array if it doesn't exist in the session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the item to the cart (you can modify this part based on your database structure)
    // For simplicity, we're storing the item ID and quantity in the cart
    if (isset($_SESSION['cart'][$itemID])) {
        // Increment quantity if the item is already in the cart
        $_SESSION['cart'][$itemID]++;
    } else {
        // Add the item to the cart with a quantity of 1
        $_SESSION['cart'][$itemID] = 1;
    }
}

// Handle clearing the cart
if (isset($_GET['clearCart'])) {
    unset($_SESSION['cart']);
}

// Handle deleting an item from the cart
if (isset($_GET['deleteItem'])) {
    $deleteItemID = $_GET['deleteItem'];
    if (isset($_SESSION['cart'][$deleteItemID])) {
        unset($_SESSION['cart'][$deleteItemID]);
    }
}

// Handle increasing the quantity of an item
if (isset($_GET['increaseQuantity'])) {
    $increaseItemID = $_GET['increaseQuantity'];
    if (isset($_SESSION['cart'][$increaseItemID])) {
        $_SESSION['cart'][$increaseItemID] = intval($_SESSION['cart'][$increaseItemID]) + 1;
    }
}

// Handle decreasing the quantity of an item
if (isset($_GET['decreaseQuantity'])) {
    $decreaseItemID = $_GET['decreaseQuantity'];
    if (isset($_SESSION['cart'][$decreaseItemID]) && $_SESSION['cart'][$decreaseItemID] > 1) {
        $_SESSION['cart'][$decreaseItemID] = intval($_SESSION['cart'][$decreaseItemID]) - 1;
    }
}

// Display the contents of the cart
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/mystyle.css" rel="stylesheet" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <title>e-market</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #ccc;
            margin: 0;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            max-width: 400px;
            width: 80%;
            padding: 20px;
            margin: 20px auto; /* Center the container */
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .back-link {
            font-size: 28px;
            text-decoration: none;
            color: #333;
            margin-bottom: 10px;
            display: block;
            cursor: pointer; /* Add cursor style */
        }

        h2 {
            color: #6a217c;
            text-align: center;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .quantity-modify-btns {
            display: flex;
            align-items: center;
        }

        .quantity-modify-btn {
            background-color: #6a217c;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 5px;
        }

        .quantity {
            margin: 0 10px;
        }

        .delete-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: auto;
        }

        .clear-cart-btn,
        .checkout-btn {
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
            text-align: center; /* Center text within the button */
        }

        .delete-btn:hover {
            background-color: #280d2e;
        }

        .clear-cart-btn:hover,
        .checkout-btn:hover {
            background-color: #280d2e;
        }

        /* Fixed menu bar at the bottom */
        .navbar-fixed-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-link {
            font-size: 20px;
            padding: 10px;
            text-decoration: none;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
     <a class="back-link" onclick="goBack()">&larr;</a>
    <h2>Shopping Cart</h2>
    <ul>
        <?php
        // Check if 'cart' key is set in the session
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $itemID => $quantity) {
                // Fetch item details from the database based on the ItemID
                // Adjust this part based on your actual database structure
                $itemDetails = fetchItemDetails($itemID);
                ?>
                <li>
                    <div class="quantity-modify-btns">
                        <button class="quantity-modify-btn" onclick="decreaseQuantity(<?php echo $itemID; ?>)">-</button>
                        <span class="quantity"><?php echo $quantity; ?></span>
                        <button class="quantity-modify-btn" onclick="increaseQuantity(<?php echo $itemID; ?>)">+</button>
                    </div>
                    <strong><?php echo $itemDetails['ItemName']; ?></strong>
                    <button class="delete-btn" onclick="deleteItem(<?php echo $itemID; ?>)">Delete</button>
                </li>
                <?php
            }
        }
        ?>
    </ul>
    <button class="clear-cart-btn" onclick="clearCart()">Clear Cart</button>
    <button class="checkout-btn" onclick="checkout()">Checkout</button>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    // Check if the page is being reloaded
    var isReloading = <?php echo isset($_GET['reload']) ? $_GET['reload'] : '0'; ?>;

    function clearCart() {
        window.location.href = 'cart.php?clearCart=1';
    }

    function deleteItem(itemID) {
        window.location.href = 'cart.php?deleteItem=' + itemID + '&reload=' + isReloading;
    }

    function increaseQuantity(itemID) {
        window.location.href = 'cart.php?increaseQuantity=' + itemID + '&reload=' + isReloading;
    }

    function decreaseQuantity(itemID) {
        window.location.href = 'cart.php?decreaseQuantity=' + itemID + '&reload=' + isReloading;
    }

    function checkout() {
        window.location.href = 'orders.php';
    }

    // JavaScript function to go back to the previous page
    function goBack() {
        window.history.back();
    }
</script>

<?php include('navbar.php'); ?>
  </nav>

</body>
</html>
<?php

// Function to fetch item details from the database (replace this with your actual database query)
function fetchItemDetails($itemID) {
    // Connect to your database (replace dbname, username, password with your actual database credentials)
    $pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

    // Fetch item details based on the ItemID
    $query = $pdo->prepare('SELECT * FROM items WHERE ItemID = ?');
    $query->execute([$itemID]);
    return $query->fetch(PDO::FETCH_ASSOC);
}
?>