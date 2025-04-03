<?php
include "db.php";
include "auth.php";
$id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer une tache</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav>
        <a href="index.php"><button>Accueil</button></a>
        <button id="darkmode">Mode sombre</button>
    </nav>
    <br>
    <a href="dashboard.php"><button id="back">Retour</button></a>
    <?php try {//Requête de suppression
            $sql = "DELETE FROM taches WHERE id = :id AND utilisateur_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":id" => $id, ":user_id" => $_SESSION["user_id"]]);
            if ($stmt->rowCount() > 0) {
                echo "Tâche supprimée avec succès ! Redirection dans 1 seconde...";
                echo '<meta http-equiv="refresh" content="1;url=dashboard.php">';
            } else {
                echo "Erreur : tâche introuvable ou vous n'avez pas la permission.";
            }
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        } ?>
    <script src="js/script.js"></script>
</body>

</html>