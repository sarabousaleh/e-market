<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userIdToDelete = $_POST['delete_user'];

    // Delete user from the database
    $pdo = new PDO('mysql:host=localhost;dbname=emarket', 'root', '');

    $stmt = $pdo->prepare("DELETE FROM users WHERE UserID = ?");
    $stmt->execute([$userIdToDelete]);

    // Redirect to the admin page after deleting the user
    header("Location: admin.php");
    exit();
}
?>
