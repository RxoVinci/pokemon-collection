<?php
require_once "includes/db.php";
$utilisateur_id = isset($_SESSION["utilisateur_id"]) ? $_SESSION["utilisateur_id"] : null;
$userPoints = 0;
$message = "";

if ($utilisateur_id) {
    $userReq = $pdo->prepare("SELECT points, dernier_reset FROM utilisateurs WHERE id = ?");
    $userReq->execute([$utilisateur_id]);
    $user = $userReq->fetch();
    $today = date('Y-m-d');
    if ($user['dernier_reset'] != $today) {
        $resetReq = $pdo->prepare("UPDATE utilisateurs SET points = 7000, dernier_reset = ? WHERE id = ?");
        $resetReq->execute([$today, $utilisateur_id]);
        $userPoints = 7000;
    } else {
        $userPoints = $user['points'];
    }
}

if ($utilisateur_id && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pokemon_id"])) {
    $pokemon_id = intval($_POST["pokemon_id"]);
    $cout = intval($_POST["cout"]);
    if ($userPoints >= $cout) {
        $ins = $pdo->prepare("INSERT INTO collections (utilisateur_id, pokemon_id) VALUES (?, ?)");
        $ins->execute([$utilisateur_id, $pokemon_id]);
        $upd = $pdo->prepare("UPDATE utilisateurs SET points = points - ? WHERE id = ?");
        $upd->execute([$cout, $utilisateur_id]);
        $userPoints -= $cout;
        $message = "Carte collectée avec succès !";
    } else {
        $message = "Pas assez de points.";
    }
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 1;
?>
<?php require_once "includes/header.php"; ?>

<a href="catalogue.php" class="btn btn-retour">⬅ Retour au catalogue</a>

<?php if ($message) : ?>
    <div class="succes"><?= $message ?></div>
<?php endif; ?>

<p class="chargement" id="chargement">Chargement du Pokémon...</p>
<div class="detail" id="detail"></div>

<script>
var idPokemon = <?= $id ?>;
var utilisateur_id = <?= json_encode($utilisateur_id) ?>;
var userPoints = <?= json_encode($userPoints) ?>;

function getCout(id) {
    if (id % 25 === 0) return 15000;
    if (id % 10 === 0) return 8000;
    if (id % 5 === 0) return 3000;
    if (id % 3 === 0) return 2000;
    return 500;
}

fetch("https://pokeapi.co/api/v2/pokemon/" + idPokemon)
    .then(function(r) { return r.json(); })
    .then(function(pokemon) {
        document.getElementById("chargement").style.display = "none";
        var image = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/" + pokemon.id + ".png";
        var badgesTypes = pokemon.types.map(function(t) {
            return '<span class="type-badge type-' + t.type.name + '">' + t.type.name + '</span>';
        }).join("");
        var hp = pokemon.stats[0].base_stat;
        var attaque = pokemon.stats[1].base_stat;
        var defense = pokemon.stats[2].base_stat;
        var vitesse = pokemon.stats[5].base_stat;
        var taille = pokemon.height / 10;
        var poids = pokemon.weight / 10;
        var cout = getCout(pokemon.id);
        
        var btnCollecter = "";
        if (utilisateur_id) {
            var disabled = userPoints < cout ? "disabled" : "";
            btnCollecter = '<form method="POST" style="margin-top:20px;">' +
                '<input type="hidden" name="pokemon_id" value="' + pokemon.id + '">' +
                '<input type="hidden" name="cout" value="' + cout + '">' +
                '<button type="submit" class="btn-collecter" ' + disabled + '>💎 Collecter (' + cout + ' pts)</button>' +
                '</form>';
        } else {
            btnCollecter = '<p style="margin-top:20px;"><a href="connexion.php" style="color:var(--accent-2);">Connecte-toi pour collecter</a></p>';
        }
        
        document.getElementById("detail").innerHTML =
            '<img src="' + image + '" alt="' + pokemon.name + '">' +
            '<div class="detail-infos">' +
                '<p class="numero">N° ' + pokemon.id + '</p>' +
                '<h1>' + pokemon.name + '</h1>' +
                '<div class="types">' + badgesTypes + '</div>' +
                '<ul class="stats">' +
                    '<li><span class="nom-stat">❤️ HP</span> <div class="barre-stat" style="width:' + hp + 'px"></div> ' + hp + '</li>' +
                    '<li><span class="nom-stat">⚔️ Attaque</span> <div class="barre-stat" style="width:' + attaque + 'px"></div> ' + attaque + '</li>' +
                    '<li><span class="nom-stat">🛡️ Défense</span> <div class="barre-stat" style="width:' + defense + 'px"></div> ' + defense + '</li>' +
                    '<li><span class="nom-stat">💨 Vitesse</span> <div class="barre-stat" style="width:' + vitesse + 'px"></div> ' + vitesse + '</li>' +
                '</ul>' +
                '<p class="mensurations">📏 Taille : ' + taille + ' m &nbsp;|&nbsp; ⚖️ Poids : ' + poids + ' kg</p>' +
                btnCollecter +
            '</div>';
    });
</script>

<?php require_once "includes/footer.php"; ?>