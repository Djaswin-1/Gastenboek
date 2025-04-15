<?php
session_start();
include '../database/db_connect.php'; // Databaseverbinding

// Haal alle berichten op uit de database
$sql = "SELECT id, naam, bericht, afbeelding, tijd FROM berichten ORDER BY tijd DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Berichten</title>
    
</head>
<body>

<div class="container">

    <?php
    // Controleer of de gebruiker is ingelogd en toon de logout-knop
    if (isset($_SESSION['username'])) {
        echo "<a href='../auth/logout.php' class='btn btn-danger'>🚪 Uitloggen</a>";
    }
    ?>

    <h2>Gastenboek Berichten</h2>

    <?php
    // Controleer of er berichten zijn
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='bericht'>";
            echo "<p><strong>" . htmlspecialchars($row['naam']) . "</strong> - " . date('d-m-Y H:i', strtotime($row['tijd'])) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['bericht'])) . "</p>";

            // Controleer of er een afbeelding is en of deze bestaat
            if (!empty($row['afbeelding']) && file_exists($row['afbeelding'])) {
                echo "<img src='" . htmlspecialchars($row['afbeelding']) . "' alt='Afbeelding' class='bericht-afbeelding' style='max-width:300px; display:block;'>";
            } else {
                echo "<p><em>Er is geen afbeelding gepost.</em></p>";
            }

            // Bewerken en Verwijderen opties alleen voor de auteur van het bericht
            if (isset($_SESSION['username']) && strtolower($_SESSION['username']) === strtolower($row['naam'])) {
                echo "<a href='edit_post.php?id=" . $row['id'] . "' class='btn btn-bewerken'>✏️ Bewerken</a>";
                echo " <a href='delete_post.php?id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Weet je zeker dat je dit bericht wilt verwijderen?\")'>🗑️ Verwijderen</a>";
            }

            echo "</div><hr>";
        }
    } else {
        echo "<p>Er zijn nog geen berichten.</p>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
