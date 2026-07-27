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

<h1 class="titre-section">👤 Profil - <?= htmlspecialchars($pseudo) ?></h1>

<div class="points-display">
    💎 Points disponibles : <?= $user['points'] ?> / 7000
</div>

<p style="text-align:center; margin-bottom:30px; font-size:1.1em; color: var(--texte-gris);">
    Ma collection : <?= count($cartes) ?> Pokémon différents
</p>

<div class="grille" id="ma-collection">
    <?php if (empty($cartes)) : ?>
        <p class="chargement" style="grid-column: 1 / -1;">Tu n'as pas encore de cartes. <a href="catalogue.php" style="color: var(--accent-2);">Va en collecter !</a></p>
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
                var image = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/" + data.id + ".png";
                var typePrincipal = data.types[0].type.name;
                var c = document.createElement("a");
                c.className = "carte-pokemon";
                c.href = "carte.php?id=" + carte.pokemon_id;
                c.innerHTML =
                    '<img src="' + image + '" alt="' + data.name + '">' +
                    '<h3>' + data.name + '</h3>' +
                    '<span class="type-badge type-' + typePrincipal + '">' + typePrincipal + '</span>' +
                    '<div class="points-carte">x' + carte.nombre + '</div>';
                grille.appendChild(c);
            });
    });
}
</script>

<?php require_once "includes/footer.php"; ?>