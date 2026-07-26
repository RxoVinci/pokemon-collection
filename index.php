<?php require_once "includes/db.php"; ?>
<?php require_once "includes/header.php"; ?>

<section class="hero">
    <h1>Bienvenue sur Pokémon Collection</h1>
    <p>Ouvre des boosters, collectionne tes Pokémon préférés et complète ton Pokédex.</p>
</section>

<section class="booster-section">
    <?php if (isset($_SESSION['utilisateur_id'])) : ?>
        <a href="booster.php" class="btn-booster">🎁 Ouvrir un booster</a>
    <?php else : ?>
        <p>Connecte-toi pour ouvrir ton premier booster.</p>
        <a href="connexion.php" class="btn-booster">Se connecter</a>
    <?php endif; ?>
</section>

<?php require_once "includes/footer.php"; ?>