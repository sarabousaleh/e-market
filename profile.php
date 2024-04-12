<?php
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Handle logout
if (isset($_POST['logout'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "emarket";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate password change
    // You can add more validation logic as needed

    // Update password in the database
    $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updatePasswordSQL = "UPDATE users SET Password = '$hashedNewPassword' WHERE UserID = '$user_id'";
    $conn->query($updatePasswordSQL);

    // Redirect or display a success message as needed
}

// Handle profile changes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])) {
    $email = $_POST['email'];
    $mobileNumber = $_POST['mobile_number'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    // Update profile in the database
    $updateProfileSQL = "UPDATE users SET Email = '$email', MobileNumber = '$mobileNumber', Address = '$address', Age = '$age', Gender = '$gender' WHERE UserID = '$user_id'";
    $conn->query($updateProfileSQL);

    // Redirect or display a success message as needed
}

// Fetch updated user data
$sql = "SELECT * FROM users WHERE UserID = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Handle the case where user information is not found
    echo "User not found";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <title>e-market</title>
    <link rel="stylesheet" type="text/css" href="css/mystyle.css" />
    <style>
        /* mystyle.css */

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #6a217c;
        }

        .result-box {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input {
            width: 100%;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
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

        .result-box button {
            margin: 10px;
        }

        button:hover {
            background-color: #4a1664;
        }

        /* Responsive styles for smaller screens */
        @media screen and (max-width: 600px) {
            .container {
                width: 90%;
                margin: 10px auto;
            }
        }
    </style>
</head>

<body>

    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Profile</h2>

        <div class="result-box">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $user['Username']; ?>" readonly>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo isset($user['Email']) ? $user['Email'] : ''; ?>">

        <label for="mobile_number">Mobile Number:</label>
        <input type="text" id="mobile_number" name="mobile_number" value="<?php echo isset($user['MobileNumber']) ? $user['MobileNumber'] : ''; ?>">

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo isset($user['Address']) ? $user['Address'] : ''; ?>">

        <label for="age">Age:</label>
        <input type="text" id="age" name="age" value="<?php echo isset($user['Age']) ? $user['Age'] : ''; ?>">

        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
            <option value="Male" <?php echo (isset($user['Gender']) && $user['Gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo (isset($user['Gender']) && $user['Gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
        </select>

        <button type="submit" name="save_changes" class="result-box">Save Changes</button>
    </form>
</div>


        <!-- Change Password Form -->
        <div class="result-box">
            <h3>Change Password</h3>
            <form onsubmit="return validatePassword()" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" name="change_password" class="result-box">Change Password</button>
            </form

>
        </div>

        <!-- Logout Button -->
        <div class="result-box">
            <form method="post">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>
    </div>
<!-- ... your HTML and PHP code ... -->

<script>
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])): ?>
        <?php var_dump($_POST); // Print the $_POST array for debugging purposes ?>
    <?php endif; ?>

    function validatePassword() {
        var newPassword = document.getElementById("new_password").value;
        var confirmPassword = document.getElementById("confirm_password").value;

        if (newPassword !== confirmPassword) {
            alert("Passwords do not match. Please re-enter.");
            return false;
        }

        return true;
    }

    function validateNumericInput(inputElement) {
        var inputValue = inputElement.value.trim();

        // Allow only numeric values
        var numericRegex = /^[0-9]+$/;

        if (!numericRegex.test(inputValue)) {
            alert("Please enter only numeric values.");
            inputElement.value = ''; // Clear the input field
            return false;
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('age').addEventListener('input', function () {
            validateNumericInput(this);
        });

        document.getElementById('mobile_number').addEventListener('input', function () {
            validateNumericInput(this);
        });
    });
</script>

</body>

</html>