<?php
require_once "includes/db.php";

if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: connexion.php");
    exit;
}

$utilisateur_id = $_SESSION["utilisateur_id"];
$pseudo = $_SESSION["pseudo"];

$userReq = $pdo->prepare("SELECT points, dernier_reset FROM utilisateurs WHERE id = ?");
$userReq->execute([$utilisateur_id]);
$user = $userReq->fetch();

$today = date('Y-m-d');
if ($user['dernier_reset'] != $today) {
    $resetReq = $pdo->prepare("UPDATE utilisateurs SET points = 7000, dernier_reset = ? WHERE id = ?");
    $resetReq->execute([$today, $utilisateur_id]);
    $user['points'] = 7000;
}

$req = $pdo->prepare("SELECT pokemon_id, COUNT(*) as nombre FROM collections WHERE utilisateur_id = ? GROUP BY pokemon_id");
$req->execute([$utilisateur_id]);
$cartes = $req->fetchAll();
?>
<?php require_once "includes/header.php"; ?>

<h1 class="titre-page">Mon Profil - <?= htmlspecialchars($pseudo) ?></h1>

<div class="points-display">
    💎 Points disponibles : <?= $user['points'] ?> / 7000
</div>

<p style="text-align:center; margin-bottom:30px; font-size:1.1em;">Ma collection : <?= count($cartes) ?> Pokémon différents</p>

<div class="grille-cartes" id="ma-collection">
    <?php if (empty($cartes)) : ?>
        <p style="grid-column: 1 / -1; text-align: center;">Tu n'as pas encore de cartes. <a href="catalogue.php">Va en collecter !</a></p>
    <?php endif; ?>
</div>

<script>
var mesCartes = <?= json_encode($cartes) ?>;
var grille = document.getElementById("ma-collection");

if (mesCartes.length > 0) {
    grille.innerHTML = "";
    mesCartes.forEach(function(carte) {
        fetch("https://pokeapi.co/api/v2/pokemon/" + carte.pokemon_id)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var c = document.createElement("a");
                c.className = "carte-pokemon";
                c.href = "carte.php?id=" + carte.pokemon_id;
                c.innerHTML =
                    '<img src="' + data.sprites.front_default + '" alt="' + data.name + '">' +
                    '<h3>' + data.name + '</h3>' +
                    '<span class="type">x' + carte.nombre + '</span>';
                grille.appendChild(c);
            });
    });
}
</script>

<?php require_once "includes/footer.php"; ?>