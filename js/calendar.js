document.addEventListener('DOMContentLoaded', function() {
    // Données des événements (à remplacer par des données dynamiques plus tard avec PHP)
    const events = [
        {
            date: '2025-01-15',
            title: 'Tournoi de Football Inter-régional',
            type: 'football',
            time: '14:00',
            location: 'Stade Municipal'
        },
        {
            date: '2025-01-20',
            title: 'Championnat National de Basketball',
            type: 'basketball',
            time: '16:00',
            location: 'Palais des Sports'
        },
        {
            date: '2025-01-25',
            title: 'Open de Tennis',
            type: 'tennis',
            time: '09:00',
            location: 'Club de Tennis'
        },
        {
            date: '2025-02-01',
            title: 'Marathon de la Ville',
            type: 'athletisme',
            time: '08:00',
            location: 'Centre-ville'
        },
        {
            date: '2025-02-10',
            title: 'Compétition de Natation',
            type: 'natation',
            time: '10:00',
            location: 'Piscine Olympique'
        }
    ];

    let currentDate = new Date(2025, 0); // Janvier 2025
    const calendar = document.getElementById('calendarDays');
    const monthDisplay = document.getElementById('currentMonth');
    const selectedDateDisplay = document.getElementById('selectedDate');
    const dayEventsContainer = document.getElementById('dayEvents');

    // Boutons de navigation
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    function renderCalendar() {
        calendar.innerHTML = '';
        monthDisplay.textContent = new Intl.DateTimeFormat('fr-FR', { 
            month: 'long', 
            year: 'numeric' 
        }).format(currentDate);

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        
        // Ajout des jours vides pour commencer au bon jour de la semaine
        const firstDayOfWeek = firstDay.getDay() || 7; // Convertit 0 (dimanche) en 7
        for (let i = 1; i < firstDayOfWeek; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day empty';
            calendar.appendChild(emptyDay);
        }

        // Ajout des jours du mois
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;

            const currentDateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayEvents = events.filter(event => event.date === currentDateString);

            if (dayEvents.length > 0) {
                dayElement.classList.add('has-event');
            }

            dayElement.addEventListener('click', () => showEvents(currentDateString, dayEvents));
            calendar.appendChild(dayElement);
        }
    }

    function showEvents(date, dayEvents) {
        // Mise à jour de la date sélectionnée
        const formattedDate = new Date(date).toLocaleDateString('fr-FR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        selectedDateDisplay.textContent = formattedDate;

        // Affichage des événements
        dayEventsContainer.innerHTML = '';
        if (dayEvents.length === 0) {
            dayEventsContainer.innerHTML = '<p>Aucun événement prévu pour cette date</p>';
            return;
        }

        dayEvents.forEach(event => {
            const eventElement = document.createElement('div');
            eventElement.className = `day-event ${event.type}`;
            eventElement.innerHTML = `
                <div class="event-info">
                    <h4>${event.title}</h4>
                    <p><i class="fas fa-clock"></i> ${event.time}</p>
                    <p><i class="fas fa-map-marker-alt"></i> ${event.location}</p>
                </div>
                <a href="evenements.html" class="btn-primary">Voir détails</a>
            `;
            dayEventsContainer.appendChild(eventElement);
        });
    }

    // Initialisation du calendrier
    renderCalendar();
});
