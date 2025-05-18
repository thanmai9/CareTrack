<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if POST variables are set
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Establish MySQL connection
        $conn = new mysqli('localhost', 'root', '', 'users');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and execute SQL statement to retrieve the hashed password from the users table
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Bind result variables
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashedPassword)) {
                // Redirect to the desired page
                header("Location: thanmai.html");
                exit(); // Make sure no further code is executed
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "Username not found.";
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
