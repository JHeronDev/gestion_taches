<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_id"])) {
header('Location: connexion.php');
exit;
}
?>