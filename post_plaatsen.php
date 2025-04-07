<?php
session_start();
include 'database/db_connect.php'; // Databaseverbinding toevoegen

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$uploadOk = 1;
$afbeelding_pad = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = trim($_POST["naam"]);
    $bericht = trim($_POST["bericht"]);

    if (!empty($_FILES["afbeelding"]["name"])) {
        $target_dir = "uploads/"; // Zorg dat deze map bestaat en schrijfbaar is
        $file_name = $_FILES["afbeelding"]["name"];
        $file_tmp = $_FILES["afbeelding"]["tmp_name"];
        $file_size = $_FILES["afbeelding"]["size"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ["jpg", "jpeg", "png"];

        // Controleer of bestand een afbeelding is
        $check = getimagesize($file_tmp);
        if ($check === false) {
            $error = "❌ Bestand is geen afbeelding.";
            $uploadOk = 0;
        }

        // Controleer de bestandsextensie
        if (!in_array($file_ext, $allowed_ext)) {
            $error = "❌ Alleen JPG en PNG bestanden zijn toegestaan.";
            $uploadOk = 0;
        }

        // Controleer bestandsgrootte (max 2MB)
        if ($file_size > 2 * 1024 * 1024) {
            $error = "❌ Bestand is te groot. Maximale grootte is 2MB.";
            $uploadOk = 0;
        }

        // Genereer een veilige, unieke bestandsnaam
        $new_file_name = uniqid("img_", true) . "." . $file_ext;
        $afbeelding_pad = $target_dir . $new_file_name;

        // Verplaats bestand naar de uploads-map
        if ($uploadOk == 1 && !move_uploaded_file($file_tmp, $afbeelding_pad)) {
            $error = "❌ Er is een fout opgetreden bij het uploaden.";
            $uploadOk = 0;
        }
    }

    if ($uploadOk == 1) {
        // Bericht opslaan in de database
        $sql = "INSERT INTO berichten (naam, bericht, afbeelding, tijd) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $naam, $bericht, $afbeelding_pad);

        if ($stmt->execute()) {
            header("Location: post.php");
            exit;
        } else {
            $error = "❌ Fout bij plaatsen van het bericht.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom</title>
    <link rel="stylesheet" href="style.css?ver=<?php echo time(); ?>"> <!-- Zorgt voor vernieuwde CSS -->
    <script>
        // JavaScript om de afbeelding preview te tonen
        function showPreview(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById("afbeelding-preview");
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Afbeelding preview" style="max-width: 100%; max-height: 200px; margin-top: 10px;" />';
            };

            // Lees het bestand als een Data URL (base64)
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>
<body>

    <div class="container">
        <a href="index.php" class="btn">🏠Home</a> <!-- Home knop toegevoegd -->

        <h1>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

        <h2>Bericht plaatsen</h2>
        <div class="formulier-container">
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <label for="naam">Naam</label>
                <input type="text" id="naam" name="naam" required>

                <label for="afbeelding">Voeg afbeelding toe (JPG/PNG, max 2MB)</label>

                <!-- Knop-container voor de upload -->
                <div class="upload-btn-container">
                    <label for="afbeelding" class="upload-btn">Kies een afbeelding</label>
                    <input type="file" id="afbeelding" name="afbeelding" accept=".jpg,.jpeg,.png" onchange="showPreview(event)">
                </div>

                <!-- Preview voor de afbeelding -->
                <div id="afbeelding-preview"></div>

                <label for="bericht">Bericht</label>
                <textarea id="bericht" name="bericht" required></textarea>

                <button type="submit">Plaats bericht</button>
            </form>
        </div>

        <br>
        <a href="logout.php">Uitloggen</a>
    </div>

</body>
</html>
