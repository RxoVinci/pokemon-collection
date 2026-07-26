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
            $insert = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (?, ?, ?)");
            $insert->execute([$pseudo, $email, $hash]);
            $succes = "Inscription réussie ! Tu peux maintenant te connecter.";
        }
    }
}
?>
<?php require_once "includes/header.php"; ?>

<div class="form-container">
    <h1>Inscription</h1>

    <?php if ($erreur) : ?>
        <div class="erreur"><?= $erreur ?></div>
    <?php endif; ?>

    <?php if ($succes) : ?>
        <div class="succes"><?= $succes ?></div>
    <?php endif; ?>

    <form method="POST" action="inscription.php">
        <label for="pseudo">Pseudo</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required>

        <button type="submit">S'inscrire</button>

        <p class="lien">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    </form>
</div>

<?php require_once "includes/footer.php"; ?>