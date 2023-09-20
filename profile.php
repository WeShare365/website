<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// include database connection
include 'config.php';

// Get user details
$stmt = $pdo->prepare("SELECT fullname, username, profilepic FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Check if user exists
if (!$user) {
    echo 'User not found.';
    exit();
}

// Set session variables for fullname and username
$_SESSION['fullname'] = $user['fullname'];
$_SESSION['username'] = $user['username'];

// Change password
if (isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    // Password hash
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$password_hash, $_SESSION['user_id']]);
    echo 'Password changed successfully.';
}

// Change profile picture
if (isset($_POST['change_picture'])) {
    $profilepic = $_FILES['profilepic'];

    // Check if the uploaded file is an image
    $image_type = exif_imagetype($profilepic["tmp_name"]);
    if ($image_type === false) {
        echo 'Invalid image file.';
        exit();
    }

    // Create an image resource based on the uploaded file
    $image = null;
    if ($image_type == IMAGETYPE_JPEG) {
        $image = imagecreatefromjpeg($profilepic["tmp_name"]);
    } elseif ($image_type == IMAGETYPE_PNG) {
        $image = imagecreatefrompng($profilepic["tmp_name"]);
    } elseif ($image_type == IMAGETYPE_GIF) {
        $image = imagecreatefromgif($profilepic["tmp_name"]);
    }

    if (!$image) {
        echo 'Failed to create image resource.';
        exit();
    }

    // Set the desired size for the scaled image
    $max_size = 1600;

    // Get the original width and height of the image
    $original_width = imagesx($image);
    $original_height = imagesy($image);

    // Calculate the aspect ratio of the original image
    $aspect_ratio = $original_width / $original_height;

    // Calculate the dimensions for scaling the image
    $new_width = $max_size;
    $new_height = $max_size;

    // Scale the image to fit within the maximum size while maintaining the aspect ratio
    if ($original_width > $original_height) {
        $new_height = round($new_width / $aspect_ratio);
    } else {
        $new_width = round($new_height * $aspect_ratio);
    }

    // Create a new image with the scaled dimensions
    $scaled_image = imagescale($image, $new_width, $new_height);

    // Calculate the crop position to make the image square
    $crop_x = ($new_width > $new_height) ? round(($new_width - $new_height) / 2) : 0;
    $crop_y = ($new_height > $new_width) ? round(($new_height - $new_width) / 2) : 0;
    $crop_size = min($new_width, $new_height);

    // Crop the image to make it square
    $cropped_image = imagecrop($scaled_image, ['x' => $crop_x, 'y' => $crop_y, 'width' => $crop_size, 'height' => $crop_size]);

    // Save the cropped image to a file
    $target_dir = "profile-pictures/";
    $target_file = $target_dir . basename($profilepic["name"]);
    imagepng($cropped_image, $target_file);
    imagedestroy($scaled_image);
    imagedestroy($cropped_image);

    // Update the profile picture in the database
    $stmt = $pdo->prepare("UPDATE users SET profilepic = ? WHERE id = ?");
    $stmt->execute([$target_file, $_SESSION['user_id']]);
    echo 'Profile picture changed successfully.';

    // Update the profile picture in the user variable
    $user['profilepic'] = $target_file;
}

// Delete account
if (isset($_POST['delete_account'])) {
    // Get all uploads of the user
    $stmt = $pdo->prepare("SELECT id FROM uploads WHERE fk_user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $uploads = $stmt->fetchAll();

    // For each upload, delete the associated links
    foreach ($uploads as $upload) {
        $stmt = $pdo->prepare("DELETE FROM upload_links WHERE fk_upload_id = ?");
        $stmt->execute([$upload['id']]);
    }

    // Delete the uploads
    $stmt = $pdo->prepare("DELETE FROM uploads WHERE fk_user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    session_destroy();
    header('Location: login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | WeShare</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <style>
        .btn-green {
            background-color: #41D272;
            color: white;
        }
        .btn-red {
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Profile</h1>
        <div class="profile-details">
            <p>Full Name: <?php echo $_SESSION['fullname']; ?></p>
            <p>Username: <?php echo $_SESSION['username']; ?></p>
        </div>
        
        <?php if ($user['profilepic']): ?>
        <div class="profile-picture">
            <img src="<?php echo $user['profilepic']; ?>" style="width:256px; height:256px; border-radius: 50%;" alt="Profile Picture">
        </div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>
            <input type="submit" name="change_password" value="Change Password" class="btn-green">
        </form>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="profilepic">Change Profile Picture:</label>
            <input type="file" name="profilepic" id="profilepic" required>
            <input type="submit" name="change_picture" value="Change Picture" class="btn-green">
        </form>
        <form id="delete-account-form" action="" method="POST">
            <input type="submit" name="delete_account" value="Delete Account" class="btn-red">
        </form>
    </main>
    <?php include 'footer.php'; ?>
    <script>
    $(function() {
        $("#delete-account-form").submit(function(e) {
            var form = this;
            e.preventDefault();
            $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Delete account": function() {
                        $(this).dialog("close");
                        form.submit(); 
                    },
                    Cancel: function() {
                        $(this).dialog("close");
                    }
                }
            });
        });
    });
</script>

    <div id="dialog-confirm" title="Delete Account?" style="display:none;">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Are you sure you want to delete your account?</p>
    </div>
</body>
</html>
