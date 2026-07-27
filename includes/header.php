<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon Collection</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <a href="index.php" class="logo">🔴 Pokémon <span>Collection</span></a>

    <button class="hamburger" id="hamburger">☰</button>

    <nav id="menu">
        <button class="fermer-menu" id="fermer-menu">✖</button>
        <a href="index.php">Accueil</a>
        <a href="catalogue.php">Catalogue</a>
        <?php if (isset($_SESSION['utilisateur_id'])) : ?>
            <a href="profil.php">👤 Mon Profil</a>
            <a href="deconnexion.php">Déconnexion</a>
        <?php else : ?>
            <a href="connexion.php">Connexion</a>
            <a href="inscription.php">Inscription</a>
        <?php endif; ?>
    </nav>

    <button class="theme-btn" id="theme-btn">☀️</button>
</header>

<main>