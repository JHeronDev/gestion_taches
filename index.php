<?php
include "db.php";
?>
<?php
if (!isset($_SESSION["user_id"])) {
    $connect = false;
} else {
    $id = $_SESSION["user_id"];
    $connect = true;
} ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Contacts</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav>
        <a href="index.php"><button>Accueil</button></a>
        <?php if (!$connect) {
            echo '<a href="inscription.php"><button>Inscription</button></a>
                    <a href="connexion.php"><button>Connexion</button></a>';
        } else {
            echo '<a href="dashboard.php"><button>Dashboard</button></a>
                <a href="profil.php"><button>Profil</button></a>
                <a href="deconnexion.php"><button>Deconnexion</button></a> ';
        }
        ?>
        <button id="darkmode">Mode sombre</button>
    </nav>

    <?php if (!$connect) {
        echo "<h2>Vous n'êtes pas connecté.</h2>";
    } else {
        $sql = "SELECT nom, prenom FROM utilisateurs WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<h2>Bonjour " . $user["prenom"] . " " . $user["nom"] . ".</h2>";
        echo "Pour accéder à vos tâches, allez sur Dashboard.<br><br>";
        echo "Pour accéder à votre profil, allez sur Profil.";
    } ?>
    <script src="js/script.js"></script>
</body>

</html>