<?php
require_once "includes/db.php";
$erreur = "";
$succes = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = trim($_POST["pseudo"]);
    $email = trim($_POST["email"]);
    $mot_de_passe = $_POST["mot_de_passe"];

    if (empty($pseudo) || empty($email) || empty($mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (strlen($mot_de_passe) < 6) {
        $erreur = "Le mot de passe doit faire au moins 6 caractères.";
    } else {
        $verif = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $verif->execute([$email]);
        if ($verif->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe, points, dernier_reset) VALUES (?, ?, ?, 7000, CURDATE())");
            $insert->execute([$pseudo, $email, $hash]);
            $succes = "Inscription réussie ! Tu peux maintenant te connecter.";
        }
    }
}
?>
<?php require_once "includes/header.php"; ?>

<h1 class="titre-section">Inscription</h1>

<form class="formulaire" method="POST" action="inscription.php">
    <?php if ($erreur) : ?>
        <div class="erreur"><?= $erreur ?></div>
    <?php endif; ?>
    <?php if ($succes) : ?>
        <div class="succes"><?= $succes ?></div>
    <?php endif; ?>

    <label for="pseudo">Pseudo</label>
    <input type="text" name="pseudo" id="pseudo" placeholder="Sacha" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="sacha@pokemon.com" required>

    <label for="mot_de_passe">Mot de passe</label>
    <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Au moins 6 caractères" required>

    <button type="submit" class="btn">S'inscrire</button>

    <p class="lien">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
</form>

<?php require_once "includes/footer.php"; ?>