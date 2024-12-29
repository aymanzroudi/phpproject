// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Slider functionality
    const slider = document.querySelector('.hero-slider');
    const slides = slider.querySelectorAll('.slide');
    const prevBtn = slider.querySelector('.prev');
    const nextBtn = slider.querySelector('.next');
    const dotsContainer = slider.querySelector('.slider-dots');
    
    let currentSlide = 0;
    
    // Create dots
    slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('slider-dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });
    
    const dots = dotsContainer.querySelectorAll('.slider-dot');
    
    function updateSlides() {
        slides.forEach(slide => {
            slide.classList.remove('active');
            slide.style.display = 'none';
        });
        slides[currentSlide].classList.add('active');
        slides[currentSlide].style.display = 'block';
        
        dots.forEach(dot => dot.classList.remove('active'));
        dots[currentSlide].classList.add('active');
    }
    
    function goToSlide(index) {
        currentSlide = index;
        updateSlides();
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlides();
    }
    
    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateSlides();
    }
    
    prevBtn.addEventListener('click', prevSlide);
    nextBtn.addEventListener('click', nextSlide);
    
    // Auto-advance slides
    setInterval(nextSlide, 5000);
    
    // Initial setup
    updateSlides();

    // Animation du menu lors du défilement
    let lastScroll = 0;
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll <= 0) {
            navbar.style.top = "0";
            return;
        }

        if (currentScroll > lastScroll && !navbar.classList.contains('scroll-down')) {
            navbar.style.top = "-80px";
        } else if (currentScroll < lastScroll && navbar.style.top === "-80px") {
            navbar.style.top = "0";
        }
        lastScroll = currentScroll;
    });

    // Gestion des boutons d'inscription
    const inscriptionButtons = document.querySelectorAll('.btn-primary');
    inscriptionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.tagName === 'BUTTON') {  // Only for button elements, not links
                e.preventDefault();
                const eventTitle = this.closest('.event-info').querySelector('h3').textContent;
                alert(`Inscription à l'événement "${eventTitle}" - Cette fonctionnalité sera bientôt disponible avec PHP !`);
            }
        });
    });

    // Animation des cartes d'événements au survol
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Smooth scroll pour les liens de navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            if (href === '#') return;
            
            document.querySelector(href).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});

// Function to register for an event
function registerForEvent(eventId) {
    // Add registration logic here
    alert('Inscription à l\'événement en cours de développement...');
}
