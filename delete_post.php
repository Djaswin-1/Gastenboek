<?php
session_start();
include 'database/db_connect.php';

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

// Haal het bericht op uit de database
$stmt = $conn->prepare("SELECT id, naam, afbeelding FROM berichten WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Bericht niet gevonden.";
    exit;
}

$post = $result->fetch_assoc();

// Controleer of de ingelogde gebruiker de auteur is
if (strcasecmp($post['naam'], $username) !== 0) {
    echo "Je mag alleen je eigen berichten verwijderen.";
    exit;
}

// Verwijder de afbeelding als die bestaat
if (!empty($post['afbeelding']) && file_exists($post['afbeelding'])) {
    unlink($post['afbeelding']); // Verwijder afbeelding uit server
}

// Verwijder het bericht uit de database
$delete_stmt = $conn->prepare("DELETE FROM berichten WHERE id = ?");
$delete_stmt->bind_param("i", $post_id);
$delete_stmt->execute();

header("Location: post.php"); // Terug naar post-overzicht
exit;
?>
