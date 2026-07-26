<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

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

if ($utilisateur_id && $_SERVER["REQUEST_METHOD"] == "POST") {
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

<h1 class="titre-page">Catalogue Pokémon</h1>

<?php if ($utilisateur_id) : ?>
    <div class="points-display">
        💎 Vos points : <?= $userPoints ?> / 7000
    </div>
<?php endif; ?>

<div style="text-align: center; margin-bottom: 30px;">
    <button class="btn-filtre" data-type="all">Tous</button>
    <button class="btn-filtre" data-type="fire">🔥 Feu</button>
    <button class="btn-filtre" data-type="water">💧 Eau</button>
    <button class="btn-filtre" data-type="grass">🌿 Plante</button>
    <button class="btn-filtre" data-type="electric">⚡ Électrique</button>
    <button class="btn-filtre" data-type="ice">❄️ Glace</button>
    <button class="btn-filtre" data-type="fighting">👊 Combat</button>
    <button class="btn-filtre" data-type="poison">☠️ Poison</button>
</div>

<div class="grille-cartes" id="grille-cartes">
    <p>Chargement des Pokémon...</p>
</div>

<script>
var typeFilter = "all";
var allPokemon = [];
var utilisateur_id = <?= json_encode($utilisateur_id) ?>;
var userPoints = <?= json_encode($userPoints) ?>;

var couts = {
    1: 500, 2: 500, 3: 2000, 4: 2000, 5: 3000, 6: 8000, 7: 500, 8: 2000, 9: 2000,
    10: 500, 11: 2000, 12: 2000, 13: 500, 14: 500, 15: 2000, 16: 500, 17: 2000, 18: 3000,
    19: 500, 20: 2000, 21: 500, 22: 2000, 23: 1000, 24: 2000, 25: 2000, 26: 3000
};

function getCout(id) {
    return couts[id] || (Math.random() < 0.5 ? 500 : (Math.random() < 0.7 ? 2000 : 8000));
}

function getTypeEmoji(type) {
    var emojis = {
        "fire": "🔥", "water": "💧", "grass": "🌿", "electric": "⚡",
        "ice": "❄️", "fighting": "👊", "poison": "☠️", "ground": "⛰️",
        "flying": "🦅", "psychic": "🧠", "bug": "🐛", "rock": "🪨"
    };
    return emojis[type] || "•";
}

function displayPokemon() {
    var grille = document.getElementById("grille-cartes");
    grille.innerHTML = "";

    var filtered = allPokemon.filter(function(pokemon) {
        if (typeFilter === "all") return true;
        return pokemon.types && pokemon.types.some(function(t) {
            return t.type.name === typeFilter;
        });
    });

    filtered.forEach(function(pokemon) {
        var cout = getCout(pokemon.id);
        var carte = document.createElement("div");
        carte.className = "carte-pokemon";
        var emoji = getTypeEmoji(pokemon.types[0].type.name);
        
        var btnHTML = "";
        if (utilisateur_id) {
            var disabled = userPoints < cout;
            btnHTML = '<form method="POST" style="margin-top: 8px;">' +
                '<input type="hidden" name="pokemon_id" value="' + pokemon.id + '">' +
                '<input type="hidden" name="cout" value="' + cout + '">' +
                '<button type="submit" class="btn-collecter" ' + (disabled ? 'disabled' : '') + '>Collecter</button>' +
                '</form>';
        } else {
            btnHTML = '<p style="margin-top: 8px; color: #999;"><a href="connexion.php">Se connecter</a></p>';
        }
        
        carte.innerHTML =
            '<a href="carte.php?id=' + pokemon.id + '" style="text-decoration: none; color: inherit;">' +
            '<img src="' + pokemon.sprites.front_default + '" alt="' + pokemon.name + '">' +
            '<h3>' + pokemon.name + '</h3>' +
            '<span class="type">' + emoji + '</span>' +
            '</a>' +
            '<span class="points">💎 ' + cout + ' pts</span>' +
            btnHTML;
        grille.appendChild(carte);
    });
}

fetch("https://pokeapi.co/api/v2/pokemon?limit=50")
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var promises = data.results.map(function(p, i) {
            return fetch("https://pokeapi.co/api/v2/pokemon/" + (i + 1))
                .then(function(r) { return r.json(); });
        });
        return Promise.all(promises);
    })
    .then(function(pokemons) {
        allPokemon = pokemons;
        displayPokemon();
    });

document.querySelectorAll(".btn-filtre").forEach(function(btn) {
    btn.addEventListener("click", function() {
        document.querySelectorAll(".btn-filtre").forEach(function(b) {
            b.style.opacity = "0.6";
        });
        this.style.opacity = "1";
        typeFilter = this.getAttribute("data-type");
        displayPokemon();
    });
});
</script>

<?php require_once "includes/footer.php"; ?>