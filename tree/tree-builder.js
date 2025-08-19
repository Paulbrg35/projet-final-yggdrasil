// /js/tree/tree-builder.js
document.addEventListener('DOMContentLoaded', function () {
    // === DONNÉES DE L'ARBRE (format hiérarchique) ===
    const treeData = {
        name: "Vous",
        born: "1990",
        died: "",
        info: "Vous êtes à l'origine de cet arbre.",
        children: [
            {
                name: "Mère",
                born: "1965",
                died: "",
                info: "Votre mère, source de nombreuses histoires familiales.",
                children: [
                    {
                        name: "Grand-mère",
                        born: "1940",
                        died: "2010",
                        info: "Originaire de Bretagne, elle adorait la cuisine traditionnelle."
                    },
                    {
                        name: "Grand-père",
                        born: "1938",
                        died: "2015",
                        info: "Ancien marin, il a parcouru les océans."
                    }
                ]
            },
            {
                name: "Père",
                born: "1963",
                died: "",
                info: "Votre père, passionné d'histoire locale.",
                children: [
                    {
                        name: "Tante Sophie",
                        born: "1970",
                        died: "",
                        info: "Vit à Lyon, collectionne les documents anciens."
                    },
                    {
                        name: "Oncle Marc",
                        born: "1968",
                        died: "2020",
                        info: "Auteur de récits familiaux non publiés."
                    }
                ]
            }
        ]
    };

    // === CONFIGURATION D3 ===
    const margin = { top: 20, right: 90, bottom: 30, left: 90 };
    const width = document.getElementById('tree-container').offsetWidth - margin.left - margin.right;
    const height = 700;

    // Créer la hiérarchie
    const root = d3.hierarchy(treeData);
    const treeLayout = d3.tree().size([height, width]);

    // Appliquer la mise en page
    treeLayout(root);

    // Créer SVG
    const svg = d3.select("#tree-container")
        .append("svg")
        .attr("width", "100%")
        .attr("height", height + margin.top + margin.bottom);

    const g = svg.append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    // Zoom et drag
    const zoom = d3.zoom()
        .scaleExtent([0.1, 3])
        .on("zoom", (event) => {
            g.attr("transform", event.transform);
        });

    svg.call(zoom);

    // Liens
    const link = g.selectAll(".link")
        .data(root.links())
        .enter().append("path")
        .attr("class", "link")
        .attr("d", d3.linkHorizontal()
            .x(d => d.y)
            .y(d => d.x)
        );

    // Noeuds
    const node = g.selectAll(".node")
        .data(root.descendants())
        .enter().append("g")
        .attr("class", "node")
        .attr("transform", d => `translate(${d.y},${d.x})`)
        .on("click", function (event, d) {
            showTooltip(d.data, event);
        })
        .on("mouseover", function () {
            d3.select(this).select("circle").transition().attr("r", 14);
        })
        .on("mouseout", function () {
            d3.select(this).select("circle").transition().attr("r", 10);
        });

    // Cercles
    node.append("circle")
        .attr("r", 10);

    // Texte
    node.append("text")
        .attr("dy", "18")
        .style("text-anchor", "middle")
        .text(d => d.data.name);

    // === TOOLTIP ===
    const tooltip = d3.select("#tooltip");

    function showTooltip(data, event) {
        const lang = document.documentElement.lang || 'fr';

        const labels = {
            fr: { born: '🎂 Né(e)', died: '⚰️ Décédé(e)', alive: 'Vivant(e)' },
            en: { born: '🎂 Born', died: '⚰️ Died', alive: 'Alive' },
            br: { born: '🎂 Dezhanet', died: '⚰️ Marvet', alive: 'Bev' }
        };

        const l = labels[lang] || labels.fr;

        const diedLine = data.died
            ? `${l.died} : ${data.died}`
            : l.alive;

        tooltip
            .style("opacity", 1)
            .html(`
                <strong>${data.name}</strong><br>
                ${l.born} : ${data.born}<br>
                ${diedLine}<br>
                <em>${data.info}</em>
            `)
            .style("left", (event.pageX + 10) + "px")
            .style("top", (event.pageY - 28) + "px");
    }

    // Fermer le tooltip en cliquant ailleurs
    d3.select("body").on("click", function (event) {
        if (!d3.select(event.target).classed("node")) {
            tooltip.style("opacity", 0);
        }
    });

    // === DICTIONNAIRE DE TRADUCTION ===
    const translations = {
        fr: {
            'nav.dashboard': 'Tableau de bord',
            'nav.family': 'Ma Famille',
            'nav.profile': 'Profil',
            'nav.logout': 'Déconnexion',
            'access.dyslexia': 'Mode Dyslexie',
            'access.dark': 'Passer en mode sombre',
            'tree.title': 'Votre Arbre Généalogique',
            'tree.subtitle': 'Explorez vos racines. Cliquez sur un membre pour voir ses détails. Utilisez la molette pour zoomer, cliquez et glissez pour déplacer.',
            'footer.support': 'Support',
            'footer.legal': 'Légal',
            'footer.tools': 'Outils',
            'footer.allrights': 'Tous droits réservés.',
            'modal.terms_title': 'Conditions d\'Utilisation',
            'modal.terms_p1': 'En utilisant Yggdrasil, vous acceptez nos conditions.',
            'modal.privacy_title': 'Politique de Confidentialité',
            'modal.privacy_p1': 'Nous protégeons vos données conformément au RGPD.'
        },
        en: {
            'nav.dashboard': 'Dashboard',
            'nav.family': 'My Family',
            'nav.profile': 'Profile',
            'nav.logout': 'Logout',
            'access.dyslexia': 'Dyslexia Mode',
            'access.dark': 'Switch to Dark Mode',
            'tree.title': 'Your Family Tree',
            'tree.subtitle': 'Explore your roots. Click a member to see details. Use scroll to zoom, click and drag to pan.',
            'footer.support': 'Support',
            'footer.legal': 'Legal',
            'footer.tools': 'Tools',
            'footer.allrights': 'All rights reserved.',
            'modal.terms_title': 'Terms of Service',
            'modal.terms_p1': 'By using Yggdrasil, you agree to our terms.',
            'modal.privacy_title': 'Privacy Policy',
            'modal.privacy_p1': 'We protect your data in compliance with GDPR.'
        },
        br: {
            'nav.dashboard': 'Taolenn-wez',
            'nav.family': 'Ho Teuli',
            'nav.profile': 'Profil',
            'nav.logout': 'Digevreañ',
            'access.dyslexia': 'Mod Dyslexie',
            'access.dark': 'Trec\'h da gouloù tiñj',
            'tree.title': 'Ho Kernezenn Genealogel',
            'tree.subtitle': 'Diskouezit ho zudeni. Klikit war un den evit gwelet ar munudoù. Ar boblinenn evit sklaerat, klikit ha riklit evit dilec\'hiañ.',
            'footer.support': 'Skor',
            'footer.legal': 'Lezenn',
            'footer.tools': 'Ostilhoù',
            'footer.allrights': 'An holl gwirioù rezervet.',
            'modal.terms_title': 'Ranneloù Implijout',
            'modal.terms_p1': 'En ur implijout Yggdrasil, e gemerit ar ranneloù.',
            'modal.privacy_title': 'Politegerez Aotre',
            'modal.privacy_p1': 'Gouzout a ra bout ho titouroù en ur gomziant GDPR.'
        }
    };

    // Langues
    const languages = ['fr', 'en', 'br'];
    const langNames = {
        fr: 'FR → EN → BR',
        en: 'EN → BR → FR',
        br: 'BR → FR → EN'
    };

    function setLanguage(lang) {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (translations[lang] && translations[lang][key]) {
                el.textContent = translations[lang][key];
            }
        });
        document.getElementById('lang-text').textContent = langNames[lang];
        document.documentElement.lang = lang;
        localStorage.setItem('language', lang);
    }

    // Charger la langue
    const savedLang = localStorage.getItem('language') || 'fr';
    setLanguage(savedLang);

    // Bascule langue
    document.getElementById('lang-toggle')?.addEventListener('click', () => {
        const currentLang = localStorage.getItem('language') || 'fr';
        const nextLang = languages[(languages.indexOf(currentLang) + 1) % languages.length];
        setLanguage(nextLang);
    });

    // === MODE DYSLEXIE & SOMBRE ===
    const body = document.body;

    if (localStorage.getItem('dyslexiaMode') === 'true') body.classList.add('opendyslexic');
    document.getElementById('dyslexia-toggle')?.addEventListener('click', () => {
        body.classList.toggle('opendyslexic');
        localStorage.setItem('dyslexiaMode', body.classList.contains('opendyslexic') ? 'true' : 'false');
    });

    if (localStorage.getItem('theme') === 'dark') body.classList.add('dark-mode');
    document.getElementById('theme-toggle')?.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
    });

    // === MODALS ===
    const termsModal = document.getElementById('terms-modal');
    const privacyModal = document.getElementById('privacy-modal');
    const closeModal = document.querySelectorAll('.modal-close');
    const modalLinks = document.querySelectorAll('.modal-link');

    modalLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const modalType = link.getAttribute('data-modal');
            if (modalType === 'terms') termsModal.style.display = 'flex';
            if (modalType === 'privacy') privacyModal.style.display = 'flex';
        });
    });

    closeModal.forEach(btn => {
        btn.addEventListener('click', () => {
            termsModal.style.display = 'none';
            privacyModal.style.display = 'none';
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === termsModal) termsModal.style.display = 'none';
        if (e.target === privacyModal) privacyModal.style.display = 'none';
    });

    console.log('🌳 Yggdrasil - Arbre généalogique chargé');
});