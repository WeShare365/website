<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>WeShare</title>
</head>
<body>
    <?php
        session_start();
        if(isset($_SESSION['user_id'])) {
            header('Location: home.php');
            exit();
        }
    ?>
    <?php include 'header.php'; ?>
    <main>
        <h1>Welcome to WeShare</h1>
        <p>Upload and share your files with ease.</p>
        <a href="login.php" class="button" style="cursor: pointer;">Start Uploading</a>
    </main>
        <section id="faq">
            <h2>Frequently Asked Questions</h2>
        <section id="faq">
        <div class="faq-item">
            <div class="faq-question">
                <span>Wie kann ich eine Datei hochladen?</span>
                <img src="dropdown.svg" alt="Dropdown Icon" class="dropdown-icon">
            </div>
            <div class="faq-answer">
                Um eine Datei hochzuladen, klicken Sie einfach auf die Schaltfläche "Upload" und wählen Sie die gewünschte Datei von Ihrem Computer aus.
            </div>
        </div> <br>
        <div class="faq-item">
            <div class="faq-question">
                <span>Kann ich mehrere Dateien gleichzeitig hochladen?</span>
                <img src="dropdown.svg" alt="Dropdown Icon" class="dropdown-icon">
            </div>
            <div class="faq-answer">
                Nein, momentan ist dies nicht möglich.
            </div>
        </div> <br>
        <div class="faq-item">
            <div class="faq-question">
                <span>Wie kann ich meine hochgeladenen Dateien teilen?</span>
                <img src="dropdown.svg" alt="Dropdown Icon" class="dropdown-icon">
            </div>
            <div class="faq-answer">
                Nachdem Sie eine Datei hochgeladen haben, erhalten Sie einen eindeutigen Link, den Sie mit anderen teilen können, um Ihre Datei zugänglich zu machen.
            </div>
        </div> <br>
        <div class="faq-item">
        <div class="faq-question">
            <span>Ist mein Passwort sicher?</span>
            <img src="dropdown.svg" alt="Dropdown Icon" class="dropdown-icon">
        </div>
        <div class="faq-answer">
            Ja, Ihr Passwort ist sicher. Wir verwenden Hashing, um Ihr Passwort zu schützen. Denken Sie dennoch daran, <strong>immer</strong> ein starkes Passwort zu verwenden.
        </div>
    </div>

        </div>
    </section>

        <!-- More FAQ items... -->
    </section>
    <?php include 'footer.php'; ?>
    <script src="index.js"></script>
</body>
</html>
