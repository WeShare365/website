<header>
    <a href="http://localhost/WeShare/mittwoch-projekt/index.php">
        <img src="Logo_WeShare.svg" alt="WeShare Logo" id="logo">
    </a>
    <nav>
        <ul style="display: flex; align-items: center;">
            <?php if (isset($_SESSION['username'])) { ?>
                <!-- Show these links only when the user is logged in -->
                <li style="margin-left: auto;">
                    <a href="logout.php">Logout</a>
                </li>
                <li style="float: right;">
                    <a href="profile.php">
                        <?php
                            // include database connection
                            require 'config.php';
                            // Get user profile picture
                            $stmt = $pdo->prepare("SELECT profilepic FROM users WHERE username = ?");
                            $stmt->execute([$_SESSION['username']]);
                            $user = $stmt->fetch();
                        ?>
                        <img src="<?php echo $user['profilepic']; ?>" style="border: 1px solid white; padding: 2px; border-radius: 50%;" width="60" height="60" alt="Profile Picture">                    
                    </a>
                </li>
            <?php } else { ?>
                <!-- Show these links only when the user is not logged in -->
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Signup</a></li>
            <?php } ?>
        </ul>
    </nav>
</header>
