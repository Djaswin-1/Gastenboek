<?php
session_start();
session_unset(); // Verwijdert alle sessievariabelen
session_destroy(); // Vernietigt de sessie

header("Location: login.php"); // Stuur de gebruiker terug naar de inlogpagina
exit;
?>
