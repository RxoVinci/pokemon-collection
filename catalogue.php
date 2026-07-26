<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

<h1 style="text-align:center; margin-bottom:30px;">Catalogue Pokémon</h1>

<div class="grille-cartes" id="grille-cartes">
    <p>Chargement des Pokémon...</p>
</div>

<script>
fetch("https://pokeapi.co/api/v2/pokemon?limit=50")
    .then(function(reponse) {
        return reponse.json();
    })
    .then(function(data) {
        var grille = document.getElementById("grille-cartes");
        grille.innerHTML = "";

        data.results.forEach(function(pokemon, index) {
            var id = index + 1;
            var carte = document.createElement("a");
            carte.className = "carte-pokemon";
            carte.href = "carte.php?id=" + id;
            carte.innerHTML =
                '<img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' + id + '.png" alt="' + pokemon.name + '">' +
                '<h3>' + pokemon.name + '</h3>' +
                '<span class="type">#' + id + '</span>';
            grille.appendChild(carte);
        });
    });
</script>

<?php require_once "includes/footer.php"; ?>