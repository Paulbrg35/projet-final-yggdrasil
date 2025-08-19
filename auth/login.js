// /js/auth/login.js
document.addEventListener('DOMContentLoaded', function () {
    // Réinitialisation du mot de passe
    const forgotLink = document.querySelector('a[href="forgot-password.php"]');
    if (forgotLink) {
        forgotLink.addEventListener('click', () => {
            console.log('Redirection vers récupération de mot de passe');
        });
    }

    // Gestion du "Se souvenir de moi"
    const rememberCheckbox = document.querySelector('input[name="remember"]');
    if (rememberCheckbox) {
        rememberCheckbox.addEventListener('change', function () {
            if (this.checked) {
                console.log('Utilisateur souhaite être mémorisé');
            }
        });
    }

    console.log('🔑 Yggdrasil - Connexion chargée');
});