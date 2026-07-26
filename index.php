<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

<section class="hero">
    <h1>Pokémon Collection</h1>
    <p>Ouvre des boosters, collectionne tes Pokémon préférés et complète ton Pokédex.</p>
    <a href="catalogue.php" class="btn-hero">Voir le catalogue</a>
</section>

<h2 class="section-titre">Nos Cartes Populaires</h2>

<div class="grille-cartes" id="cartes-populaires">
    <p>Chargement...</p>
</div>

<script>
fetch("https://pokeapi.co/api/v2/pokemon?limit=8")
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var grille = document.getElementById("cartes-populaires");
        grille.innerHTML = "";

        data.results.forEach(function(pokemon, index) {
            var id = index + 1;
            var carte = document.createElement("a");
            carte.className = "carte-pokemon";
            carte.href = "carte.php?id=" + id;
            carte.innerHTML =
                '<img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/' + id + '.png" alt="' + pokemon.name + '">' +
                '<h3>' + pokemon.name + '</h3>' +
                '<span class="type">#' + id + '</span>';
            grille.appendChild(carte);
        });
    });
</script>

<?php require_once "includes/footer.php"; ?>