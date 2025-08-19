// /js/auth/register.js
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm');
    const confirmError = document.getElementById('confirm-error');
    const requirements = document.querySelectorAll('.password-requirements li');

    const checks = {
        length: false,
        upper: false,
        lower: false,
        digit: false,
        special: false
    };

    function validatePassword() {
        const pwd = passwordInput.value;
        checks.length = pwd.length >= 8;
        checks.upper = /[A-Z]/.test(pwd);
        checks.lower = /[a-z]/.test(pwd);
        checks.digit = /\d/.test(pwd);
        checks.special = /[!@#$%^&*]/.test(pwd);

        requirements.forEach((li, i) => {
            const valid = Object.values(checks)[i];
            li.classList.toggle('valid', valid);
        });
    }

    function validateConfirm() {
        if (confirmInput.value && confirmInput.value !== passwordInput.value) {
            confirmError.style.display = 'block';
        } else {
            confirmError.style.display = 'none';
        }
    }

    passwordInput.addEventListener('input', validatePassword);
    confirmInput.addEventListener('input', validateConfirm);

    // Bouton "Afficher le mot de passe"
    const toggleBtn = document.getElementById('toggle-password');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            toggleBtn.textContent = type === 'password' ? '👁️' : '🙈';
        });
    }

    console.log('🔐 Yggdrasil - Inscription chargée');
});