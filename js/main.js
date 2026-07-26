var hamburger = document.getElementById("hamburger");
var menu = document.getElementById("menu");

if (hamburger) {
    hamburger.addEventListener("click", function () {
        menu.classList.toggle("ouvert");
    });
}

var themeBtn = document.getElementById("theme-btn");
var body = document.body;

if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark");
    if (themeBtn) themeBtn.textContent = "☀️";
}

if (themeBtn) {
    themeBtn.addEventListener("click", function () {
        body.classList.toggle("dark");
        if (body.classList.contains("dark")) {
            themeBtn.textContent = "☀️";
            localStorage.setItem("theme", "dark");
        } else {
            themeBtn.textContent = "🌙";
            localStorage.setItem("theme", "light");
        }
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