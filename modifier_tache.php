<?php
include "db.php";
include "auth.php";

$id = $_GET['id'];
try {
    //Requête de récuperation des infos
    $sql = "SELECT * FROM taches WHERE id = :id AND utilisateur_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
    $stmt->execute();
    $tache = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tache) {
        die("Tâche introuvable ou accès non autorisé.");
    }
    $sql = "SELECT categorie FROM categories WHERE tache_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $categories = array_column($categories, 'categorie');
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier</title>
    <link rel="stylesheet" href="css/style.css">

</head>

<body>
    <nav>
        <a href="index.php"><button>Accueil</button></a>
        <button id="darkmode">Mode sombre</button>

    </nav>


    <div id="titre">
        <h2>Modifier une tache</h2>
        <a href="dashboard.php"><button id="back">Retour</button></a>
    </div>


    <form method="POST">

        <label for="titre">Titre :</label>
        <input type="text" name="titre" value="<?= $tache["titre"] ?>" required>
        <label for="description">Description :</label>
        <input type="text" name="description" value="<?= $tache["description"] ?>" required>
        <label for="commentaire">Commentaire :</label>
        <input type="text" name="commentaire" value="<?= $tache["commentaire"] ?>">
        <label for="date_limite">Date limite :</label>
        <input type="date" name="date_limite" value="<?= $tache["date_limite"] ?>" required>
        <label>Catégories :</label>
        <div id="radio">
            <input type="checkbox" id="Loisir" name="categorie[]" value="Loisir" <?php foreach ($categories as $cat)
                if ($cat == "Loisir")
                    echo "checked"; ?>>
            <label for="Loisir">Loisir</label>
            <input type="checkbox" id="Travail" name="categorie[]" value="Travail" <?php foreach ($categories as $cat)
                if ($cat == "Travail")
                    echo "checked"; ?>>
            <label for="Travail">Travail</label>
            <input type="checkbox" id="Transport" name="categorie[]" value="Transport" <?php foreach ($categories as $cat)
                if ($cat == "Transport")
                    echo "checked"; ?>>
            <label for="Transport">Transport</label>
        </div>
        <div id="radio">
            <label>Statut :</label>
            <input type="radio" id="En attente" name="statut" value="En attente" <?= ($tache["statut"] == "En attente") ? "checked" : "" ?>>
            <label for="En attente">En attente</label>
            <input type="radio" id="Terminée" name="statut" value="Terminée" <?= ($tache["statut"] == "Terminée") ? "checked" : "" ?>>
            <label for="Terminée">Terminée</label>
        </div>
        <div id="radio">
            <label>Priorité :</label>
            <input type="radio" id="Normale" name="prioritaire" value="Normale" <?= ($tache["prioritaire"] == "Normale") ? "checked" : "" ?>>
            <label for="Normale">Normale</label>
            <input type="radio" id="Prioritaire" name="prioritaire" value="Prioritaire"
                <?= ($tache["prioritaire"] == "Prioritaire") ? "checked" : "" ?>>
            <label for="Prioritaire">Prioritaire</label>
        </div>
        <div id="boutons">
            <button type="submit">Modifier</button>
        </div>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $titre = $_POST["titre"];
        $categories_update = $_POST["categorie"];
        $description = $_POST["description"];
        $commentaire = $_POST["commentaire"];
        $date_limite = $_POST["date_limite"];
        $statut = $_POST["statut"];
        $prioritaire = $_POST["prioritaire"];
        
        try { //Requête de modification
            $sql = "UPDATE taches SET titre=:titre, description=:description, commentaire=:commentaire, statut=:statut, prioritaire=:prioritaire, date_limite = :date_limite WHERE id = :id AND utilisateur_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":titre" => $titre, ":description" => $description, ":commentaire" => $commentaire, ":date_limite" => $date_limite, ":statut" => $statut, ":prioritaire" => $prioritaire, ":id" => $id, ":user_id" => $_SESSION["user_id"]]);

            $categories_del = array_diff($categories, $categories_update);
            if (!empty($categories_del)) {
                $sql = "DELETE FROM categories WHERE tache_id = ? AND categorie IN (" . implode(',', array_fill(0, count($categories_del), '?')) . ")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge([$id], $categories_del));
            }

            $categories_add = array_diff($categories_update, $categories);
            if (!empty($categories_add)) {
                $sql = "INSERT INTO categories (tache_id, categorie) VALUES " . implode(',', array_fill(0, count($categories_add), "(?, ?)"));
                $stmt = $pdo->prepare($sql);
                $params = [];
                foreach ($categories_add as $cat) {
                    $params[] = $id;
                    $params[] = $cat;
                }
                $stmt->execute($params);
            }
            echo "Tache modifiée avec succès ! Redirection...";
            echo '<meta http-equiv="refresh" content="0;url=dashboard.php">';
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    } ?>
    <script src="js/script.js"></script>
</body>

</html>