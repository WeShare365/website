<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home | WeShare</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Welcome <?php echo $_SESSION['username']; ?></h1>
        <a href="uploadpage.php" class="button">Upload a File</a>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
