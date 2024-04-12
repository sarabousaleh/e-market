<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Site Metas -->
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>e-market</title>
    <link rel="stylesheet" type="text/css" href="css/mystyle.css" />

    <link href="css/style.css" rel="stylesheet" />
    <link href="css/mystyle.css" rel="stylesheet" />

    <!-- Add a link to a CSS file or include the styles directly here -->
</head>
<body>

<?php
// Connect to your database (replace dbname, username, password with your actual database credentials)
$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

// Get category ID from the URL
$categoryID = isset($_GET['CategoryID']) ? $_GET['CategoryID'] : 1;

// Fetch items for the selected category
$queryItems = $pdo->prepare('SELECT * FROM items WHERE CategoryID = ?');
$queryItems->execute([$categoryID]);
$items = $queryItems->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .back-arrow {
        font-size: 40px;
        font-weight: bold;
    }

    .blog_section {
        padding: 20px 0;
    }

    .heading_container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-evenly;
        text-align: center;
    }

    h2 {
        color: #6a217c;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        margin: -10px; /* Add negative margin to counteract margin on individual items */
    }

    .col-md-4 {
        width: calc(33.33% - 20px); /* Adjust width and margin based on your styling */
        margin: 10px;
    }

    .box {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s;
        cursor: pointer;
        width: 100%;
    }

    .box:hover {
        transform: translateY(-5px);
    }

    .detail-box {
        display: flex; /* Display items horizontally */
        flex-direction: column; /* Align item details vertically */
        align-items: center; /* Center items horizontally */
        padding: 20px;
    }

    h5 {
        color: #333;
        margin-bottom: 10px;
    }

    p {
        color: #666;
        margin-bottom: 10px;
    }

    .item-image {
        width: 100%;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px; /* Adjust the value to control the roundness */
        margin-bottom: 10px;
    }

    .price-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .shopping-basket-icon {
        font-size: 20px;
        color: #6a217c;
    }

    .navbar-fixed-bottom {
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        position: fixed;
        bottom: 0;
        width: 100%;
        background-color: #fff;
        color: #6a217c;
        padding: 10px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    }

    /* Responsive styles */
    @media screen and (max-width: 768px) {
        .container {
            padding: 10px;
        }

        .col-md-4 {
            width: calc(50% - 20px); /* Two items per row on small screens */
        }
    }
</style>

<div class="back-arrow">
    <a href="index.php">&#8592;</a>
</div>

<section class="blog_section">
    <div class="container">
        <div class="heading_container">
            <h2>Items</h2>
        </div>

        <div class="row">
            <?php foreach ($items as $item): ?>
                <div class="col-md-4">
                    <div class="box">
                        <div class="detail-box">
                            <!-- Display item details -->
                            <?php if (isset($item['image_url'])): ?>
                                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['ItemName']; ?>" class="item-image">
                            <?php endif; ?>
                            <h5><?php echo $item['ItemName']; ?></h5>
                            <div class="price-container">
                                <p>Price: $<?php echo $item['Price']; ?></p>
                                <!-- Check if $item has 'ItemID' key before using it in the link -->
                                <?php if (isset($item['ItemID'])): ?>
                                    <a href="cart.php?addItem=<?php echo $item['ItemID']; ?>">
                                        <img src="images/cart.png" alt="Add to Cart" style="width: 24px; height: 24px;">
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<nav class="navbar-fixed-bottom">
    <a class="nav-link" href="index.php" style="font-size: 20px;">Home</a>
    <a class="nav-link" href="search.php" style="font-size: 20px;">Search</a>
    <a class="nav-link" href="orders.php" style="font-size: 20px;">Orders</a>
    <a class="nav-link" href="profile.php" style="font-size: 20px;">Profile</a>
</nav>

</body>
</html>
