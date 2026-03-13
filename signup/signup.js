document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signupForm");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const errorMsg = document.getElementById("password-error");
    const registerBtn = document.querySelector(".register-btn");

    // FOR STUDENT ID 
    const studentId = document.getElementById("studentid");
    const studentWarning = document.getElementById("studentid-warning");

    if (studentId) {
        studentId.addEventListener("input", () => {

            studentId.value = studentId.value.toUpperCase();

            const pattern = /^A[0-9]{9}$/;

            if (studentId.value === "") {
                studentWarning.style.display = "none";
                return;
            }

            if (!pattern.test(studentId.value)) {
                studentWarning.style.display = "block";
            } else {
                studentWarning.style.display = "none";
            }

        });
    }
    // FORCE CAPITAL LETTER INPUT
    const uppercaseFields = [
        document.getElementById("username"),
        document.getElementById("fullname"),
        document.getElementById("studentid")
    ];

    uppercaseFields.forEach(field => {
        if (field) {
            field.addEventListener("input", () => {
                field.value = field.value.toUpperCase();
            });
        }
    });

    // PASSWORD REQUIREMENTS TOGGLE
    const toggleReq = document.getElementById("toggleRequirements");
    const requirementsBox = document.getElementById("requirementsBox");
    const closeReq = document.getElementById("closeRequirements");

    if (toggleReq && requirementsBox) {
        toggleReq.addEventListener("click", () => {
            requirementsBox.classList.toggle("show");
        });
    }

    if (closeReq && requirementsBox) {
        closeReq.addEventListener("click", () => {
            requirementsBox.classList.remove("show");
        });
    }

    // PASSWORD RULE ELEMENTS
    const lengthRule = document.getElementById("rule-length");
    const upperRule = document.getElementById("rule-upper");
    const lowerRule = document.getElementById("rule-lower");
    const numberRule = document.getElementById("rule-number");
    const specialRule = document.getElementById("rule-special");

    // --- SEQUENTIAL FILLING LOGIC ---
    const steps = [
        document.getElementById("username"),
        document.getElementById("email"),
        document.getElementById("fullname"),
        document.getElementById("studentid"),
        document.getElementById("password"),
        document.getElementById("confirmPassword")
    ].filter(Boolean);

    function isFilled(el) {
        return el.value.trim() !== "";
    }

    function blockIfPreviousNotFilled(index) {
        for (let i = 0; i < index; i++) {
            if (!isFilled(steps[i])) {
                steps[i].focus();
                steps[i].style.border = "2px solid red";
                setTimeout(() => (steps[i].style.border = ""), 1200);
                return true;
            }
        }
        return false;
    }

    steps.forEach((field, index) => {
        field.addEventListener("focus", (e) => {
            if (blockIfPreviousNotFilled(index)) {
                e.preventDefault();
                setTimeout(() => field.blur(), 0);
            }
        });

        field.addEventListener("mousedown", (e) => {
            if (blockIfPreviousNotFilled(index)) e.preventDefault();
        });
    });

    // PASSWORD VALIDATION FUNCTION
    function isPasswordValid() {
        const value = password.value;

        return (
            value.length >= 8 &&
            /[A-Z]/.test(value) &&
            /[a-z]/.test(value) &&
            /[0-9]/.test(value) &&
            /[@$!%*?&]/.test(value)
        );
    }

    // PASSWORD MATCH CHECK
    function checkPasswordMatch() {
        if (!isPasswordValid() && password.value.length > 0) {
            errorMsg.textContent = "Password does not meet the requirements.";
            errorMsg.style.display = "block";
            registerBtn.disabled = true;
            return;
        }

        if (confirmPassword.value === "") {
            errorMsg.style.display = "none";
            registerBtn.disabled = false;
            return;
        }

        if (password.value !== confirmPassword.value) {
            errorMsg.textContent = "Passwords do not match.";
            errorMsg.style.display = "block";
            registerBtn.disabled = true;
        } else {
            errorMsg.style.display = "none";
            registerBtn.disabled = false;
        }
    }

    // BLOCK ACCESS TO CONFIRM PASSWORD
    if (confirmPassword) {
        confirmPassword.addEventListener("focus", (e) => {
            if (!isPasswordValid()) {
                e.preventDefault();
                password.focus();
                errorMsg.textContent = "Password does not meet the requirements.";
                errorMsg.style.display = "block";
            }
        });

        confirmPassword.addEventListener("input", checkPasswordMatch);
    }

    // PASSWORD REQUIREMENT CHECK
    if (password) {
        password.addEventListener("input", () => {
            const value = password.value;

            if (value.length >= 8) {
                lengthRule.classList.add("valid");
                lengthRule.textContent = "✔ At least 8 characters";
            } else {
                lengthRule.classList.remove("valid");
                lengthRule.textContent = "✖ At least 8 characters";
            }

            if (/[A-Z]/.test(value)) {
                upperRule.classList.add("valid");
                upperRule.textContent = "✔ One uppercase letter";
            } else {
                upperRule.classList.remove("valid");
                upperRule.textContent = "✖ One uppercase letter";
            }

            if (/[a-z]/.test(value)) {
                lowerRule.classList.add("valid");
                lowerRule.textContent = "✔ One lowercase letter";
            } else {
                lowerRule.classList.remove("valid");
                lowerRule.textContent = "✖ One lowercase letter";
            }

            if (/[0-9]/.test(value)) {
                numberRule.classList.add("valid");
                numberRule.textContent = "✔ One number";
            } else {
                numberRule.classList.remove("valid");
                numberRule.textContent = "✖ One number";
            }

            if (/[@$!%*?&]/.test(value)) {
                specialRule.classList.add("valid");
                specialRule.textContent = "✔ One special character (@$!%*?&)";
            } else {
                specialRule.classList.remove("valid");
                specialRule.textContent = "✖ One special character (@$!%*?&)";
            }

            checkPasswordMatch();
        });

        password.addEventListener("input", checkPasswordMatch);
    }

    // FINAL SUBMIT CHECK
    if (form) {
        form.addEventListener("submit", (e) => {
            if (!isPasswordValid()) {
                e.preventDefault();
                errorMsg.textContent = "Password does not meet the requirements.";
                errorMsg.style.display = "block";
                password.focus();
                return;
            }

            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                errorMsg.textContent = "Passwords do not match.";
                errorMsg.style.display = "block";
                confirmPassword.focus();
            }
        });
    }

    // SHOW / HIDE PASSWORD
    const showPasswordToggle = document.getElementById("showPassword");

    if (showPasswordToggle && password && confirmPassword) {
        showPasswordToggle.addEventListener("change", () => {
            const type = showPasswordToggle.checked ? "text" : "password";
            password.type = type;
            confirmPassword.type = type;
        });
    }
});