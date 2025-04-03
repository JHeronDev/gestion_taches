<?php

$host = "localhost";
$db_name = "gestion_taches";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
} catch (PDOException $exception) {
    echo "Erreur de connexion : " . $exception->getMessage();
}
