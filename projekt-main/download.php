<?php
require_once 'config.php';

$file_found = false;
$upload = null;
$password_required = false;
$password_correct = false;
$file_expired = false;
$error = '';

if (isset($_GET['hash']) && isset($_GET['id'])) {
    $hash = $_GET['hash'];
    $id = $_GET['id'];

    // Prepare SQL statement
    $sql = "SELECT * FROM upload_links WHERE fk_upload_id = ? AND upload_hash = ?";
    $stmt = $pdo->prepare($sql);

    // Execute SQL statement
    $stmt->execute([$id, $hash]);

    // Fetch the record
    $record = $stmt->fetch();

    if ($record) {
        // Fetch the associated upload record
        $sql = "SELECT * FROM uploads WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $upload = $stmt->fetch();
        if ($upload) {
            $file_found = true;

            // check file expiry date
            $expiry_dates = json_decode(file_get_contents('expiry-dates.json'), true);
            if (isset($expiry_dates[$id]) && strtotime($expiry_dates[$id]) < time()) {
                $file_expired = true;
            }

            $passwords = json_decode(file_get_contents('passwords.json'), true);
            if (isset($passwords[$id])) {
                $password_required = true;
                if (isset($_POST['password'])) {
                    if ($_POST['password'] === $passwords[$id]) {
                        $password_correct = true;
                    } else {
                        $error = "Incorrect password.";
                    }
                }
            }
        }
    }
}

if ($file_found && !$file_expired && (!$password_required || $password_correct)) {
    // Fetch the file from the file system
    $file_path = 'uploads/' . $upload['id'] . '.' . $upload['file_extension'];

    // Force the browser to download the file
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'. $upload['file_name'] .'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    flush(); // Flush system output buffer
    readfile($file_path);
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Download | WeShare</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <?php if (!$file_found) { ?>
            <h1>File Not Found</h1>
            <p>The file you are looking for could not be found.</p>
        <?php } elseif ($file_expired) { ?>
            <h1>File Expired</h1>
            <p>The file you are trying to access has expired.</p>
        <?php } elseif ($password_required && !$password_correct) { ?>
            <h1>Password Required</h1>
            <?php if (isset($_POST['password'])) { ?>
                <div class="error">Wrong password entered.</div>
            <?php } ?>
            <form action="" method="POST">
                <label for="password">Enter Password:</label>
                <input type="password" name="password" id="password" required>
                <input type="submit" value="Submit" class="button">
            </form>
        <?php } ?>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
