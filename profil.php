<?php
include "db.php";
include "auth.php";
$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id = :user_id");
$stmt->execute([":user_id" => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav><a href="index.php"><button>Accueil</button></a>
        <button id="darkmode">Mode sombre</button>
    </nav>

    <h2>Profil</h2>

    <form method="POST">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($user["nom"]) ?>" required><br>

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" value="<?= htmlspecialchars($user["prenom"]) ?>" required><br>

        <label for="email">Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required><br>

        <label for="nouveau_mdp">Nouveau mot de passe :</label>
        <input type="password" name="nouveau_mdp" placeholder="Optionnel"><br>

        <button type="submit">Modifier</button>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = trim($_POST["nom"]);
        $prenom = trim($_POST["prenom"]);
        $email = trim($_POST["email"]);
        $new_password = trim($_POST["nouveau_mdp"]);
        try {
            $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email WHERE id = :user_id";
            $params = [":nom" => $nom, ":prenom" => $prenom, ":email" => $email, ":user_id" => $user_id];
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, mot_de_passe = :mot_de_passe WHERE id = :user_id";
                $params[":mot_de_passe"] = $hashed_password;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo "Mise à jour réussie !";
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    } ?>
     <script src="js/script.js"></script>
</body>

</html>