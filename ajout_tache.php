<?php
include "db.php";
include "auth.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout d'une tache</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="css/script.js"></script>

</head>

<body>

    <nav>
        <a href="index.php"><button>Accueil</button></a>
        <button id="darkmode">Mode sombre</button>
    </nav>
    <div id="titre">
        <h2>Ajouter une tache</h2>
        <a href="dashboard.php"><button id="back">Retour</button></a>
    </div>


    <form method="POST">
        <label for="titre">Titre :</label>
        <input type="text" name="titre" required>
        <label for="description">Description :</label>
        <input type="text" name="description" required>
        <label for="commentaire">Commentaire :</label>
        <input type="text" name="commentaire">
        <label for="date_limite">Date limite :</label>
        <input type="date" name="date_limite" required>
        <label>Catégories :</label>
        <div id="radio">
            <input type="checkbox" id="Loisir" name="categorie[]" value="Loisir">
            <label for="Loisir">Loisir</label>
            <input type="checkbox" id="Travail" name="categorie[]" value="Travail">
            <label for="Travail">Travail</label>
            <input type="checkbox" id="Transport" name="categorie[]" value="Transport">
            <label for="Transport">Transport</label>
        </div>
        <div id="radio">
            <label>Priorité :</label>
            <input type="radio" id="Normale" name="prioritaire" value="Normale" checked>
            <label for="Normale">Normale</label>
            <input type="radio" id="Prioritaire" name="prioritaire" value="Prioritaire">
            <label for="Prioritaire">Prioritaire</label>
        </div>
        <div id="boutons">
            <button type="submit">Ajouter</button>
            <button id="del" type="reset" onclick="return confirm('Tout supprimer ?');">Reset</button>
        </div>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $utilisateur_id = $_SESSION["user_id"];
        $titre = $_POST["titre"];
        $categorie = $_POST["categorie"];
        $description = $_POST["description"];
        $commentaire = $_POST["commentaire"];
        $date_limite = $_POST["date_limite"];
        $prioritaire = $_POST["prioritaire"];


        try {
            //Requête d'insertion
            $sql = "INSERT INTO taches (utilisateur_id, titre, description, commentaire, date_limite, prioritaire) VALUES (:utilisateur_id, :titre, :description, :commentaire, :date_limite, :prioritaire)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':utilisateur_id' => $utilisateur_id,
                ':titre' => $titre,
                ':description' => $description,
                ':commentaire' => $commentaire,
                ':date_limite' => $date_limite,
                ':prioritaire' => $prioritaire
            ]);
            $tache_id = $pdo->lastInsertId();
            if (!empty($_POST["categorie"])) {
                foreach ($categorie as $cat) {
                    $sql = "INSERT INTO categories (tache_id, categorie) VALUES (:tache_id, :categorie)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':tache_id' => $tache_id,
                        ':categorie' => $cat
                    ]);
                }
            }
            echo "Tache ajoutée avec succès ! Actualisation...";
            echo '<meta http-equiv="refresh" content="0;url=ajout_tache.php">';
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    } ?>
    <script src="js/script.js"></script>
</body>

</html>