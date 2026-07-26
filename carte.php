<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

<?php
$id = isset($_GET["id"]) ? intval($_GET["id"]) : 1;
?>

<div class="detail-carte" id="detail-carte">
    <p>Chargement...</p>
</div>

<script>
var idPokemon = <?= $id ?>;

fetch("https://pokeapi.co/api/v2/pokemon/" + idPokemon)
    .then(function(reponse) {
        return reponse.json();
    })
    .then(function(data) {
        var detail = document.getElementById("detail-carte");
        var types = data.types.map(function(t) {
            return t.type.name;
        }).join(", ");

        detail.innerHTML =
            '<img src="' + data.sprites.front_default + '" alt="' + data.name + '">' +
            '<h1>' + data.name + '</h1>' +
            '<p><strong>Type :</strong> ' + types + '</p>' +
            '<div class="stats">' +
                '<div class="stat"><strong>Taille</strong><br>' + (data.height / 10) + ' m</div>' +
                '<div class="stat"><strong>Poids</strong><br>' + (data.weight / 10) + ' kg</div>' +
            '</div>';
    });
</script>

<?php require_once "includes/footer.php"; ?>