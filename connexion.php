<?php
require_once "includes/db.php";
$erreur = "";
$succes = "";
$mode = isset($_GET['mode']) && $_GET['mode'] === 'inscription' ? 'inscription' : 'connexion';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'inscription') {
        $mode = 'inscription';
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
                $mode = 'connexion';
            }
        }
    } else {
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
}
?>
<?php require_once "includes/header.php"; ?>

<h1 class="titre-section"><?= $mode === 'connexion' ? 'Connexion' : 'Inscription' ?></h1>

<div class="onglets-auth">
    <a href="?mode=connexion" class="onglet <?= $mode === 'connexion' ? 'actif' : '' ?>">Connexion</a>
    <a href="?mode=inscription" class="onglet <?= $mode === 'inscription' ? 'actif' : '' ?>">Inscription</a>
</div>

<?php if ($erreur) : ?>
    <div class="erreur" style="max-width:450px; margin:0 auto 15px;"><?= $erreur ?></div>
<?php endif; ?>
<?php if ($succes) : ?>
    <div class="succes" style="max-width:450px; margin:0 auto 15px;"><?= $succes ?></div>
<?php endif; ?>

<?php if ($mode === 'connexion') : ?>
    <form class="formulaire" method="POST" action="connexion.php">
        <label for="email-connexion">Email</label>
        <input type="email" name="email" id="email-connexion" placeholder="sacha@pokemon.com" required>

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Votre mot de passe" required>

        <button type="submit" class="btn">Se connecter</button>
    </form>
<?php else : ?>
    <form class="formulaire" method="POST" action="connexion.php?mode=inscription">
        <input type="hidden" name="action" value="inscription">

        <label for="pseudo">Pseudo</label>
        <input type="text" name="pseudo" id="pseudo" placeholder="Sacha" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="sacha@pokemon.com" required>

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Au moins 6 caractères" required>

        <button type="submit" class="btn">S'inscrire</button>
    </form>
<?php endif; ?>

<?php require_once "includes/footer.php"; ?>