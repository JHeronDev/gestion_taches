document.addEventListener("DOMContentLoaded", () => {
    const bouton = document.getElementById("darkmode");
    const body = document.body;

    if (localStorage.getItem("darkMode") === "enabled") {
        body.classList.add("dark");
    }

    bouton.addEventListener("click", () => {
        body.classList.toggle("dark");

        if (body.classList.contains("dark")) {
            localStorage.setItem("darkMode", "enabled");
        } else {
            localStorage.setItem("darkMode", "disabled");
        }
    });
});
