<?php
include 'database/db_connect.php'; // Databaseverbinding toevoegen

// Controleer of het formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Controleer of wachtwoorden overeenkomen
    if ($password !== $confirm_password) {
        $error = "Wachtwoorden komen niet overeen!";
    } else {
        // Controleer of gebruikersnaam of e-mail al bestaat
        $check_sql = "SELECT * FROM gebruikers WHERE gebruikersnaam = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Gebruikersnaam of e-mailadres is al in gebruik!";
        } else {
            // Hash het wachtwoord
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // SQL-query om gebruiker op te slaan
            $sql = "INSERT INTO gebruikers (gebruikersnaam, email, wachtwoord) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                header("Location: login.php"); // Doorsturen naar loginpagina
                exit;
            } else {
                $error = "Fout bij registratie. Probeer opnieuw.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <link rel="stylesheet" href="style.css"> <!-- Verwijzing naar CSS-bestand -->
</head>
<body>

    <div class="container">
        <h2>Registratie</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        
        <form action="register.php" method="POST">
            <label for="username">Gebruikersnaam</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Wachtwoord</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Wachtwoord bevestigen</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="btn">Registreren</button>

            <!-- Inloggen-knop onder de Registreren-knop -->
            <a href="login.php" class="btn">Heb je al een account? Klik hier om in te loggen</a>
        </form>
    </div>

</body>
</html>
