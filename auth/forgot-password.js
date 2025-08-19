// /js/auth/forgot-password.js
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const errorDiv = document.getElementById('reset-error');
    const successDiv = document.getElementById('reset-success');

    // Réinitialiser les messages au chargement
    if (errorDiv) errorDiv.style.display = 'none';
    if (successDiv) successDiv.style.display = 'none';

    // Fonction pour annoncer aux lecteurs d'écran
    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        document.body.appendChild(announcement);
        setTimeout(() => document.body.removeChild(announcement), 1000);
    }

    // Validation du formulaire
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Réinitialiser les messages
            if (errorDiv) errorDiv.style.display = 'none';
            if (successDiv) successDiv.style.display = 'none';

            const email = emailInput.value.trim();
            let isValid = true;
            let errorMessage = '';

            // Validation de l'email
            if (!email) {
                errorMessage = 'Veuillez entrer une adresse email.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorMessage = 'Veuillez entrer une adresse email valide.';
            }

            if (!errorMessage) {
                // ✅ Simuler l'envoi (dans la vraie vie, appel à reset-password.php)
                if (successDiv) {
                    successDiv.textContent = `Un lien de réinitialisation a été envoyé à ${email}.`;
                    successDiv.style.display = 'block';
                }
                emailInput.value = ''; // Réinitialiser le champ
                announceToScreenReader(`Email de récupération envoyé à ${email}`);
            } else {
                if (errorDiv) {
                    errorDiv.textContent = errorMessage;
                    errorDiv.style.display = 'block';
                }
                emailInput.focus();
                announceToScreenReader(errorMessage);
            }
        });
    }

    // Effacer les messages si l'utilisateur commence à taper
    if (emailInput) {
        emailInput.addEventListener('input', function () {
            if (errorDiv) errorDiv.style.display = 'none';
            if (successDiv) successDiv.style.display = 'none';
        });
    }

    console.log('📬 Yggdrasil - Page de mot de passe oublié chargée');
});