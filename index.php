<?php
session_start();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> <!-- Verwijzing naar CSS-bestand -->
    <title>Home - Welkom bij de Gastenboek</title>
</head>
<body>


    <div class="container">
        <h1>Welkom bij de Gastenboek</h1>

        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Welkom, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Je bent ingelogd.</p>
            <a href="post.php" class="btn">Bekijk Berichten</a>
            <a href="post_plaatsen.php" class="btn">Plaats een Bericht</a> <!-- Alleen zichtbaar als je bent ingelogd -->
            <a href="logout.php" class="btn">Uitloggen</a>
        <?php else: ?>
            <p>Je bent niet ingelogd.</p>
            <p>Log in of registreer een account om een bericht te plaatsen.</p>
            <a href="login.php" class="btn">Inloggen</a>
            <a href="register.php" class="btn">Registreren</a>
        <?php endif; ?>
    </div>

</body>
</html>
