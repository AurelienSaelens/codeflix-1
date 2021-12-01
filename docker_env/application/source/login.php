<?php
// On démarre la session PHP
session_start();
// Pour empêcher d'aller sur la page connexion si déjà connecté : 
if (isset($_SESSION["utilisateur"])){
    header("Location: profil.php");
    exit;
}

if (!empty($_POST)) {
    // var_dump($_POST);
    // Le formulaire a été envoyé
    // On verifie que TOUS les champs requis sont remplis
    if (
        isset($_POST["email"], $_POST["password"])
        && !empty($_POST["email"]) && !empty($_POST["password"])
    ) {
        // Vérifier que c'est un email
        $_SESSION["error"] = []; // Créer un espace erreur dans la session

        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $_SESSION["error"][] =  "L'adresse email est incorrecte";
        }

        // Vérifier si l'email existe dans la base de données
        // Connexion à la base de données
        require_once "server_connection.php";
        // requête SQL
        $sql = "SELECT * FROM `utilisateurs` WHERE `email` = :email";
        // préparer la requête
        $query = $conn->prepare($sql);
        $query->bindValue(":email", $_POST["email"], PDO::PARAM_STR);
        //exécuter la requête
        $query->execute();

        $user = $query->fetch();

        // Si l'utilisateur n'existe pas :
        if (!$user) {
            $_SESSION["error"][] = "L'utilisateur et/ou le mot de passe est incorrect"; // ne pas donner d'indice 
        }

        // si user existant, on peut vérifier son mot de passe avec password_verify()
        if (!password_verify($_POST["password"], $user["password"])) {
            $_SESSION["error"][] = "L'utilisateur et/ou le mot de passe est incorrect";
        }

        // L'utilisateur et le mot de passe sont corrects
        // On peut connecter l'utilisateur = ouvrir la session PHP
        

        // On stocke dans $_SESSION les informations de l'utilisateur
        $_SESSION["utilisateur"] = [
            "user_id" => $user["user_id"],
            "prenom" => $user['prenom'],
            "nom" => $user['nom'],
            "pseudo" => $user["pseudo"],
            "email" => $user["email"],
            //"roles" => $user["roles"]
        ];

        // On peut rediriger vers la page de profil
        header("Location: index.php");

    }
}

// inclure header
/* include "header.php"; */
// inclure navbar
/* include "navbar.php"; */
?>



<?php
// Pour afficher les erreurs 
if (isset($_SESSION["error"])) {
    foreach ($_SESSION["error"] as $message) {
?>
        <p><?= $message ?></p>
<?php
    }
    // Une fois qu'une erreur a été affichée, il faut l'effacer
    unset($_SESSION["error"]);
}
?>

<link rel="stylesheet" href="./css/login.css">

<div class="popUpForm"> 
    <div class="contenu">
        <div class="close">+</div>
        <h1>Connexion</h1>
        <div class="formulaire">
            <form method="post">
                <div class="elementForm">
                    <label for="email">Email</label>
                    <input type="email" placeholder="aaron.swartz@gmail.com" name="email" id="email">
                </div>
                <div class="elementForm">
                    <label for="password">Mot de passe</label>
                    <input type="password" placeholder="bestcoder" name="password" id="password">
                </div>
                <div class="elementForm">
                    <button class="btn" type="submit">Me connecter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="./js/login.js"></script>
<?php
// inclure le footer
/* include "footer.php"; */
?>