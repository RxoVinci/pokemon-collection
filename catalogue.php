<?php require_once "includes/db.php"; ?>
<?php
$utilisateur_id = isset($_SESSION["utilisateur_id"]) ? $_SESSION["utilisateur_id"] : null;
$userPoints = 0;

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
    }
}
?>
<?php require_once "includes/header.php"; ?>

<h1 class="titre-section">Le catalogue</h1>

<?php if ($utilisateur_id) : ?>
    <div class="points-display">💎 Vos points : <?= $userPoints ?> / 7000</div>
<?php else : ?>
    <div class="points-display"><a href="connexion.php" style="color:white;">Connecte-toi pour collecter des cartes</a></div>
<?php endif; ?>

<div class="recherche">
    <input type="text" id="barre-recherche" placeholder="Rechercher un Pokémon...">
</div>

<div class="filtres" id="filtres">
    <button class="filtre-btn actif" data-type="tous">Tous</button>
    <button class="filtre-btn" data-type="fire">Feu 🔥</button>
    <button class="filtre-btn" data-type="water">Eau 💧</button>
    <button class="filtre-btn" data-type="grass">Plante 🌿</button>
    <button class="filtre-btn" data-type="electric">Électrique ⚡</button>
    <button class="filtre-btn" data-type="normal">Normal ⭐</button>
</div>

<p class="chargement" id="chargement">Chargement des Pokémon...</p>

<div class="grille" id="liste-catalogue"></div>

<script>
var tousLesPokemons = [];
var typeActuel = "tous";
var rechercheActuelle = "";
var utilisateur_id = <?= json_encode($utilisateur_id) ?>;
var userPoints = <?= json_encode($userPoints) ?>;

function getCout(id) {
    if (id % 25 === 0) return 15000;
    if (id % 10 === 0) return 8000;
    if (id % 5 === 0) return 3000;
    if (id % 3 === 0) return 2000;
    return 500;
}

function creerCarte(pokemon) {
    var typePrincipal = pokemon.types[0].type.name;
    var image = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/" + pokemon.id + ".png";
    var cout = getCout(pokemon.id);
    
    var btnHTML = "";
    if (utilisateur_id) {
        var disabled = userPoints < cout ? "disabled" : "";
        btnHTML = '<form method="POST" style="width:100%;" onclick="event.stopPropagation();">' +
            '<input type="hidden" name="pokemon_id" value="' + pokemon.id + '">' +
            '<input type="hidden" name="cout" value="' + cout + '">' +
            '<button type="submit" class="btn-collecter" ' + disabled + '>Collecter</button>' +
            '</form>';
    }
    
    return '<div class="carte-pokemon">' +
        '<a href="carte.php?id=' + pokemon.id + '" style="text-decoration:none; color:inherit; width:100%;">' +
        '<img src="' + image + '" alt="' + pokemon.name + '">' +
        '<h3>' + pokemon.name + '</h3>' +
        '<span class="type-badge type-' + typePrincipal + '">' + typePrincipal + '</span>' +
        '<div class="points-carte">💎 ' + cout + ' pts</div>' +
        '</a>' +
        btnHTML +
        '</div>';
}

function afficherPokemons() {
    var grille = document.getElementById("liste-catalogue");
    var filtres = tousLesPokemons.filter(function(p) {
        if (typeActuel === "tous") return true;
        return p.types[0].type.name === typeActuel;
    });
    filtres = filtres.filter(function(p) {
        return p.name.includes(rechercheActuelle.toLowerCase());
    });
    grille.innerHTML = filtres.map(creerCarte).join("");
    if (filtres.length === 0) {
        grille.innerHTML = '<p class="chargement">Aucun Pokémon trouvé 😢</p>';
    }
}

fetch("https://pokeapi.co/api/v2/pokemon?limit=50")
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var promises = data.results.map(function(p) {
            return fetch(p.url).then(function(r) { return r.json(); });
        });
        return Promise.all(promises);
    })
    .then(function(pokemons) {
        tousLesPokemons = pokemons;
        document.getElementById("chargement").style.display = "none";
        afficherPokemons();
    });

document.querySelectorAll(".filtre-btn").forEach(function(btn) {
    btn.addEventListener("click", function() {
        document.querySelectorAll(".filtre-btn").forEach(function(b) {
            b.classList.remove("actif");
        });
        btn.classList.add("actif");
        typeActuel = btn.dataset.type;
        afficherPokemons();
    });
});

document.getElementById("barre-recherche").addEventListener("input", function(e) {
    rechercheActuelle = e.target.value;
    afficherPokemons();
});
</script>

<?php require_once "includes/footer.php"; ?>