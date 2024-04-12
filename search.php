<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>e-market</title>

  <link rel="stylesheet" type="text/css" href="css/search.css" />
  <link rel="stylesheet" type="text/css" href="css/mystyle.css" />
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
  
    .container {
      max-width: 600px;
      width: 100%;
      padding: 20px;
      margin: 20px 0;
      border-radius: 8px;
    }
  
    .heading-container {
      text-align: center;
      margin-bottom: 20px;
    }
  
    h2 {
      color: #6a217c;
    }
  
    form {
      text-align: center;
      margin-bottom: 20px;
    }
  
    input[type="text"] {
      width: calc(100% - 22px);
      padding: 10px;
      margin: 10px 0;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
  
    input[type="submit"] {
      background-color: #6a217c;
      color: #fff;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
  
    input[type="submit"]:hover {
      background-color: #280d2e;
    }
  
    .result-container {
      max-height: 400px;
      overflow-y: auto;
      margin-top: 20px;
    }
  
    .result-box {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      margin: 10px 0;
      overflow: hidden;
      transition: transform 0.3s;
    }
  
    .detail-box {
      display: flex; /* Display items horizontally */
      align-items: center; /* Center items vertically */
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
    }
  
    h5 {
      color: #333;
      margin-bottom: 10px;
    }
  
    p {
      color: #666;
      margin-bottom: 10px; /* Add margin to separate paragraphs */
    }
  
    .item-image {
      max-width: 100px; /* Set maximum width for the image */
      margin-right: 20px; /* Add margin to the right of the image */
      border-radius: 8px;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="heading-container">
    <h2>Search for Items</h2>
  </div>

  <form method="post">
    <input type="text" name="search" id="search" placeholder="Enter item name">
    <input type="submit" value="Search">
  </form>

  <div class="result-container">
  <?php
// Process the search query
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to your database (replace dbname, username, password with your actual database credentials)
    $pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

    // Get the search query
    $search = isset($_POST['search']) ? $_POST['search'] : '';

    // Fetch items based on the search query (item names)
    $queryItems = $pdo->prepare('SELECT ItemID, ItemName, Price, image_url FROM items WHERE ItemName LIKE ?');
$queryItems->execute(["%$search%"]);
$items = $queryItems->fetchAll(PDO::FETCH_ASSOC);
    // Fetch items based on the search query (categories)
    $queryCategories = $pdo->prepare('SELECT items.ItemID, items.ItemName, items.Price, items.image_url, categories.CategoryName FROM items JOIN categories ON items.CategoryID = categories.CategoryID WHERE categories.CategoryName LIKE ?');
    $queryCategories->execute(["%$search%"]);
    $categoryItems = $queryCategories->fetchAll(PDO::FETCH_ASSOC);
    // Combine and display search results
    $combinedResults = array_merge($items, $categoryItems);


foreach ($combinedResults as $result): ?>
  <div class="result-box">
    <div class="detail-box">
      <?php if (isset($result['image_url'])): ?>
        <img src="<?php echo $result['image_url']; ?>" alt="<?php echo $result['ItemName']; ?>" class="item-image">
      <?php endif; ?>
      <div>
        <h5><?php echo $result['ItemName']; ?></h5>
        <p>Price: $<?php echo $result['Price']; ?></p>
        <?php if (isset($result['CategoryName'])): ?>
          <p>Category: <?php echo $result['CategoryName']; ?></p>
        <?php endif; ?>
        <!-- Add to Cart icon with a link to a hypothetical cart.php page -->
        <a href="cart.php?addItem=<?php echo $result['ItemID']; ?>">
          <img src="images/cart.png" alt="Add to Cart" style="width: 24px; height: 24px;">
        </a>
      </div>
    </div>
  </div>
        
<?php endforeach;
}
?>


</div>
</div>
<?php include('navbar.php'); ?>
        
</body>
</html>
        