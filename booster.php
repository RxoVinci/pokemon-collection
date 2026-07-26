<?php
require_once "includes/db.php";

if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: connexion.php");
    exit;
}

$utilisateur_id = $_SESSION["utilisateur_id"];
$peut_ouvrir = true;
$temps_restant = 0;

$req = $pdo->prepare("SELECT dernier_ouverture FROM boosters WHERE utilisateur_id = ? ORDER BY id DESC LIMIT 1");
$req->execute([$utilisateur_id]);
$dernier = $req->fetch();

if ($dernier) {
    $diff = time() - strtotime($dernier["dernier_ouverture"]);
    if ($diff < 86400) {
        $peut_ouvrir = false;
        $temps_restant = 86400 - $diff;
    }
}

$cartes_obtenues = [];
if ($peut_ouvrir && $_SERVER["REQUEST_METHOD"] == "POST") {
    for ($i = 0; $i < 5; $i++) {
        $id_alea = rand(1, 150);
        $cartes_obtenues[] = $id_alea;
        $ins = $pdo->prepare("INSERT INTO collections (utilisateur_id, pokemon_id) VALUES (?, ?)");
        $ins->execute([$utilisateur_id, $id_alea]);
    }
    $log = $pdo->prepare("INSERT INTO boosters (utilisateur_id, dernier_ouverture) VALUES (?, NOW())");
    $log->execute([$utilisateur_id]);
    $peut_ouvrir = false;
    $temps_restant = 86400;
}
?>
<?php require_once "includes/header.php"; ?>

<div class="booster-section">
    <h1>Ouvrir un booster</h1>

    <?php if (!empty($cartes_obtenues)) : ?>
        <h2>Tu as obtenu 5 nouvelles cartes !</h2>
        <div class="grille-cartes" id="cartes-nouvelles"></div>
        <script>
            var ids = <?= json_encode($cartes_obtenues) ?>;
            var grille = document.getElementById("cartes-nouvelles");
            ids.forEach(function(id) {
                fetch("https://pokeapi.co/api/v2/pokemon/" + id)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var c = document.createElement("div");
                        c.className = "carte-pokemon";
                        c.innerHTML =
                            '<img src="' + data.sprites.front_default + '" alt="' + data.name + '">' +
                            '<h3>' + data.name + '</h3>';
                        grille.appendChild(c);
                    });
            });
        </script>
    <?php elseif ($peut_ouvrir) : ?>
        <p>Tu peux ouvrir un booster ! 5 Pokémon aléatoires t'attendent.</p>
        <form method="POST" action="booster.php">
            <button type="submit" class="btn-booster">Ouvrir le booster</button>
        </form>
    <?php else : ?>
        <?php
        $heures = floor($temps_restant / 3600);
        $minutes = floor(($temps_restant % 3600) / 60);
        ?>
        <p>Tu as déjà ouvert un booster récemment.</p>
        <p>Reviens dans <strong><?= $heures ?>h <?= $minutes ?>min</strong>.</p>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>