<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $profilepic = 'Default_ProfilePic.png'; //set the default profile picture

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        echo "This username is already taken. Please try a different one.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // hashing the password

        $sql = "INSERT INTO users (fullname, username, password, profilepic) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fullname, $username, $hashedPassword, $profilepic]);

        if ($stmt->rowCount() == 1) {
            header('Location: login.php'); // redirect to login page after successful registration
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Signup | WeShare</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Signup</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" name="fullname" id="fullname" required>
            <br>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <br>
            <input type="submit" value="Signup" class="button">
            <div class="footer">
                Already have an account? <a href="login.php">Login Here</a>
            </div>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
