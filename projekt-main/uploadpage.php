<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $user_id = $_SESSION['user_id'];
    $file_name = $_FILES['file']['name'];
    $file_temp_name = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_size = $_FILES['file']['size'];
    $file_error = $_FILES['file']['error'];
    $password_protected = isset($_POST['password_protected']);
    $password = $password_protected ? $_POST['password'] : "";
    $expiry_date_set = isset($_POST['expiry_date_set']);
    $expiry_date = $expiry_date_set ? $_POST['expiry_date'] : "";

    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_exts = array("jpg", "jpeg", "png", "gif", "txt", "pdf");

    if (in_array($file_ext, $allowed_exts)) {
        if ($file_error === 0) {
            if ($file_size <= 5242880) {
                $file_name_with_ext = $name . '.' . $file_ext;
                $sql = "INSERT INTO uploads (fk_user_id, file_name, file_extension) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id, $file_name_with_ext, $file_ext]);
                $last_insert_id = $pdo->lastInsertId();

                $upload_hash = bin2hex(random_bytes(20));
                $sql = "INSERT INTO upload_links (fk_upload_id, upload_hash) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$last_insert_id, $upload_hash]);

                $new_file_name = $last_insert_id . "." . $file_ext;
                $upload_dir = "uploads/" . $new_file_name;
                move_uploaded_file($file_temp_name, $upload_dir);

                if ($password_protected) {
                    $passwords[$last_insert_id] = $password;
                    file_put_contents('passwords.json', json_encode($passwords));
                }
                
                if ($expiry_date_set) {
                    $expiry_dates = json_decode(file_get_contents('expiry-dates.json'), true);
                    $expiry_dates[$last_insert_id] = $expiry_date;
                    file_put_contents('expiry-dates.json', json_encode($expiry_dates));
                }

                $success = true;
                $downloadLink = "http://localhost/weshare/mittwoch-projekt/download.php?hash=$upload_hash&id=$last_insert_id";
            } else {
                $error = "File size exceeds maximum allowed limit of 5MB.";
            }
        } else {
            $error = "An error occurred while uploading the file.";
        }
    } else {
        $error = "Only files with the following extensions are allowed: " . implode(", ", $allowed_exts) . ".";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <title>Upload Files | WeShare</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Upload Files</h1>
        <?php if (isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="name">File Name:</label>
            <input type="text" name="name" id="name" required>
            <br>
            <label for="file">Select File:</label>
            <input type="file" name="file" id="file" required>
            <br>
            <label for="password_protected">Password Protect:</label>
            <input type="checkbox" name="password_protected" id="password_protected">
            <br>
            <label for="password" style="display: none;">Password:</label>
            <input type="password" name="password" id="password" style="display: none;">
            <br>
            <label for="expiry_date_set">Set Expiry Date:</label>
            <input type="checkbox" name="expiry_date_set" id="expiry_date_set">
            <br>
            <label for="expiry_date" style="display: none;">Expiry Date:</label>
            <input type="date" name="expiry_date" id="expiry_date" style="display: none;">
            <br>
            <input type="submit" value="Upload" class="button">
        </form>
    </main>
    <div id="uploadSuccessDialog" title="Upload Successful" style="display: none;">
        <p>Your file is ready to download:</p>
        <input id="downloadLink" readonly>
        <button id="copyLinkButton">Copy link</button>
    </div>
    <script>
        $(function() {
            // Show/hide password field based on checkbox
            $('#password_protected').change(function() {
                if ($(this).is(':checked')) {
                    $('label[for="password"]').show();
                    $('#password').show();
                } else {
                    $('label[for="password"]').hide();
                    $('#password').hide();
                }
            });

            // Show/hide expiry date field based on checkbox
            $('#expiry_date_set').change(function() {
                if ($(this).is(':checked')) {
                    $('label[for="expiry_date"]').show();
                    $('#expiry_date').show();
                } else {
                    $('label[for="expiry_date"]').hide();
                    $('#expiry_date').hide();
                }
            });

            <?php if (isset($success)) { ?>
            // Fill in download link
            $('#downloadLink').val('<?php echo $downloadLink; ?>');

            // Open dialog
            $("#uploadSuccessDialog").dialog({
                modal: true,
                buttons: {
                    Ok: function() {
                        $(this).dialog("close");
                    }
                }
            });

            // Copy link button functionality
            $('#copyLinkButton').click(function() {
                $('#downloadLink').select();
                document.execCommand('copy');
            });
            <?php } ?>
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>
