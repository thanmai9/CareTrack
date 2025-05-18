<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure the path is correct

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        $conn = new mysqli('localhost', 'root', '', 'users');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        if ($email) {
            echo '<form action="usr-pass-reset.php" method="post">
                    <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" required>
                    <button type="submit">Reset Password</button>
                  </form>';
        } else {
            echo 'Invalid or expired token.';
        }

        $conn->close();
    } else {
        die('Token is missing.');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && isset($_POST['password'])) {
        $token = $_POST['token'];
        $password = $_POST['password'];

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $conn = new mysqli('localhost', 'root', '', 'users');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();

        if ($email) {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashedPassword, $email);
            $stmt->execute();

            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            echo 'Password has been reset successfully.';
        } else {
            echo 'Invalid or expired token.';
        }

        $conn->close();
    } else {
        die('Required fields are missing.');
    }
} else {
    die('Invalid request method.');
}
?>
