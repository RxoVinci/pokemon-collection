var boutonTheme = document.getElementById("theme-btn");

if (localStorage.getItem("theme") === "clair") {
    document.body.classList.add("clair");
    boutonTheme.textContent = "🌙";
}

boutonTheme.addEventListener("click", function () {
    document.body.classList.toggle("clair");
    if (document.body.classList.contains("clair")) {
        boutonTheme.textContent = "🌙";
        localStorage.setItem("theme", "clair");
    } else {
        boutonTheme.textContent = "☀️";
        localStorage.setItem("theme", "sombre");
    }
});

var boutonHamburger = document.getElementById("hamburger");
var menu = document.getElementById("menu");
var boutonFermer = document.getElementById("fermer-menu");

if (boutonHamburger) {
    boutonHamburger.addEventListener("click", function () {
        menu.classList.add("ouvert");
    });
}

if (boutonFermer) {
    boutonFermer.addEventListener("click", function () {
        menu.classList.remove("ouvert");
    });
}

var champEmail = document.getElementById("email-connexion");

if (champEmail) {
    var emailSauve = localStorage.getItem("email_connexion");
    if (emailSauve) {
        champEmail.value = emailSauve;
    }
    champEmail.addEventListener("input", function () {
        localStorage.setItem("email_connexion", champEmail.value);
    });
}