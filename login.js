document.addEventListener("DOMContentLoaded", () => {
    const passwordInput = document.getElementById("password");
    const toggleBtn = document.getElementById("togglePasswordBtn");

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener("click", function () {
            const icon = this.querySelector("i");
            const isHidden = passwordInput.type === "password";

            // Toggle input type
            passwordInput.type = isHidden ? "text" : "password";

            // Toggle icon classes
            icon.classList.toggle("fa-eye", !isHidden);
            icon.classList.toggle("fa-eye-slash", isHidden);

            // Update ARIA attributes for accessibility
            this.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
            this.setAttribute("aria-pressed", isHidden);
        });
    }
});