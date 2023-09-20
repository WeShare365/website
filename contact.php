<?php
session_start();

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Generate a unique ID for each message
    $id = uniqid();

    // Create an array to hold the message data
    $data = array(
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'message' => $message
    );

    // Read the existing data from contact.json
    $jsonData = file_get_contents('contact.json');
    $messages = [];

    // If the file is not empty, decode the existing JSON data
    if (!empty($jsonData)) {
        $messages = json_decode($jsonData, true);
    }

    // Add the new message to the messages array
    $messages[] = $data;

    // Encode the messages array as JSON
    $jsonString = json_encode($messages, JSON_PRETTY_PRINT);

    // Write the JSON string to the contact.json file
    if (file_put_contents('contact.json', $jsonString) !== false) {
        $successMessage = "Form submitted successfully.";
    } else {
        $errorMessage = "Error occurred while saving the form data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>
    <main>
        <h1>Contact Us</h1>
        <p>If you have any questions or concerns, please fill out the form below, and we'll get back to you as soon as possible.</p>

        <?php if ($successMessage): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form action="#" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Submit</button> <br><br>
        </form>
        <div style="width: 100%; height: 600px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d10847.60750896936!2d8.495156987158209!3d47.17935770000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x479aaa594655ee67%3A0x5ae173f0d98b982f!2sSiemens%20Schweiz%20AG!5e0!3m2!1sde!2sch!4v1684331052045!5m2!1sde!2sch" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </main>

<?php include 'footer.php'; ?>
</body>
</html>
