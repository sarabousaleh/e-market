<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "emarket";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $plainPassword = $_POST['password'];
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $mobileNumber = $_POST['mobileNumber'];
    $address = $_POST['address'];

    // Check if the username already exists
    $checkUsernameSql = "SELECT * FROM users WHERE Username = ?";
    $checkUsernameStatement = $conn->prepare($checkUsernameSql);
    $checkUsernameStatement->bind_param("s", $username);
    $checkUsernameStatement->execute();
    $checkUsernameResult = $checkUsernameStatement->get_result();

    if ($checkUsernameResult->num_rows > 0) {
        echo "Username already exists. Please choose another username.";
    } else {
        // Use prepared statement to prevent SQL injection
        $insertUserSql = "INSERT INTO users (Username, Password, Email, Age, Gender, mobilenumber, address) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertUserStatement = $conn->prepare($insertUserSql);

        $insertUserStatement->bind_param("sssisss", $username, $hashedPassword, $email, $age, $gender, $mobileNumber, $address);

        if ($insertUserStatement->execute()) {
            $insertUserStatement->close();
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $insertUserStatement->error;
        }
    }
}

$conn->close();
?>

<!-- The rest of your HTML code remains unchanged -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        h1 {
            color: #6a217c;
            margin-bottom: 20px;
            text-align: center; /* Center the h1 */
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
        }

        input,
        select,
        textarea {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button {
            padding: 10px;
            background-color: #6a217c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4a1850;
        }

        .login-link {
            text-align: center;
            margin-top: 10px;
        }

        .login-link a {
            color: #6a217c;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
    
</head>
<body>
    <div class="container">
        <h1>e-market</h1>
        <form action="signup.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="age">Age:</label>
            <input type="number" name="age" required>

            <label for="gender">Gender:</label>
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <label for="mobileNumber">Mobile Number:</label>
            <input type="tel" name="mobileNumber" required>

            <label for="address">Address:</label>
            <textarea name="address" rows="4" required></textarea>

            <button type="submit">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>