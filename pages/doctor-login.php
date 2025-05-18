<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if POST variables are set
    if (isset($_POST['doctorID']) && isset($_POST['password'])) {
        $doctorID = $_POST['doctorID'];
        $password = $_POST['password'];

        // Establish MySQL connection
        $conn = new mysqli('localhost', 'root', '', 'doctor');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute SQL statement to retrieve the hashed password from the doctors table
        $stmt = $conn->prepare("SELECT password FROM doctors WHERE doctorID = ?");
        $stmt->bind_param("s", $doctorID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Bind result variables
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            // Verify password
            if (password_verify($password, $hashedPassword)) {
                echo "Login successful";
                header("location: drrenuka.html");
                // You may set session variables or perform other actions here
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "Doctor ID not found.";
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