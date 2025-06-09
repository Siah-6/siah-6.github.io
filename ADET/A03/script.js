const characters = [
    {
        id: 1,
        name: 'Ichigo Kurosaki',
        title: 'Substitute Soul Reaper',
        image: 'images/ichigo/ichigo-base.jpg',
        description: 'A teenager with the ability to see ghosts who becomes a Soul Reaper.',
        power: { name: "Getsuga Tensho", description: "A powerful energy slash that can cut through almost anything!" },
        stats: { power: 95, speed: 90 },
        bentoImages: [
            { name: 'Base Form', image: 'images/ichigo/ichigo.jpg' },
            { name: 'Shikai Form', image: 'images/ichigo/ichigo-shikai.jpg' },
            { name: 'Bankai Form', image: 'images/ichigo/ichigo-bankai.jpg' },
            { name: 'Hollow Form', image: 'images/ichigo/ichigo-hollow.jpg' },
            { name: 'Final Getsuga', image: 'images/ichigo/ichigo-final.jpg' },
            { name: 'True Bankai', image: 'images/ichigo/true-bankai.jpg' }
        ]
    },
    {
        id: 2,
        name: 'Rukia Kuchiki',
        title: 'Soul Reaper',
        image: 'images/rukia/rukia.jpg',
        description: 'A Soul Reaper from the Kuchiki noble family with ice powers.',
        power: { name: "Sode no Shirayuki", description: "The purest ice-type Zanpakuto with freezing abilities!" },
        stats: { power: 85, speed: 80 },
        bentoImages: [
            { name: 'Base Form', image: 'images/rukia/rukia.jpg' },
            { name: 'Shikai Release', image: 'images/rukia/rukia-shikai.jpg' },
            { name: 'Bankai Form', image: 'images/rukia/rukia-bankai.jpg' },
            { name: 'Ice Attacks', image: 'images/rukia/rukia-ice.jpg' },
            { name: 'Combat Stance', image: 'images/rukia/rukia-combat.jpg' },
            { name: 'Soul Reaper Outfit', image: 'images/rukia/rukia-uniform.jpg' }
        ]
    },
    {
        id: 3,
        name: 'Byakuya Kuchiki',
        title: 'Captain of Squad 6',
        image: 'images/byakuya/byakuya.jpg',
        description: 'Noble captain known for his cherry blossom zanpakuto.',
        power: { name: "Senbonzakura", description: "Thousand cherry blossoms that dance like deadly petals!" },
        stats: { power: 98, speed: 95 },
        bentoImages: [
            { name: 'Captain Form', image: 'images/byakuya/byakuya.jpg' },
            { name: 'Senbonzakura Shikai', image: 'images/byakuya/byakuya-shikai.jpg' },
            { name: 'Bankai Release', image: 'images/byakuya/byakuya-bankai.jpg' },
            { name: 'Cherry Blossoms', image: 'images/byakuya/byakuya-petals.jpg' },,
            { name: 'Final Scene', image: 'images/byakuya/byakuya-final.jpg' }
        ]
    },
    {
        id: 4,
        name: 'Kenpachi Zaraki',
        title: 'Captain of Squad 11',
        image: 'images/kenpachi/kenpachi.jpg',
        description: 'The most feared warrior in Soul Society.',
        power: { name: "Nozarashi", description: "Raw cutting power that can cleave through any defense!" },
        stats: { power: 100, speed: 70 },
        bentoImages: [
            { name: 'Battle Ready', image: 'images/kenpachi/kenpachi.jpg' },
            { name: 'Eyepatch Off', image: 'images/kenpachi/kenpachi-eyepatch.jpg' },
            { name: 'Shikai Release', image: 'images/kenpachi/kenpachi-shikai.jpg' },
            { name: 'Berserker Mode', image: 'images/kenpachi/kenpachi-berserker.jpg' },
            { name: 'Nozarashi Blade', image: 'images/kenpachi/kenpachi-blade.jpg' }
        ]
    },
    {
        id: 5,
        name: 'Ulquiorra Cifer',
        title: 'Espada #4',
        image: 'images/ulqui/ulquiorra.jpg',
        description: 'The emotionless Arrancar with devastating power.',
        power: { name: "Cero Oscuras", description: "The darkest Cero that can destroy anything in its path!" },
        stats: { power: 94, speed: 90 },
        bentoImages: [
            { name: 'Arrancar Form', image: 'images/ulqui/ulquiorra.jpg' },
            { name: 'Resurrección', image: 'images/ulqui/ulquiorra-res.jpg' },
            { name: 'Segunda Etapa', image: 'images/ulqui/ulquiorra-segunda.jpg' },
            { name: 'Cero Oscuras', image: 'images/ulqui/ulquiorra-cero.jpg' },
            { name: 'Wings of Despair', image: 'images/ulqui/ulquiorra-wings.jpg' },
            { name: 'Final Moments', image: 'images/ulqui/ulquiorra-end.jpg' }
        ]
    },
    {
        id: 6,
        name: 'Grimmjow Jaegerjaquez',
        title: 'Espada #6',
        image: 'images/grimm/grimmjow.jpg',
        description: 'The aggressive and battle-hungry Arrancar with panther-like abilities.',
        power: { name: "Pantera", description: "Fierce panther claws that can tear through any defense!" },
        stats: { power: 91, speed: 93 },
        bentoImages: [
            { name: 'Arrancar Form', image: 'images/grimm/grimmjow.jpg' },
            { name: 'Combat Mode', image: 'images/grimm/grimmjow-combat.jpg' },
            { name: 'Pantera Release', image: 'images/grimm/grimmjow-pantera.jpg' },
            { name: 'Cero Blast', image: 'images/grimm/grimmjow-cero.jpg' },
            { name: 'Berserker State', image: 'images/grimm/grimmjow-berserker.jpg' },
            { name: 'Final Strike', image: 'images/grimm/grimmjow-final.jpg' }
        ]
    }
];
function showPage(pageId) {
    document.querySelectorAll('.page-section').forEach(page => {
        page.classList.remove('active');
    });
    document.getElementById(pageId).classList.add('active');
    if (pageId === 'characters') {
        loadCharacters();
    }
    setTimeout(() => {
        animatePageLoad();
    }, 100);
}

