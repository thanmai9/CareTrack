<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Check if POST variables are set
    if (isset($_POST['doctorID']) && isset($_POST['email']) && isset($_POST['password'])) {
        $doctorID = $_POST['doctorID'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Establish MySQL connection
        $conn = new mysqli('localhost', 'root', '', 'doctor');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if doctorID already exists
        $stmt = $conn->prepare("SELECT doctorID FROM doctors WHERE doctorID = ?");
        $stmt->bind_param("s", $doctorID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Doctor ID already exists. Please choose a different Doctor ID.";
        } else {
            // Insert new doctor record
            $stmt = $conn->prepare("INSERT INTO doctors (doctorID, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $doctorID, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Redirect to login page
                header("Location: doctor-login.html");
                exit(); // Ensure the script stops execution after redirect
            } else {
                echo "Error: " . $stmt->error;
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        die("Required fields are missing.");
    }
} else {
    die("Invalid request method.");
}
?>
