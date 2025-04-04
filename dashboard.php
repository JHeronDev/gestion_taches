<?php
include "db.php";
include "auth.php";
?>
<?php $aujourd_hui = new DateTime();
$seuil = 2;
$critere = "date_limite";
$ordre = "ASC";
$where = "1=1";
$params = [":user_id" => $_SESSION["user_id"]];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (isset($_GET["tri"])) {
    switch ($_GET["tri"]) {
        case "date":
            $critere = "date_limite";
            break;
        case "statut":
            $critere = "statut";
            break;
        case "prioritaire":
            $critere = "prioritaire";
            break;
    }
}
if (isset($_GET["ordre"])) {
    switch ($_GET["ordre"]) {
        case "asc":
            $ordre = "ASC";
            break;
        case "desc":
            $ordre = "DESC";
            break;
    }
}
if (isset($_GET["filtre"])) {
    switch ($_GET["filtre"]) {
        case "a_venir":
            $where = "date_limite >= CURDATE()";
            break;
        case "en_retard":
            $where = "date_limite < CURDATE() AND statut !='Terminée'";
            break;
        case " terminee":
            $where = "statut = 'Terminée'";
            break;
        case "prioritaire":
            $where = "prioritaire = 'Prioritaire'";
            break;
        case "loisir":
            $where = "categorie = 'Loisir'";
            break;
        case "travail":
            $where = "categorie = 'Travail'";
            break;
        case "transport":
            $where = "categorie = 'Transport'";
            break;
    }
}
$sql = "SELECT taches.*, GROUP_CONCAT(categories.categorie SEPARATOR ' - ') AS categorie FROM taches LEFT JOIN categories ON taches.id=categories.tache_id WHERE utilisateur_id = :user_id AND $where "
;
if (!empty($search)) {
    $sql .= " AND (titre LIKE :search OR description LIKE :search)";
}
$sql
    .= " GROUP BY taches.id ORDER BY $critere $ordre";
$stmt = $pdo->prepare($sql);
if (!empty($search)) {
    $params[':search'] = "%$search%";
}
$stmt->execute($params);
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des taches</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav>
        <a href="index.php"><button>Accueil</button></a>
        <button id="darkmode">Mode sombre</button>
    </nav>
    <div id="titre">
        <a href="ajout_tache.php"><button id="add">Ajouter une tache</button></a>
        <h2>Liste des taches</h2>
    </div>
    <form id="recherche" method="GET">
        <input type="text" name="search" placeholder="Rechercher une tâche..."
            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Rechercher</button>
        <a href="dashboard.php">🔄</a>
    </form>
    <form class="tri" method="GET">
        <label for="tri">Tri:</label>
        <select name="tri" id="tri" onchange="this.form.submit()">
            <option value="date" <?= (isset($_GET['tri']) && $_GET['tri'] == 'date') ? 'selected' : '' ?>>Date
            </option>
            <option value="statut" <?= (isset($_GET['tri']) && $_GET['tri'] == 'statut') ? 'selected' : '' ?>>
                Statut</option>
            <option value="prioritaire" <?= (isset($_GET['tri']) && $_GET['tri'] == 'prioritaire') ? 'selected' : '' ?>>
                Priorité</option>
        </select>
        <button id="fleche" type="submit" name="ordre" value="asc">⬆️</button>
        <button id="fleche" type="submit" name="ordre" value="desc">⬇️</button>

        <label for="filtre">Filtre:</label>
        <select name="filtre" id="filtre" onchange="this.form.submit()">
            <option value="" <?= (!isset($_GET['filtre']) || $_GET['filtre'] == '') ? 'selected' : '' ?>>Toutes
                les tâches</option>
            <option value="a_venir" <?= (isset($_GET['filtre']) && $_GET['filtre'] == 'a_venir') ? 'selected' : '' ?>>À
                venir</option>
            <option value="en_retard" <?= (isset($_GET['filtre']) && $_GET['filtre'] == 'en_retard') ? 'selected' : '' ?>>
                En retard</option>
            <option value="terminee" <?= (isset($_GET['filtre']) && $_GET['filtre'] == 'terminee') ? 'selected' : '' ?>>
                Terminée</option>
            <option value="prioritaire" <?= (isset($_GET['filtre']) && $_GET['filtre'] == 'prioritaire') ? 'selected' : '' ?>>Prioritaire</option>
            <option value="loisir" <?= (isset($_GET['filtre']) && $_GET['filtre'] == 'loisir') ? 'selected' : '' ?>>Loisir
            </option>
            <option value="travail" <?= (isset($_GET['filtre']) && $_GET['filtre'] == 'travail') ? 'selected' : '' ?>>
                Travail</option>
            <option value="transport" <?= (isset($_GET['filtre']) && $_GET['filtre'] == 'transport') ? 'selected' : '' ?>>
                Transport</option>
        </select>
    </form>
    <?php try {
        if (isset($_POST["tache_id"])) {
            $id = $_POST["tache_id"];
            $sql = "UPDATE taches SET statut = 'Terminée' WHERE id = :id AND utilisateur_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":id" => $id, ":user_id" => $_SESSION["user_id"]]);
            echo "Tâche terminée !  Actualisation dans 1 seconde...";
            echo '<meta http-equiv="refresh" content="1;url=dashboard.php">';
        }
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    } ?>



    <?php foreach ($taches as $tache):
        $date_limite = new DateTime($tache["date_limite"]);
        $interval = $aujourd_hui->diff($date_limite)->days;
        $rouge = ($date_limite > $aujourd_hui && $interval <= $seuil) ? 'rouge' : '';
        $rouge2 = ($tache["prioritaire"] == "Prioritaire") ? 'rouge' : '';
        $color = ($tache["statut"] == "En attente") ? 'orange' : 'vert'; ?>

        <table border="1">
            <tr>
                <th>Titre</th>
                <td>
                    <h3><?= $tache["titre"] ?></h3>
                </td>
            </tr>
            <tr>
                <th>Catégorie</th>
                <td><?= $tache["categorie"] ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= $tache["description"] ?></td>
            </tr>
            <tr>
                <th>Commentaire</th>
                <td><?= $tache["commentaire"] ?></td>
            </tr>
            <tr>
                <th class="<?= $rouge ?>">Date limite</th>
                <td class="<?= $rouge ?>"><?= $tache["date_limite"] ?></td>
            </tr>
            <tr>
                <th>Statut</th>
                <td class="<?= $color ?>"><?= $tache["statut"] ?></td>
            </tr>
            <tr>
                <th>Priorité</th>
                <td class="<?= $rouge2 ?>"><?= $tache["prioritaire"] ?></td>
            </tr>
            <tr>
                <th>Actions</th>
                <td>
                    <div class="boutons">
                        <form method="POST"> <input type="hidden" name="tache_id" value="<?= $tache["id"] ?>">
                            <button id="ter" type="submit" name="terminer"
                                onclick="return confirm('Terminer cette tache ?');">Terminer</button>
                        </form>

                        <a href="modifier_tache.php?id=<?= $tache["id"] ?>"><button>Modifier</button></a>
                        <a href="supprimer_tache.php?id=<?= $tache["id"] ?>"
                            onclick="return confirm('Supprimer cette tache ?');"><button id="del">Supprimer</button></a>
                    </div>
                </td>
            </tr>
        </table>
    <?php endforeach; ?>


    <script src="js/script.js"></script>
</body>

</html>