<?php
session_start(); // Start een sessie om gegevens op te slaan na inloggen
include '../database/db_connect.php'; // De databaseverbinding

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // SQL-query om de gebruiker op te halen
    $sql = "SELECT * FROM gebruikers WHERE gebruikersnaam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Als gebruiker bestaat, controleer wachtwoord
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['wachtwoord'])) {
            // Wachtwoord komt overeen, sessie starten
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['gebruikersnaam'];
            header("Location: ../posts/post_plaatsen.php"); // Redirect naar een welkompagina
            exit;
        } else {
            $error = "Ongeldig wachtwoord.";
        }
    } else {
        $error = "Gebruikersnaam niet gevonden.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css"> <!-- Verwijzing naar CSS-bestand -->
    <title>Inloggen</title>
</head>
<body>

    <div class="container">
        <h2>Inloggen</h2>
        <?php if (!empty($error)) echo "<p class='message' style='color:red;'>$error</p>"; ?>
        
        <form action="login.php" method="POST">
            <label for="username">Gebruikersnaam</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Wachtwoord</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Inloggen</button>
        </form>

        <!-- Extra knop voor registratie -->
        <p>Nog geen account? <a href="register.php" class="register-link">Klik hier om te registreren</a></p>
    </div>

</body>
</html>
