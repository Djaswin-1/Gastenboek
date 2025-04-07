<?php
$servername = "localhost";
$username = "root"; // Standaard XAMPP-gebruiker
$password = ""; // Standaard leeg in XAMPP
$dbname = "gastenboek"; // Jouw database naam

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}
?>
