<?php
session_start(); // Start the session

if (!isset($_SESSION['doctorID'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$doctorID = $_SESSION['doctorID'];

// Establish MySQL connection for doctors database
$connDoctors = new mysqli('localhost', 'root', '', 'doctors');

// Check connection
if ($connDoctors->connect_error) {
    die("Connection to doctors database failed: " . $connDoctors->connect_error);
}

// Fetch doctor details
$stmt = $connDoctors->prepare("SELECT doctorID FROM doctors WHERE doctorID = ?");
if (!$stmt) {
    die("Prepare failed: " . $connDoctors->error);
}

$stmt->bind_param("s", $doctorID);
$stmt->execute();
$stmt->bind_result($doctorIDResult); // Adjusted to fetch doctorID
$stmt->fetch();
$stmt->close();

// Close connection to doctors database
$connDoctors->close();

// Establish MySQL connection for patients database
$connPatients = new mysqli('localhost', 'root', '', 'patients');

// Check connection
if ($connPatients->connect_error) {
    die("Connection to patients database failed: " . $connPatients->connect_error);
}

// Fetch patient details
$patientQuery = "SELECT patientID, name, diagnosis, age FROM patients";
$patientsResult = $connPatients->query($patientQuery);

if (!$patientsResult) {
    die("Query failed: " . $connPatients->error);
}

// Close connection to patients database
$connPatients->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, Doctor <?php echo htmlspecialchars($doctorIDResult); ?></h1>
    <h2>Patient Information</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Diagnosis</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($patient = $patientsResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($patient['patientID']); ?></td>
                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                <td><?php echo htmlspecialchars($patient['diagnosis']); ?></td>
                <td><?php echo htmlspecialchars($patient['age']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
