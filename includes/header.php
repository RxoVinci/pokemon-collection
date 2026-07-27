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
        <a href="contact.php">Contact</a>
        <?php if (!isset($_SESSION['utilisateur_id'])) : ?>
            <a href="connexion.php">Connexion</a>
        <?php endif; ?>
    </nav>

    <div class="header-droite">
        <button class="theme-btn" id="theme-btn">☀️</button>

        <?php if (isset($_SESSION['utilisateur_id'])) : ?>
            <div class="profil-menu" id="profil-menu">
                <button class="profil-btn" id="profil-btn">👤</button>
                <div class="profil-dropdown" id="profil-dropdown">
                    <a href="profil.php">Mon stuff</a>
                    <a href="contact.php">Aide</a>
                    <a href="deconnexion.php" class="deconnexion-lien">Déconnexion</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>

<main>