(function () {
    var KEY = "museum-theme";

    function current() {
        try {
            return localStorage.getItem(KEY) === "dark" ? "dark" : "light";
        } catch (e) {
            return "light";
        }
    }

    function apply(theme) {
        var isDark = theme === "dark";
        document.documentElement.classList.toggle("dark", isDark);
        if (document.body) {
            document.body.classList.toggle("dark", isDark);
        }
        document.querySelectorAll("[data-theme-icon]").forEach(function (icon) {
            icon.className = isDark ? "bi bi-sun-fill" : "bi bi-moon-stars-fill";
        });
    }

    apply(current());

    window.toggleTheme = function () {
        var next = current() === "dark" ? "light" : "dark";
        try {
            localStorage.setItem(KEY, next);
        } catch (e) {}
        apply(next);
    };

    document.addEventListener("DOMContentLoaded", function () {
        apply(current());
    });
})();
