<?php
session_start();
include 'database/db_connect.php'; // Databaseverbinding

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Controleer of er een post-ID is meegegeven
if (!isset($_GET['id'])) {
    header("Location: post.php");
    exit;
}

$post_id = intval($_GET['id']);
$username = $_SESSION['username'];
$error = "";

// Haal het bericht op uit de database
$stmt = $conn->prepare("SELECT id, bericht, naam, afbeelding FROM berichten WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "Bericht niet gevonden.";
} else {
    $post = $result->fetch_assoc();

    // Controleer of de ingelogde gebruiker de auteur is
    if (strtolower($post['naam']) !== strtolower($username)) {
        $error = "Je mag alleen je eigen berichten bewerken.";
    }
}

// Verwerk het formulier als er geen fouten zijn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $nieuw_bericht = trim($_POST['bericht']);
    $nieuwe_afbeelding = $post['afbeelding']; // Standaard de bestaande afbeelding

    // Controleer of er een nieuwe afbeelding is geüpload
    if (!empty($_FILES['afbeelding']['name'])) {
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES['afbeelding']['name'], PATHINFO_EXTENSION));

        // Controleer of het bestand een afbeelding is
        $check = getimagesize($_FILES['afbeelding']['tmp_name']);
        if ($check === false) {
            $error = "Fout: Het geüploade bestand is geen afbeelding.";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            $error = "Fout: Alleen JPG, JPEG en PNG bestanden zijn toegestaan.";
        } elseif ($_FILES["afbeelding"]["size"] > 2 * 1024 * 1024) {
            $error = "Fout: De afbeelding is te groot. Maximale grootte is 2MB.";
        } else {
            // Verwijder de oude afbeelding als die bestaat
            if (!empty($post['afbeelding']) && file_exists($post['afbeelding'])) {
                unlink($post['afbeelding']);
            }

            // Sla de nieuwe afbeelding op met een unieke naam
            $nieuwe_afbeelding = $target_dir . uniqid() . "." . $imageFileType;
            if (!move_uploaded_file($_FILES['afbeelding']['tmp_name'], $nieuwe_afbeelding)) {
                $error = "Fout bij het uploaden van de afbeelding.";
            }
        }
    }

    // Update het bericht in de database als er geen fouten zijn
    if (empty($error) && !empty($nieuw_bericht)) {
        $update_stmt = $conn->prepare("UPDATE berichten SET bericht = ?, afbeelding = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $nieuw_bericht, $nieuwe_afbeelding, $post_id);
        $update_stmt->execute();
        header("Location: post.php");
        exit;
    } elseif (empty($nieuw_bericht)) {
        $error = "Fout: Het bericht mag niet leeg zijn.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bericht Wijzigen</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Functie om een preview van de afbeelding te tonen
        function showImagePreview(input) {
            const preview = document.getElementById("image-preview");
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Afbeelding preview" style="max-width: 300px; max-height: 300px; margin-top: 10px;" />';
            };

            // Lees het bestand als een data-URL (base64)
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Bericht bewerken</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="bericht">Je bericht:</label>
            <textarea id="bericht" name="bericht" required><?php echo htmlspecialchars($post['bericht'] ?? ''); ?></textarea>

            <label for="afbeelding">Afbeelding wijzigen (JPG/PNG, max 2MB):</label>

            <!-- Toegevoegde div-container voor de upload knop -->
            <div class="upload-btn-container">
                <label for="afbeelding" class="upload-btn">Kies een afbeelding</label>
                <input type="file" id="afbeelding" name="afbeelding" accept=".jpg,.jpeg,.png" onchange="showImagePreview(this)">
            </div>

            <!-- Weergave van de nieuwe afbeelding (indien geselecteerd) -->
            <div id="image-preview"></div>

            <button type="submit">Opslaan</button>
        </form>
        <br>
        <a href="post.php">Terug naar berichten</a>
    </div>
</body>
</html>
