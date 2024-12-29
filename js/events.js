console.log('Events.js is loaded');

// Variable globale pour le statut de connexion
let isLoggedIn;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Initialiser isLoggedIn
    isLoggedIn = document.querySelector('.register-btn') !== null;
    console.log('isLoggedIn:', isLoggedIn);

    // Initialize date pickers
    const datePickers = document.querySelectorAll('.datepicker');
    console.log('Date pickers found:', datePickers.length);
    
    flatpickr('.datepicker', {
        locale: 'fr',
        dateFormat: 'Y-m-d',
        minDate: 'today'
    });

    // Initialize price slider
    const priceSlider = document.getElementById('price_slider');
    console.log('Price slider element:', priceSlider);
    
    if (priceSlider) {
        const minPrice = Number(document.getElementById('price_min').value);
        const maxPrice = Number(document.getElementById('price_max').value);
        console.log('Price range:', minPrice, maxPrice);

        noUiSlider.create(priceSlider, {
            start: [minPrice, maxPrice],
            connect: true,
            range: {
                'min': minPrice,
                'max': maxPrice
            },
            format: {
                to: value => Math.round(value),
                from: value => Number(value)
            }
        });

        // Update input fields when slider changes
        priceSlider.noUiSlider.on('update', function(values, handle) {
            document.getElementById('price_min').value = values[0];
            document.getElementById('price_max').value = values[1];
            console.log('Slider updated:', values);
        });
    }

    // Handle search form submission
    const searchForm = document.getElementById('searchForm');
    console.log('Search form found:', searchForm);
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const formData = new FormData(this);
            const searchParams = new URLSearchParams();

            // Ajouter tous les champs du formulaire
            for (let [key, value] of formData.entries()) {
                if (value) { // Ne pas ajouter les champs vides
                    searchParams.append(key, value);
                    console.log('Form data:', key, value);
                }
            }

            // Ajouter les valeurs du slider de prix
            if (priceSlider && priceSlider.noUiSlider) {
                const [min, max] = priceSlider.noUiSlider.get();
                searchParams.append('price_min', min);
                searchParams.append('price_max', max);
                console.log('Price range in search:', min, max);
            }

            const searchUrl = 'search_events.php?' + searchParams.toString();
            console.log('Search URL:', searchUrl);

            fetch(searchUrl)
                .then(response => {
                    console.log('Search response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Search data:', data);
                    if (data.success) {
                        updateEventsDisplay(data.events);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Erreur lors de la recherche');
                });
        });

        // Reset form
        searchForm.addEventListener('reset', function(e) {
            console.log('Form reset');
            setTimeout(() => {
                if (priceSlider && priceSlider.noUiSlider) {
                    priceSlider.noUiSlider.reset();
                }
                searchForm.dispatchEvent(new Event('submit'));
            }, 0);
        });
    }

    // Handle event registration
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('register-btn')) {
            e.preventDefault();
            console.log('Register button clicked');
            
            const eventId = e.target.dataset.eventId;
            const isWaitlist = e.target.dataset.waitlist === 'true';
            console.log('Event ID:', eventId, 'Waitlist:', isWaitlist);

            if (confirm(isWaitlist ? 
                'Voulez-vous rejoindre la liste d\'attente ?' : 
                'Voulez-vous vous inscrire à cet événement ?')) {
                
                const formData = new FormData();
                formData.append('event_id', eventId);

                fetch('register_event.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Registration response:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Registration data:', data);
                    if (data.success) {
                        showSuccess(data.message);
                        if (data.status === 'registered') {
                            e.target.textContent = 'Inscrit';
                            e.target.disabled = true;
                        } else if (data.status === 'waitlist') {
                            e.target.textContent = 'En liste d\'attente';
                            e.target.disabled = true;
                        }
                        // Recharger la page pour mettre à jour les compteurs
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Erreur lors de l\'inscription');
                });
            }
        }
    });
});

function updateEventsDisplay(events) {
    console.log('Updating events display with:', events);
    
    const container = document.getElementById('events-container');
    const noResults = document.getElementById('no-results');
    
    if (!events || events.length === 0) {
        container.style.display = 'none';
        noResults.style.display = 'block';
        return;
    }

    container.style.display = 'grid';
    noResults.style.display = 'none';
    
    container.innerHTML = events.map(event => `
        <div class="event-card" data-category="${event.category}">
            <div class="event-image">
                <img src="${event.image_url || 'images/default-event.jpg'}" alt="${event.title}">
                ${event.registered_participants >= event.max_participants ? 
                    '<div class="event-status full">COMPLET</div>' : ''}
            </div>
            <div class="event-info">
                <span class="event-category">${event.category}</span>
                <h3>${event.title}</h3>
                <p class="event-description">${event.description}</p>
                <p class="event-date">
                    <i class="fas fa-calendar"></i> 
                    ${new Date(event.date).toLocaleDateString('fr-FR', { 
                        day: 'numeric', 
                        month: 'long', 
                        year: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric'
                    })}
                </p>
                <p class="event-location">
                    <i class="fas fa-map-marker-alt"></i> 
                    ${event.location}
                </p>
                <p class="event-price" data-price="${event.price}">
                    <i class="fas fa-ticket-alt"></i> 
                    ${Number(event.price).toFixed(2)} MAD
                </p>
                <p class="event-participants">
                    <i class="fas fa-users"></i>
                    ${event.registered_participants} / ${event.max_participants} participants
                    ${event.waitlist_count > 0 ? 
                        `<span class="waitlist-count">(${event.waitlist_count} en liste d'attente)</span>` : 
                        ''}
                </p>
                ${typeof isLoggedIn !== 'undefined' && isLoggedIn ? `
                    <button class="btn-primary register-btn" 
                            data-event-id="${event.id}"
                            ${event.registered_participants >= event.max_participants ? 'data-waitlist="true"' : ''}>
                        ${event.registered_participants >= event.max_participants ? 
                            'Rejoindre la liste d\'attente' : 
                            'S\'inscrire'}
                    </button>
                ` : `
                    <a href="login.php" class="btn-primary">Connectez-vous pour vous inscrire</a>
                `}
            </div>
        </div>
    `).join('');
}

function showSuccess(message) {
    console.log('Success:', message);
    alert(message);
}

function showError(message) {
    console.error('Error:', message);
    alert(message);
}
