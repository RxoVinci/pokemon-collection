<?php require_once "includes/db.php"; ?>
<?php
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

    $deja = $pdo->prepare("SELECT id FROM collections WHERE utilisateur_id = ? AND pokemon_id = ?");
    $deja->execute([$utilisateur_id, $pokemon_id]);

    if ($deja->fetch()) {
        $message = "Tu as déjà cette carte dans ton stuff.";
    } elseif ($userPoints < $cout) {
        $message = "Pas assez de points pour cette carte.";
    } else {
        $ins = $pdo->prepare("INSERT INTO collections (utilisateur_id, pokemon_id) VALUES (?, ?)");
        $ins->execute([$utilisateur_id, $pokemon_id]);
        $upd = $pdo->prepare("UPDATE utilisateurs SET points = points - ? WHERE id = ?");
        $upd->execute([$cout, $utilisateur_id]);
        $userPoints -= $cout;
        $message = "Carte ajoutée à ton stuff !";
    }
}

$cartesPossedees = [];
if ($utilisateur_id) {
    $reqPoss = $pdo->prepare("SELECT pokemon_id FROM collections WHERE utilisateur_id = ?");
    $reqPoss->execute([$utilisateur_id]);
    foreach ($reqPoss->fetchAll() as $row) {
        $cartesPossedees[] = intval($row['pokemon_id']);
    }
}
?>
<?php require_once "includes/header.php"; ?>

<?php if ($utilisateur_id) : ?>
    <div class="points-mini">Points : <?= $userPoints ?> / 7000</div>
<?php endif; ?>

<h1 class="titre-section">Le catalogue</h1>

<?php if ($message) : ?>
    <div class="<?= strpos($message, 'ajoutée') !== false ? 'succes' : 'erreur' ?>" style="max-width:500px;margin:0 auto 20px;"><?= $message ?></div>
<?php endif; ?>

<div class="recherche">
    <input type="text" id="barre-recherche" placeholder="Rechercher un Pokémon...">
</div>

<div class="filtres" id="filtres">
    <button class="filtre-btn actif" data-type="tous">Tous</button>
    <button class="filtre-btn" data-type="fire">Feu</button>
    <button class="filtre-btn" data-type="water">Eau</button>
    <button class="filtre-btn" data-type="grass">Plante</button>
    <button class="filtre-btn" data-type="electric">Électrique</button>
    <button class="filtre-btn" data-type="normal">Normal</button>
    <button class="filtre-btn" data-type="poison">Poison</button>
    <button class="filtre-btn" data-type="flying">Vol</button>
</div>

<p class="chargement" id="chargement">Chargement des Pokémon...</p>

<div class="grille" id="liste-catalogue"></div>

<script>
var traductionTypes = {
    "fire": "Feu", "water": "Eau", "grass": "Plante", "electric": "Électrique",
    "ice": "Glace", "fighting": "Combat", "poison": "Poison", "ground": "Sol",
    "flying": "Vol", "psychic": "Psy", "bug": "Insecte", "rock": "Roche",
    "ghost": "Spectre", "dragon": "Dragon", "dark": "Ténèbres", "steel": "Acier",
    "fairy": "Fée", "normal": "Normal"
};

var tousLesPokemons = [];
var typeActuel = "tous";
var rechercheActuelle = "";
var utilisateur_id = <?= json_encode($utilisateur_id) ?>;
var userPoints = <?= json_encode($userPoints) ?>;
var cartesPossedees = <?= json_encode($cartesPossedees) ?>;

function getCout(id) {
    if (id === 6 || id === 9 || id === 3 || id === 149) return 15000;
    if (id === 130 || id === 131 || id === 143) return 12000;
    if (id === 65 || id === 68 || id === 94) return 8000;
    if (id === 25 || id === 26 || id === 59) return 5000;
    if (id % 7 === 0) return 3500;
    if (id % 5 === 0) return 3000;
    if (id % 3 === 0) return 2000;
    if (id % 2 === 0) return 1500;
    return 800;
}

function creerCarte(pokemon) {
    var typePrincipal = pokemon.types[0].type.name;
    var typeFR = traductionTypes[typePrincipal] || typePrincipal;
    var image = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/" + pokemon.id + ".png";
    var cout = getCout(pokemon.id);
    var possede = cartesPossedees.indexOf(pokemon.id) !== -1;

    var btnHTML = "";
    if (utilisateur_id) {
        if (possede) {
            btnHTML = '<button class="btn-collecter" disabled>Déjà possédée</button>';
        } else if (userPoints < cout) {
            btnHTML = '<button class="btn-collecter" disabled>Points insuffisants</button>';
        } else {
            btnHTML = '<form method="POST" style="width:100%;" onclick="event.stopPropagation();">' +
                '<input type="hidden" name="pokemon_id" value="' + pokemon.id + '">' +
                '<input type="hidden" name="cout" value="' + cout + '">' +
                '<button type="submit" class="btn-collecter">Collecter</button>' +
                '</form>';
        }
    }

    return '<div class="carte-pokemon">' +
        '<a href="carte.php?id=' + pokemon.id + '" style="text-decoration:none; color:inherit; width:100%;">' +
        '<img src="' + image + '" alt="' + pokemon.name + '">' +
        '<h3>' + pokemon.name + '</h3>' +
        '<span class="type-badge type-' + typePrincipal + '">' + typeFR + '</span>' +
        '<div class="points-carte">' + cout + ' points</div>' +
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
        grille.innerHTML = '<p class="chargement">Aucun Pokémon trouvé.</p>';
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