function show404() {
    showPage('error404');
}

function loadCharacters() {
    const grid = document.getElementById('characterGrid');
    grid.innerHTML = '';
    
    characters.forEach((character, index) => {
        const card = document.createElement('div');
        card.className = 'col-lg-4 col-md-6 col-sm-6 fade-in-up';
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.innerHTML = `
            <div class="character-card hover-lift-intense" onclick="showCharacterBento(${character.id})">
                <div class="character-image-container">
                    <img src="${character.image}" alt="${character.name}" class="character-image">
                    <div class="character-overlay">
                        <div class="character-power-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                    </div>
                </div>
                
                <div class="character-info">
                    <h4 class="character-name glow-text">${character.name}</h4>
                    <p class="character-title">${character.title}</p>
                    <p class="character-description">${character.description}</p>
                    
                    <div class="character-stats">
                        <div class="stat-bar">
                            <span class="stat-label">Power</span>
                            <div class="stat-fill fire-gradient" style="width: ${character.stats.power}%;"></div>
                        </div>
                        <div class="stat-bar">
                            <span class="stat-label">Speed</span>
                            <div class="stat-fill ice-gradient" style="width: ${character.stats.speed}%;"></div>
                        </div>
                    </div>
                    
                    <button class="btn btn-character-action w-100 mt-3" onclick="event.stopPropagation(); activatePower(${character.id})">
                        <i class="fas fa-sword me-2"></i>
                        Activate Power
                    </button>
                </div>
            </div>
        `;
        
        grid.appendChild(card);
    });
}
function showCharacterBento(characterId) {
    const character = characters.find(c => c.id === characterId);
    if (!character) return;
    
    const modal = document.createElement('div');
    modal.className = 'character-bento-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        opacity: 0;
        transition: all 0.3s ease;
    `;
    
    const bentoImages = character.bentoImages || [
        { name: `${character.name} - Base Form`, image: character.image },
        { name: `${character.name} - Shikai`, image: character.image },
        { name: `${character.name} - Bankai`, image: character.image },
        { name: `${character.name} - Final Form`, image: character.image }
    ];
    
    modal.innerHTML = `
        <div class="bento-modal-content" style="
            background: rgba(26, 26, 26, 0.95);
            border-radius: 20px;
            padding: 2rem;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            border: 2px solid var(--electric-blue);
            transform: scale(0.8);
            opacity: 0;
            transition: all 0.3s ease;
        ">
            <div class="modal-header" style="text-align: center; margin-bottom: 2rem; position: relative;">
                <h2 class="glow-text" style="font-family: 'Orbitron', monospace; margin-bottom: 0.5rem;">${character.name}</h2>
                <p style="color: var(--fire-orange); font-weight: 600; margin-bottom: 0;">${character.title}</p>
                <button onclick="closeCharacterModal()" style="
                    position: absolute;
                    top: 0;
                    right: 0;
                    background: none;
                    border: none;
                    color: white;
                    font-size: 1.5rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                ">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="character-bento-grid" style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                grid-auto-rows: 200px;
                gap: 15px;
                margin-bottom: 2rem;
            ">
                ${bentoImages.map((img, index) => `
                    <div class="bento-item ${index === 0 ? 'bento-large' : ''}" style="
                        ${index === 0 ? 'grid-row: span 2; grid-column: span 2;' : ''}
                        background: rgba(0, 0, 0, 0.5);
                        border-radius: 15px;
                        overflow: hidden;
                        position: relative;
                        transition: transform 0.3s ease;
                        cursor: pointer;
                    " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <img src="${img.image}" alt="${img.name}" style="
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        " onerror="this.src='${character.image}'">
                        <div class="bento-item-overlay" style="
                            position: absolute;
                            bottom: 0;
                            left: 0;
                            right: 0;
                            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
                            padding: 1rem;
                            color: white;
                        ">
                            <h5 class="bento-item-title" style="margin: 0; font-size: 0.9rem; font-weight: 600;">${img.name}</h5>
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <div class="character-details" style="text-align: center;">
                <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 1rem;">${character.description}</p>
                <div style="margin-bottom: 1.5rem;">
                    <h5 class="character-power-name" style="color: var(--electric-blue); font-family: 'Orbitron', monospace; margin-bottom: 0.5rem;">${character.power.name}</h5>
                    <p class="character-power-description" style="color: var(--text-muted); font-size: 0.9rem;">${character.power.description}</p>
                </div>
                <button class="btn btn-custom btn-fire" onclick="activatePower(${character.id}); closeCharacterModal();">
                    <i class="fas fa-bolt me-2"></i>
                    Unleash Power
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => {
        modal.style.opacity = '1';
        const content = modal.querySelector('.bento-modal-content');
        content.style.transform = 'scale(1)';
        content.style.opacity = '1';
    }, 10);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeCharacterModal();
        }
    });
}
function closeCharacterModal() {
    const modal = document.querySelector('.character-bento-modal');
    if (modal) {
        modal.style.opacity = '0';
        const content = modal.querySelector('.bento-modal-content');
        content.style.transform = 'scale(0.8)';
        content.style.opacity = '0';
        
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}
function shuffleCharacters() {
    const grid = document.getElementById('characterGrid');
    const cards = Array.from(grid.children);
    cards.forEach(card => {
        card.classList.add('shuffle-animation');
    });
    for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [cards[i], cards[j]] = [cards[j], cards[i]];
    }
    setTimeout(() => {
        grid.innerHTML = '';
        cards.forEach(card => {
            card.classList.remove('shuffle-animation');
            grid.appendChild(card);
        });
    }, 300);
}
function animateAll() {
    const cards = document.querySelectorAll('.character-card');
    
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('power-wave-animation');
            setTimeout(() => {
                card.classList.remove('power-wave-animation');
            }, 1000);
        }, index * 100);
    });
}
function activatePower(characterId) {
    const character = characters.find(c => c.id === characterId);
    if (!character) return;
    const powerEffect = document.createElement('div');
    powerEffect.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(45deg, var(--electric-blue), var(--fire-orange));
        color: white;
        padding: 2rem;
        border-radius: 20px;
        text-align: center;
        z-index: 11000;
        animation: powerActivation 2s ease-out forwards;
        font-family: 'Orbitron', monospace;
        border: 3px solid white;
        box-shadow: 0 0 50px var(--electric-blue);
    `;
    
    powerEffect.innerHTML = `
        <h3 style="margin-bottom: 1rem; font-size: 1.5rem;">${character.power.name}</h3>
        <p style="margin: 0; font-size: 1rem;">${character.power.description}</p>
    `;
    
    document.body.appendChild(powerEffect);
    if (!document.getElementById('powerActivationStyle')) {
        const style = document.createElement('style');
        style.id = 'powerActivationStyle';
        style.textContent = `
            @keyframes powerActivation {
                0% {
                    opacity: 0;
                    transform: translate(-50%, -50%) scale(0.5);
                }
                50% {
                    opacity: 1;
                    transform: translate(-50%, -50%) scale(1.1);
                }
                100% {
                    opacity: 0;
                    transform: translate(-50%, -50%) scale(1);
                }
            }
        `;
        document.head.appendChild(style);
    }
    setTimeout(() => {
        powerEffect.remove();
    }, 2000);
}

function animatePageLoad() {
    const elements = document.querySelectorAll('.fade-in-up, .fade-in-down, .fade-in-left, .fade-in-right');
    elements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translate(0, 0)';
        }, index * 100);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    loadCharacters();
    
    const animatedElements = document.querySelectorAll('.fade-in-up, .fade-in-down, .fade-in-left, .fade-in-right');
    animatedElements.forEach(element => {
        element.style.opacity = '0';
    });
    setTimeout(() => {
        animatePageLoad();
    }, 500);
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCharacterModal();
    }
    if (e.code === 'Space' && document.querySelector('#characters.active')) {
        e.preventDefault();
        animateAll();
    }
});