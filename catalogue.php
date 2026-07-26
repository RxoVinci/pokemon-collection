<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

<h1 class="titre-page">Catalogue Pokémon</h1>

<div style="text-align: center; margin-bottom: 30px;">
    <button class="btn-filtre" data-type="all">Tous</button>
    <button class="btn-filtre" data-type="fire">🔥 Feu</button>
    <button class="btn-filtre" data-type="water">💧 Eau</button>
    <button class="btn-filtre" data-type="grass">🌿 Plante</button>
    <button class="btn-filtre" data-type="electric">⚡ Électrique</button>
    <button class="btn-filtre" data-type="ice">❄️ Glace</button>
    <button class="btn-filtre" data-type="fighting">👊 Combat</button>
    <button class="btn-filtre" data-type="poison">☠️ Poison</button>
    <button class="btn-filtre" data-type="ground">⛰️ Sol</button>
    <button class="btn-filtre" data-type="flying">🦅 Vol</button>
</div>

<div class="grille-cartes" id="grille-cartes">
    <p>Chargement des Pokémon...</p>
</div>

<script>
var typeFilter = "all";
var allPokemon = [];

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
        var carte = document.createElement("a");
        carte.className = "carte-pokemon";
        carte.href = "carte.php?id=" + pokemon.id;
        var emoji = getTypeEmoji(pokemon.types[0].type.name);
        carte.innerHTML =
            '<img src="' + pokemon.sprites.front_default + '" alt="' + pokemon.name + '">' +
            '<h3>' + pokemon.name + '</h3>' +
            '<span class="type">' + emoji + ' ' + pokemon.types[0].type.name + '</span>';
        grille.appendChild(carte);
    });
}

function getTypeEmoji(type) {
    var emojis = {
        "fire": "🔥", "water": "💧", "grass": "🌿", "electric": "⚡",
        "ice": "❄️", "fighting": "👊", "poison": "☠️", "ground": "⛰️",
        "flying": "🦅", "psychic": "🧠", "bug": "🐛", "rock": "🪨",
        "ghost": "👻", "dragon": "🐉", "dark": "🌑", "steel": "⚙️", "fairy": "✨"
    };
    return emojis[type] || "•";
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