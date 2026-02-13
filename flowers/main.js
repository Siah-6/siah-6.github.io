onload = () => {
  document.body.classList.remove("container");
  
  // Create floating hearts
  createFloatingHearts();
  
  // Add click interaction to flowers
  addFlowerInteractions();
  
  // Start music immediately
  startMusic();
};

function startMusic() {
  const audio = document.getElementById('bgMusic');
  audio.volume = 0.3;
  
  // Force play music
  audio.play().then(() => {
    console.log('Music started successfully');
  }).catch(error => {
    console.log('Autoplay blocked, trying alternative method...');
    // Try alternative method
    document.addEventListener('click', function playMusic() {
      audio.play();
      document.removeEventListener('click', playMusic);
    }, { once: true });
  });
}

function createFloatingHearts() {
  const heartsContainer = document.querySelector('.floating-hearts');
  
  setInterval(() => {
    const heart = document.createElement('div');
    heart.className = 'heart';
    heart.innerHTML = '❤️';
    heart.style.left = Math.random() * 100 + '%';
    heart.style.animationDelay = Math.random() * 2 + 's';
    heart.style.animationDuration = (Math.random() * 3 + 4) + 's';
    
    heartsContainer.appendChild(heart);
    
    // Remove heart after animation
    setTimeout(() => {
      heart.remove();
    }, 7000);
  }, 2000);
}

function addFlowerInteractions() {
  const flowers = document.querySelectorAll('.flower');
  
  flowers.forEach(flower => {
    flower.addEventListener('click', function() {
      this.style.transform = 'scale(1.1)';
      setTimeout(() => {
        this.style.transform = 'scale(1)';
      }, 300);
      
      // Create sparkle effect
      createSparkles(this);
    });
  });
}

function createSparkles(element) {
  const rect = element.getBoundingClientRect();
  
  for (let i = 0; i < 8; i++) {
    const sparkle = document.createElement('div');
    sparkle.style.position = 'fixed';
    sparkle.style.left = rect.left + rect.width / 2 + 'px';
    sparkle.style.top = rect.top + rect.height / 2 + 'px';
    sparkle.style.width = '4px';
    sparkle.style.height = '4px';
    sparkle.style.background = '#ff69b4';
    sparkle.style.borderRadius = '50%';
    sparkle.style.pointerEvents = 'none';
    sparkle.style.zIndex = '1001';
    
    const angle = (Math.PI * 2 * i) / 8;
    const velocity = 100 + Math.random() * 50;
    
    document.body.appendChild(sparkle);
    
    let progress = 0;
    const animate = () => {
      progress += 0.02;
      const x = Math.cos(angle) * velocity * progress;
      const y = Math.sin(angle) * velocity * progress - 50 * progress * progress;
      
      sparkle.style.transform = `translate(${x}px, ${y}px)`;
      sparkle.style.opacity = 1 - progress;
      
      if (progress < 1) {
        requestAnimationFrame(animate);
      } else {
        sparkle.remove();
      }
    };
    
    requestAnimationFrame(animate);
  }
}
