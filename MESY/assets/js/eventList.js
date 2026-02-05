// Initialize events from data layer
let allEvents = [...sampleEvents];
let filteredEvents = [...sampleEvents];

const searchInput = document.getElementById('searchInput');
const locationFilter = document.getElementById('locationFilter');
const eventsGrid = document.getElementById('eventsGrid');
const resultsInfo = document.getElementById('resultsInfo');
const eventCount = document.getElementById('eventCount');
const noResults = document.getElementById('noResults');
const clearBtn = document.getElementById('clearBtn');

document.addEventListener('DOMContentLoaded', function() {
    renderEvents(filteredEvents);
    updateResultsInfo();
});

function searchEvents() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const selectedLocation = locationFilter.value;

    filteredEvents = allEvents.filter(event => {
        const matchesSearch = !searchTerm || 
            event.eventName.toLowerCase().includes(searchTerm) ||
            (event.organizerName && event.organizerName.toLowerCase().includes(searchTerm)) ||
            event.description.toLowerCase().includes(searchTerm);
        
        const matchesLocation = !selectedLocation || 
            event.location.toLowerCase().includes(selectedLocation.toLowerCase());

        return matchesSearch && matchesLocation;
    });

    renderEvents(filteredEvents);
    updateResultsInfo();
    updateClearButton();
}
function clearFilters() {
    searchInput.value = '';
    locationFilter.value = '';
    filteredEvents = [...allEvents];
    renderEvents(filteredEvents);
    updateResultsInfo();
    updateClearButton();
}

function updateClearButton() {
    const hasFilters = searchInput.value.trim() !== '' || locationFilter.value !== '';
    clearBtn.style.display = hasFilters ? 'flex' : 'none';
}

function updateResultsInfo() {
    eventCount.textContent = filteredEvents.length;
    
    if (filteredEvents.length === 0) {
        resultsInfo.style.display = 'none';
        eventsGrid.style.display = 'none';
        noResults.style.display = 'block';
    } else {
        resultsInfo.style.display = 'block';
        eventsGrid.style.display = 'grid';
        noResults.style.display = 'none';
    }
}

function renderEvents(events) {
    if (events.length === 0) {
        eventsGrid.innerHTML = '';
        return;
    }

    eventsGrid.innerHTML = events.map(event => createEventCard(event)).join('');
}

function createEventCard(event) {
    const eventDate = new Date(event.eventDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    let dateBadge = '';
    let badgeClass = '';

    if (eventDate.toDateString() === today.toDateString()) {
        dateBadge = 'Today';
        badgeClass = 'today';
    } else if (eventDate.toDateString() === tomorrow.toDateString()) {
        dateBadge = 'Tomorrow';
        badgeClass = 'tomorrow';
    } else {
        dateBadge = eventDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        badgeClass = '';
    }

    const formattedDate = eventDate.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });

    const timeRange = formatTime(event.startTime) + ' - ' + formatTime(event.endTime);

    // Check if we're in organizers folder to adjust image path
    const imagePath = window.location.pathname.includes('/organizers/') ? 
        '../assets/uploads/' : 'assets/uploads/';

    return `
        <div>
            <div class="event-card h-100"
                data-location="${event.location}"
                data-organizer="${event.organizerName}">
                <div class="event-banner">
                    ${event.eventImage ? 
                        `<img src="${imagePath}${event.eventImage}" alt="Event Image">` :
                        `<div class="banner-placeholder w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-regular fa-image"></i>
                            <span>No Image</span>
                        </div>`
                    }
                    <span class="date-badge ${badgeClass}">${dateBadge}</span>
                </div>
                <div class="event-content d-flex flex-column h-100">
                    <div class="event-title">${event.eventName}</div>
                    <p class="text-secondary">${event.category}</p>
                    <div class="event-details mb-2">
                        <div class="event-description mb-2">
                            ${event.description}
                        </div>
                        <div class="event-detail">
                            <i class="fa-regular fa-calendar"></i>
                            ${formattedDate}
                        </div>
                        <div class="event-detail">
                            <i class="fa-regular fa-clock"></i>
                            ${timeRange}
                        </div>
                        <div class="event-detail">
                            <i class="fa-solid fa-location-dot"></i>
                            ${event.location}
                        </div>
                        <div class="event-detail">
                            <i class="fa-solid fa-user"></i>
                            Organizer: ${event.organizerName}
                        </div>
                        <div class="event-footer mt-4">
                            <a href="eventInfoPage.html?eventId=${event.id}" class="event-button text-white">
                                More Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function goToEventInfo(eventId) {
    window.location.href = 'eventInfoPage.html';
}

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

searchInput.addEventListener('input', function() {
    updateClearButton();
});

locationFilter.addEventListener('change', function() {
    searchEvents();
});

searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchEvents();
    }
});

updateClearButton();