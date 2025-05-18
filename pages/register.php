<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if POST variables are set
    if (isset($_POST['doctorID']) && isset($_POST['password'])) {
        $doctorID = $_POST['doctorID'];
        $password = $_POST['password'];

        // Hash the password before storing it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Establish MySQL connection
        $conn = new mysqli('localhost', 'root', '', 'doctor');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            // Check if doctorID already exists
            $stmt = $conn->prepare("SELECT doctorID FROM doctor_login WHERE doctorID = ?");
            $stmt->bind_param("s", $doctorID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "Doctor ID already exists. Please choose a different ID.";
            } else {
                // Insert new doctor record
                $stmt = $conn->prepare("INSERT INTO doctor_login (doctorID, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $doctorID, $hashedPassword);
                if ($stmt->execute()) {
                    echo "Login successful";
                } else {
                    echo "Error: " . $stmt->error;
                }
            }

            $stmt->close();
            $conn->close();
        }
    } else {
        die("Required fields are missing.");
    }
} else {
    die("Invalid request method.");
}
?>
