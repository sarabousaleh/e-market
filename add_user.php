<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Not an admin or not logged in.";
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve new user details from the form
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $newEmail = $_POST['new_email'];
    $newAge = $_POST['new_age'];
    $newGender = $_POST['new_gender'];
    $newMobileNumber = $_POST['new_mobilenumber'];
    $newAddress = $_POST['new_address'];
    $newRole = $_POST['new_role'];

    // Hash the password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Add code to insert the new user into the database
    $addUserSql = "INSERT INTO users (Username, Password, Email, Age, Gender, mobilenumber, address, Role) 
                   VALUES (:newUsername, :hashedPassword, :newEmail, :newAge, :newGender, :newMobileNumber, :newAddress, :newRole)";
    $addUserStatement = $pdo->prepare($addUserSql);

    $userData = [
        'newUsername' => $newUsername,
        'hashedPassword' => $hashedPassword,
        'newEmail' => $newEmail,
        'newAge' => $newAge,
        'newGender' => $newGender,
        'newMobileNumber' => $newMobileNumber,
        'newAddress' => $newAddress,
        'newRole' => $newRole
    ];

    if ($addUserStatement->execute($userData)) {
        echo "User added successfully.";
    } else {
        echo "Failed to add user. Error: " . implode(" ", $addUserStatement->errorInfo());
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
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <style>
        /* Add your styles here */
    </style>
    <title>Add User</title>
</head>
<body>
    <div class="container">
        <h2>Add User</h2>
        <!-- User Addition Form -->
        <form method="post" action="add_user.php">
            <label for="new_username">Username:</label>
            <input type="text" id="new_username" name="new_username" required>
            <br>

            <label for="new_password">Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <br>

            <label for="new_email">Email:</label>
            <input type="email" id="new_email" name="new_email" required>
            <br>

            <label for="new_age">Age:</label>
            <input type="text" id="new_age" name="new_age" required>
            <br>

            <label for="new_gender">Gender:</label>
            <select id="new_gender" name="new_gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <br>

            <label for="new_mobilenumber">Mobile Number:</label>
            <input type="text" id="new_mobilenumber" name="new_mobilenumber" required>
            <br>

            <label for="new_address">Address:</label>
            <input type="text" id="new_address" name="new_address" required>
            <br>

            <label for="new_role">Role:</label>
            <select id="new_role" name="new_role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <br>

            <button type="submit">Add User</button>
        </form>
        <br>
        <a href="admin.php">Back to Admin Dashboard</a>
    </div>
</body>
</html> 
