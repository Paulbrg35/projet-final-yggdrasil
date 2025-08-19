// /js/utils/accessibility.js
export function initAccessibility() {
    // Focus visible
    let hadKeyboard = true;
    document.addEventListener('mousedown', () => hadKeyboard = false, true);
    document.addEventListener('keydown', (e) => {
        if (e.metaKey || e.altKey || e.ctrlKey) return;
        hadKeyboard = true;
    }, true);
    document.addEventListener('focus', (e) => {
        if (hadKeyboard) e.target.classList.add('focus-visible');
    }, true);
    document.addEventListener('blur', (e) => {
        e.target.classList.remove('focus-visible');
    }, true);

    // Raccourcis clavier
    document.addEventListener('keydown', (e) => {
        if (e.altKey && e.key === '1') {
            e.preventDefault();
            document.querySelector('main')?.focus();
        }
        if (e.altKey && e.key === '2') {
            e.preventDefault();
            document.querySelector('.navbar')?.focus();
        }
        if (e.key === 'Escape') {
            const nav = document.querySelector('.nav-links');
            if (nav?.classList.contains('active')) {
                nav.classList.remove('active');
                document.querySelector('.mobile-menu-toggle')?.focus();
            }
        }
    });

    // Easter Egg
    let konami = [];
    const sequence = ['ArrowUp','ArrowUp','ArrowDown','ArrowDown','ArrowLeft','ArrowRight','ArrowLeft','ArrowRight','KeyB','KeyA'];
    document.addEventListener('keydown', (e) => {
        konami.push(e.code);
        if (konami.length > sequence.length) konami.shift();
        if (konami.join('') === sequence.join('')) {
            document.body.style.backgroundColor = '#D4AF37';
            setTimeout(() => document.body.style.backgroundColor = '', 1000);
            konami = [];
        }
    });
}