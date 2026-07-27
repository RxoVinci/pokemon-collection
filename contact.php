<?php require_once "includes/db.php"; ?>
<?php
$envoye = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $envoye = true;
}
?>
<?php require_once "includes/header.php"; ?>

<h1 class="titre-section">Besoin d'aide ?</h1>
<p style="text-align:center; color: var(--texte-gris); margin-bottom:30px;">
    Une question, un bug, une suggestion ? Envoie-nous un message.
</p>

<?php if ($envoye) : ?>
    <div class="succes" style="max-width:500px; margin:0 auto 20px;">Merci ! Ton message a bien été envoyé.</div>
<?php endif; ?>

<form class="formulaire" method="POST" action="contact.php">
    <label for="nom">Ton nom</label>
    <input type="text" name="nom" id="nom" placeholder="Sacha" required>

    <label for="email">Ton email</label>
    <input type="email" name="email" id="email" placeholder="sacha@pokemon.com" required>

    <label for="sujet">Sujet</label>
    <input type="text" name="sujet" id="sujet" placeholder="Bug, question, suggestion..." required>

    <label for="message">Ton message</label>
    <textarea name="message" id="message" rows="6" placeholder="Écris ton message ici..." required style="padding: 12px 15px; border: 2px solid var(--texte-gris); border-radius: 10px; background-color: var(--fond); color: var(--texte); font-family: 'Poppins', sans-serif; font-size: 15px; outline: none; resize: vertical;"></textarea>

    <button type="submit" class="btn">Envoyer</button>
</form>

<?php require_once "includes/footer.php"; ?>