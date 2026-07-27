<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

<section class="hero">
    <h1>Pokémon Collection</h1>
    <p>Collectionnez, échangez et complétez votre Pokédex</p>
    <a href="catalogue.php" class="btn">Découvrir le catalogue</a>
</section>

<section class="section-accueil">
    <h2 class="titre-section">Nos cartes populaires</h2>
    <p class="sous-titre-section">Les Pokémon les plus recherchés par les collectionneurs</p>
    <p class="chargement" id="chargement-populaires">Chargement des Pokémon...</p>
    <div class="grille" id="liste-populaires"></div>
</section>

<section class="section-mise-en-avant">
    <div class="mise-en-avant-contenu">
        <div class="mise-en-avant-texte">
            <span class="badge-nouveau">Carte Mythique</span>
            <h2>Charizard</h2>
            <p>La carte la plus recherchée par les collectionneurs. Un Pokémon de type Feu à la puissance légendaire, présent dans les rêves de tous les dresseurs depuis 1996.</p>
            <a href="carte.php?id=6" class="btn">Voir la carte</a>
        </div>
        <div class="mise-en-avant-image">
            <a href="carte.php?id=6">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png" alt="Charizard">
            </a>
        </div>
    </div>
</section>

<section class="section-accueil">
    <h2 class="titre-section">Cartes Mythiques & Rares</h2>
    <p class="sous-titre-section">Les Pokémon légendaires à collectionner absolument</p>
    <p class="chargement" id="chargement-rares">Chargement...</p>
    <div class="grille" id="liste-rares"></div>
</section>

<section class="section-avantages">
    <div class="avantage">
        <div class="avantage-icone">🎁</div>
        <h3>Points quotidiens</h3>
        <p>Reçois 7000 points chaque jour pour collectionner tes cartes préférées.</p>
    </div>
    <div class="avantage">
        <div class="avantage-icone">⚡</div>
        <h3>Cartes exclusives</h3>
        <p>Découvre plus de 50 Pokémon avec leurs stats, types et détails complets.</p>
    </div>
    <div class="avantage">
        <div class="avantage-icone">🏆</div>
        <h3>Complète ton Pokédex</h3>
        <p>Retrouve toute ta collection dans ton stuff personnel.</p>
    </div>
</section>

<script>
var traductionTypes = {
    "fire": "Feu", "water": "Eau", "grass": "Plante", "electric": "Électrique",
    "ice": "Glace", "fighting": "Combat", "poison": "Poison", "ground": "Sol",
    "flying": "Vol", "psychic": "Psy", "bug": "Insecte", "rock": "Roche",
    "ghost": "Spectre", "dragon": "Dragon", "dark": "Ténèbres", "steel": "Acier",
    "fairy": "Fée", "normal": "Normal"
};

fetch("https://pokeapi.co/api/v2/pokemon?limit=8")
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var promises = data.results.map(function(p, i) {
            return fetch("https://pokeapi.co/api/v2/pokemon/" + (i + 1))
                .then(function(r) { return r.json(); });
        });
        return Promise.all(promises);
    })
    .then(function(pokemons) {
        document.getElementById("chargement-populaires").style.display = "none";
        var grille = document.getElementById("liste-populaires");
        pokemons.forEach(function(pokemon) {
            var image = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/" + pokemon.id + ".png";
            var typePrincipal = pokemon.types[0].type.name;
            var typeFR = traductionTypes[typePrincipal] || typePrincipal;
            var carte = document.createElement("a");
            carte.className = "carte-pokemon";
            carte.href = "carte.php?id=" + pokemon.id;
            carte.innerHTML =
                '<img src="' + image + '" alt="' + pokemon.name + '">' +
                '<h3>' + pokemon.name + '</h3>' +
                '<span class="type-badge type-' + typePrincipal + '">' + typeFR + '</span>';
            grille.appendChild(carte);
        });
    });

var idsRares = [3, 9, 65, 68, 94, 130, 143, 149];
Promise.all(idsRares.map(function(id) {
    return fetch("https://pokeapi.co/api/v2/pokemon/" + id).then(function(r) { return r.json(); });
})).then(function(pokemons) {
    document.getElementById("chargement-rares").style.display = "none";
    var grille = document.getElementById("liste-rares");
    pokemons.forEach(function(pokemon) {
        var image = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/" + pokemon.id + ".png";
        var typePrincipal = pokemon.types[0].type.name;
        var typeFR = traductionTypes[typePrincipal] || typePrincipal;
        var carte = document.createElement("a");
        carte.className = "carte-pokemon carte-rare";
        carte.href = "carte.php?id=" + pokemon.id;
        carte.innerHTML =
            '<span class="badge-rare">Rare</span>' +
            '<img src="' + image + '" alt="' + pokemon.name + '">' +
            '<h3>' + pokemon.name + '</h3>' +
            '<span class="type-badge type-' + typePrincipal + '">' + typeFR + '</span>';
        grille.appendChild(carte);
    });
});
</script>

<?php require_once "includes/footer.php"; ?>