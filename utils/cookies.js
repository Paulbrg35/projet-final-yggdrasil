// /js/utils/cookies.js
export function initCookieBanner() {
    if (document.cookie.includes('yggdrasilConsent=true')) return;

    const banner = document.createElement('div');
    banner.className = 'cookie-banner';
    banner.innerHTML = `
        <span>Ce site utilise des cookies. <a href="/privacy.html">En savoir plus</a></span>
        <button class="btn accept-cookies">Accepter</button>
    `;
    document.body.appendChild(banner);

    banner.querySelector('.accept-cookies').addEventListener('click', () => {
        document.cookie = 'yggdrasilConsent=true; path=/; max-age=' + 365*24*60*60;
        banner.remove();
    });
}