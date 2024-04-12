<!-- update_user.php -->
<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Not an admin or not logged in.";
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

// Check if the user ID is provided in the query parameter
if (isset($_GET['user_id_to_update'])) {
    $userIdToUpdate = $_GET['user_id_to_update'];

    // Fetch user information from the database and display the update form
    $userSql = "SELECT * FROM users WHERE UserID = :userIdToUpdate";
    $userStatement = $pdo->prepare($userSql);

    if ($userStatement->execute(['userIdToUpdate' => $userIdToUpdate])) {
        $userData = $userStatement->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Failed to fetch user details. Error: " . implode(" ", $userStatement->errorInfo());
    }

    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update user in the database
        $updatedUsername = $_POST['updated_username'];
        $updatedPassword = password_hash($_POST['updated_password'], PASSWORD_DEFAULT);
        $updatedEmail = $_POST['updated_email'];
        $updatedAge = $_POST['updated_age'];
        $updatedMobileNumber = $_POST['updated_mobile_number'];
        $updatedAddress = $_POST['updated_address'];
        $updatedGender = $_POST['updated_gender'];
        $updatedRole = $_POST['updated_role'];

        $updateUserSql = "UPDATE users SET 
                           Username = :updatedUsername, 
                           Password = :updatedPassword, 
                           Email = :updatedEmail, 
                           Age = :updatedAge, 
                           mobilenumber = :updatedMobileNumber, 
                           address = :updatedAddress, 
                           Gender = :updatedGender, 
                           Role = :updatedRole 
                           WHERE UserID = :userIdToUpdate";

        $updateUserStatement = $pdo->prepare($updateUserSql);
        $updateUserStatement->bindParam(':updatedUsername', $updatedUsername);
        $updateUserStatement->bindParam(':updatedPassword', $updatedPassword);
        $updateUserStatement->bindParam(':updatedEmail', $updatedEmail);
        $updateUserStatement->bindParam(':updatedAge', $updatedAge);
        $updateUserStatement->bindParam(':updatedMobileNumber', $updatedMobileNumber);
        $updateUserStatement->bindParam(':updatedAddress', $updatedAddress);
        $updateUserStatement->bindParam(':updatedGender', $updatedGender);
        $updateUserStatement->bindParam(':updatedRole', $updatedRole);
        $updateUserStatement->bindParam(':userIdToUpdate', $userIdToUpdate);

        if ($updateUserStatement->execute()) {
            echo "User updated successfully.";
        } else {
            echo "Failed to update user. Error: " . implode(" ", $updateUserStatement->errorInfo());
        }
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
    <title>Update User</title>
</head>
<body>
    <div class="container">
        <h2>Update User</h2>
        <form method="post" action="update_user.php?user_id_to_update=<?php echo $userIdToUpdate; ?>">
            <!-- Add input fields for updating user attributes -->
            <label for="updated_username">Username:</label>
            <input type="text" name="updated_username" value="<?php echo $userData['Username']; ?>" required>

            <label for="updated_password">Password:</label>
            <input type="password" name="updated_password" required>

            <label for="updated_email">Email:</label>
            <input type="email" name="updated_email" value="<?php echo $userData['Email']; ?>" required>

            <label for="updated_age">Age:</label>
            <input type="number" name="updated_age" value="<?php echo $userData['Age']; ?>" required>

            <label for="updated_mobile_number">Mobile Number:</label>
            <input type="text" name="updated_mobile_number" value="<?php echo $userData['mobilenumber']; ?>" required>

            <label for="updated_address">Address:</label>
            <textarea name="updated_address" required><?php echo $userData['address']; ?></textarea>

            <label for="updated_gender">Gender:</label>
            <select name="updated_gender" required>
                <option value="Male" <?php echo ($userData['Gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($userData['Gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>

            <label for="updated_role">Role:</label>
            <select name="updated_role" required>
                <option value="user" <?php echo ($userData['Role'] === 'user') ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo ($userData['Role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>

            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>
