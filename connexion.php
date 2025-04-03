<?php
include "db.php";
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <nav><a href="index.php"><button>Accueil</button></a>
        <button id="darkmode">Mode sombre</button>
    </nav>
    
        <h2>Connexion</h2>
        

    <form method="POST">
        <label for="email">Email :</label>
        <input type="email" name="email" required><br>
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" required><br>
        <button type="submit">Se connecter</button>
    </form>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $mdp = trim($_POST["mot_de_passe"]);

        try {
            // Vérifier si l'email existe
            $sql = "SELECT id, nom, prenom, mot_de_passe FROM utilisateurs WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($mdp, $user["mot_de_passe"])) {
                // Authentification réussie
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["nom"] . " " . $user["prenom"];
                echo "Connexion réussie !   Redirection dans 1 seconde...";
                echo '<meta http-equiv="refresh" content="1;url=index.php">';
                
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
    ?>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <script src="js/script.js"></script>
</body>

</html>