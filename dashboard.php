<?php
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['gebruikersnaam'])) {
    // Niet ingelogd? Stuur door naar de inlogpagina
    header("Location: inlog.php");
    exit();
}

$gebruikersnaam = $_SESSION['gebruikersnaam'];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Voeg je CSS toe -->
</head>
<body>

    <div class="container">
        <h2>Welkom, <?php echo htmlspecialchars($gebruikersnaam); ?>!</h2>
        <p>Je bent succesvol ingelogd.</p>
        <a href="uitloggen.php" class="btn">Uitloggen</a>
    </div>

</body>
</html>