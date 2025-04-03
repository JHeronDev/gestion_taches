<?php
include "db.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="css/script.js"></script>

</head>

<body>

    <nav>
        <a href="index.php"><button>Accueil</button></a>
        <button id="darkmode">Mode sombre</button>
    </nav>
    
        <h2>Inscription</h2>
        


    <form method="POST">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" required><br>
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" required><br>
        <label for="email">Email :</label>
        <input type="email" name="email" required><br>
        <label for="telephone">Mot de passe :</label>
        <input type="password" name="mot_de_passe" required><br>
        <button type="submit">Valider</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = trim($_POST["nom"]);
        $prenom = trim($_POST["prenom"]);
        $email = trim($_POST["email"]);
        $mdp = password_hash(trim($_POST["mot_de_passe"]), PASSWORD_DEFAULT);

        try {
            //Requête d'insertion
            $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (:nom, :prenom, :email, :mot_de_passe)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':mot_de_passe' => $mdp

            ]);
            $user_id = $pdo->lastInsertId();

            $_SESSION["user_id"] = $user_id;
            $_SESSION["user_name"] = $nom . " " . $prenom;

            echo "Inscription confirmée.   Redirection dans 1 seconde...";
            echo '<meta http-equiv="refresh" content="1;url=index.php">';
            

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Cet email est déjà utilisé.";
            } else {
                die("Erreur : " . $e->getMessage());
            }
        }
    }

    ?> <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <script src="js/script.js"></script>
</body>

</html>