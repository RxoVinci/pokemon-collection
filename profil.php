<?php
require_once "includes/db.php";

if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: connexion.php");
    exit;
}

$utilisateur_id = $_SESSION["utilisateur_id"];
$pseudo = $_SESSION["pseudo"];

$req = $pdo->prepare("SELECT pokemon_id, COUNT(*) as nombre FROM collections WHERE utilisateur_id = ? GROUP BY pokemon_id");
$req->execute([$utilisateur_id]);
$cartes = $req->fetchAll();
?>
<?php require_once "includes/header.php"; ?>

<h1 style="text-align:center;">Profil de <?= htmlspecialchars($pseudo) ?></h1>
<p style="text-align:center; margin-bottom:30px;">Ma collection : <?= count($cartes) ?> Pokémon différents</p>

<div class="grille-cartes" id="ma-collection">
    <?php if (empty($cartes)) : ?>
        <p>Tu n'as pas encore de cartes. <a href="booster.php">Ouvre ton premier booster !</a></p>
    <?php endif; ?>
</div>

<script>
var mesCartes = <?= json_encode($cartes) ?>;
var grille = document.getElementById("ma-collection");

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
</script>

<?php require_once "includes/footer.php"; ?>