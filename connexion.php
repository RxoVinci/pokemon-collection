<?php
require_once "includes/db.php";
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $mot_de_passe = $_POST["mot_de_passe"];

    if (empty($email) || empty($mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        $req = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $req->execute([$email]);
        $utilisateur = $req->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur["mot_de_passe"])) {
            $_SESSION["utilisateur_id"] = $utilisateur["id"];
            $_SESSION["pseudo"] = $utilisateur["pseudo"];
            header("Location: index.php");
            exit;
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}
?>
<?php require_once "includes/header.php"; ?>

<h1 class="titre-section">Connexion</h1>

<form class="formulaire" method="POST" action="connexion.php">
    <?php if ($erreur) : ?>
        <div class="erreur"><?= $erreur ?></div>
    <?php endif; ?>

    <label for="email-connexion">Email</label>
    <input type="email" name="email" id="email-connexion" placeholder="sacha@pokemon.com" required>

    <label for="mot_de_passe">Mot de passe</label>
    <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Votre mot de passe" required>

    <button type="submit" class="btn">Se connecter</button>

    <p class="lien">Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
</form>

<?php require_once "includes/footer.php"; ?>