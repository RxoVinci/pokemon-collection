<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

<section class="hero">
    <h1>Pokémon Collection</h1>
    <p>Collectionnez, échangez et complétez votre Pokédex</p>
    <a href="catalogue.php" class="btn">Voir le catalogue</a>
</section>

<h2 class="titre-section">Nos cartes populaires</h2>

<p class="chargement" id="chargement">Chargement des Pokémon...</p>

<div class="grille" id="liste-pokemons"></div>

<script>
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
        document.getElementById("chargement").style.display = "none";
        var grille = document.getElementById("liste-pokemons");
        pokemons.forEach(function(pokemon) {
            var image = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/" + pokemon.id + ".png";
            var typePrincipal = pokemon.types[0].type.name;
            var carte = document.createElement("a");
            carte.className = "carte-pokemon";
            carte.href = "carte.php?id=" + pokemon.id;
            carte.innerHTML =
                '<img src="' + image + '" alt="' + pokemon.name + '">' +
                '<h3>' + pokemon.name + '</h3>' +
                '<span class="type-badge type-' + typePrincipal + '">' + typePrincipal + '</span>';
            grille.appendChild(carte);
        });
    });
</script>

<?php require_once "includes/footer.php"; ?>