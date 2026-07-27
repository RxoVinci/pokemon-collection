</main>

<footer>
    <div class="footer-contenu">
        <div class="footer-col">
            <h4>🔴 Pokémon Collection</h4>
            <p>Le site des collectionneurs de cartes Pokémon. Constitue ton stuff, échange, complète ton Pokédex.</p>
        </div>

        <div class="footer-col">
            <h4>Navigation</h4>
            <a href="index.php">Accueil</a>
            <a href="catalogue.php">Catalogue</a>
            <a href="contact.php">Contact</a>
        </div>

        <div class="footer-col">
            <h4>Newsletter</h4>
            <p>Reçois les nouveautés et les cartes exclusives dans ta boîte mail.</p>
            <form class="newsletter-form" onsubmit="event.preventDefault(); document.getElementById('newsletter-msg').style.display='block'; this.reset();">
                <input type="email" placeholder="ton@email.com" required>
                <button type="submit">S'inscrire</button>
            </form>
            <p id="newsletter-msg" style="display:none; color: var(--accent-2); margin-top: 10px; font-size: 13px;">Merci pour ton inscription !</p>
        </div>
    </div>

    <div class="footer-bas">
        <p>© 2026 Pokémon Collection - Xavier Okola - IIM Digital School</p>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>