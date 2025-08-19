// /js/dashboard/dashboard.js
document.addEventListener('DOMContentLoaded', function () {
    // Bienvenue personnalisée
    const urlParams = new URLSearchParams(window.location.search);
    const firstname = urlParams.get('firstname');
    const welcomeEl = document.getElementById('user-firstname');
    if (welcomeEl && firstname) {
        welcomeEl.textContent = firstname;
    }

    // Animation des cartes
    const featureCards = document.querySelectorAll('.feature-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.2 });
    featureCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    console.log('📊 Yggdrasil - Tableau de bord chargé');
});