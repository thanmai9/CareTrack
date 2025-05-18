<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure the path is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Establish MySQL connection
        $conn = new mysqli('localhost', 'root', '', 'doctor'); // Update as needed

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT doctorID FROM doctors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $token = bin2hex(random_bytes(16));
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $email, $token);
            $stmt->execute();

            $resetLink = "https://777f-202-83-28-183.ngrok-free.app/reset-password.php?token=" . $token;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'harimigu11@gmail.com';
                $mail->Password = 'tobk qdul ksdi aial';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('harimigu11@gmail.com', 'CareTakerCT');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = 'To reset your password, please click the link below:<br><a href="' . $resetLink . '">' . $resetLink . '</a>';
                $mail->AltBody = 'To reset your password, please click the link below:\n' . $resetLink;

                $mail->send();
                echo 'A password reset link has been sent to your email.';
            } catch (Exception $e) {
                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            }
        } else {
            echo 'No account found with that email address.';
        }

        $stmt->close();
        $conn->close();
    } else {
        die('Required fields are missing.');
    }
} else {
    die('Invalid request method.');
}
?>
