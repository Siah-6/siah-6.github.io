const animeList = [
    {
        title: 'Tower of God',
        description: 'A story about a boy who climbs a mysterious tower.',
        image: 'images/tower_of_god.jpg'
    },
    {
        title: 'Attack on Titan',
        description: 'Humanity’s battle for survival against titans.',
        image: 'images/attack_on_titan.jpg'
    },
    {
        title: 'Chainsaw Man',
        description: 'A demon hunter who becomes part-demon himself.',
        image: 'images/chainsaw_man.jpg'
    },
    {
        title: 'Black Clover',
        description: 'A young boy’s journey to become the Wizard King.',
        image: 'images/black_clover.jpg'
    },
    {
        title: 'Wistoria',
        description: 'Magical world filled with mystery and adventure.',
        image: 'images/wistoria.jpg'
    },
    {
        title: 'Jujutsu Kaisen',
        description: 'A student fights curses and discovers sorcery.',
        image: 'images/jujutsu_kaisen.jpg'
    }
];

const galleryContainer = document.getElementById('anime-gallery');

animeList.forEach(anime => {
    const animeDiv = document.createElement('div');
    animeDiv.classList.add('col-md-4', 'mb-4');

    animeDiv.innerHTML = `
        <div class="anime-panel">
            <img src="${anime.image}" alt="${anime.title}">
            <div class="anime-details">
                <h5>${anime.title}</h5>
                <p>${anime.description}</p>
            </div>
        </div>
    `;

    galleryContainer.appendChild(animeDiv);

    animeDiv.querySelector('.anime-details').style.display = 'none';

    animeDiv.querySelector('.anime-panel').addEventListener('click', () => {
        const details = animeDiv.querySelector('.anime-details');
        details.style.display = details.style.display === 'none' ? 'block' : 'none';
    });
});
