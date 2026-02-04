// Mock Data - Simulating backend database
const mockFlightData = [
    {
        flightNumber: "PR123",
        departureAirportCode: "MNL",
        departureDatetime: "2024-01-15 08:30",
        arrivalAirportCode: "CEB",
        arrivalDatetime: "2024-01-15 10:15",
        airlineName: "Philippine Airlines",
        aircraftType: "A320",
        passengerCount: 156,
        ticketPrice: 3500,
        pilotName: "Capt. Juan Santos",
        creditCardType: "Visa"
    },
    {
        flightNumber: "5J456",
        departureAirportCode: "MNL",
        departureDatetime: "2024-01-15 09:45",
        arrivalAirportCode: "DVO",
        arrivalDatetime: "2024-01-15 11:30",
        airlineName: "Cebu Pacific",
        aircraftType: "A330",
        passengerCount: 189,
        ticketPrice: 2800,
        pilotName: "Capt. Maria Reyes",
        creditCardType: "Mastercard"
    },
    {
        flightNumber: "Z2789",
        departureAirportCode: "MNL",
        departureDatetime: "2024-01-15 11:00",
        arrivalAirportCode: "BKK",
        arrivalDatetime: "2024-01-15 13:45",
        airlineName: "AirAsia",
        aircraftType: "A320",
        passengerCount: 142,
        ticketPrice: 4500,
        pilotName: "Capt. Robert Lee",
        creditCardType: "Amex"
    },
    {
        flightNumber: "PR234",
        departureAirportCode: "CEB",
        departureDatetime: "2024-01-15 12:30",
        arrivalAirportCode: "MNL",
        arrivalDatetime: "2024-01-15 14:15",
        airlineName: "Philippine Airlines",
        aircraftType: "B737",
        passengerCount: 134,
        ticketPrice: 3200,
        pilotName: "Capt. Ana Cruz",
        creditCardType: "Visa"
    },
    {
        flightNumber: "5J789",
        departureAirportCode: "DVO",
        departureDatetime: "2024-01-15 14:00",
        arrivalAirportCode: "MNL",
        arrivalDatetime: "2024-01-15 15:45",
        airlineName: "Cebu Pacific",
        aircraftType: "Q400",
        passengerCount: 78,
        ticketPrice: 2200,
        pilotName: "Capt. Carlos Garcia",
        creditCardType: "Mastercard"
    },
    {
        flightNumber: "2P345",
        departureAirportCode: "MNL",
        departureDatetime: "2024-01-15 16:30",
        arrivalAirportCode: "CRK",
        arrivalDatetime: "2024-01-15 17:15",
        airlineName: "PAL Express",
        aircraftType: "Q400",
        passengerCount: 68,
        ticketPrice: 1800,
        pilotName: "Capt. Diana Martinez",
        creditCardType: "Visa"
    },
    {
        flightNumber: "PR567",
        departureAirportCode: "MNL",
        departureDatetime: "2024-01-15 18:00",
        arrivalAirportCode: "SIN",
        arrivalDatetime: "2024-01-15 22:30",
        airlineName: "Philippine Airlines",
        aircraftType: "B777",
        passengerCount: 267,
        ticketPrice: 8900,
        pilotName: "Capt. Edward Wong",
        creditCardType: "Amex"
    },
    {
        flightNumber: "5J890",
        departureAirportCode: "MNL",
        departureDatetime: "2024-01-15 19:30",
        arrivalAirportCode: "HKG",
        arrivalDatetime: "2024-01-15 21:45",
        airlineName: "Cebu Pacific",
        aircraftType: "A330",
        passengerCount: 198,
        ticketPrice: 5200,
        pilotName: "Capt. Francis Chen",
        creditCardType: "Mastercard"
    }
];

// Application State
let currentFlights = [...mockFlightData];
let filteredFlights = [...mockFlightData];
let charts = {};

// Simulated API Service
const FlightService = {
    // Simulate API delay
    delay: (ms) => new Promise(resolve => setTimeout(resolve, ms)),

    // Get all flights with simulated delay
    getFlights: async (filters = {}) => {
        await FlightService.delay(800); // Simulate network delay
        
        let filtered = [...mockFlightData];
        
        // Apply filters
        if (filters.airline) {
            filtered = filtered.filter(f => f.airlineName === filters.airline);
        }
        if (filters.aircraft) {
            filtered = filtered.filter(f => f.aircraftType === filters.aircraft);
        }
        if (filters.departureAirportCode) {
            filtered = filtered.filter(f => f.departureAirportCode === filters.departureAirportCode);
        }
        if (filters.arrivalAirportCode) {
            filtered = filtered.filter(f => f.arrivalAirportCode === filters.arrivalAirportCode);
        }
        if (filters.creditCardType) {
            filtered = filtered.filter(f => f.creditCardType === filters.creditCardType);
        }
        
        // Apply sorting
        if (filters.sort) {
            filtered.sort((a, b) => {
                const aVal = a[filters.sort];
                const bVal = b[filters.sort];
                const order = filters.order === 'DESC' ? -1 : 1;
                return aVal > bVal ? order : -order;
            });
        }
        
        return filtered;
    },

    // Get unique values for filters
    getUniqueAirlines: () => [...new Set(mockFlightData.map(f => f.airlineName))],
    getUniqueAircraft: () => [...new Set(mockFlightData.map(f => f.aircraftType))],
    getUniqueAirports: () => [...new Set([...mockFlightData.map(f => f.departureAirportCode), ...mockFlightData.map(f => f.arrivalAirportCode)])],
    getUniqueCreditCardTypes: () => [...new Set(mockFlightData.map(f => f.creditCardType))],

    // Export data simulation
    exportData: async (data) => {
        await FlightService.delay(500);
        
        // Simulate CSV generation
        const headers = ['Flight Number', 'Departure Airport', 'Departure Date/Time', 'Arrival Airport', 'Arrival Date/Time', 'Airline', 'Aircraft Type', 'Passenger Count', 'Ticket Price', 'Pilot Name', 'Credit Card Type'];
        const csvContent = [
            headers.join(','),
            ...data.map(flight => [
                flight.flightNumber,
                flight.departureAirportCode,
                flight.departureDatetime,
                flight.arrivalAirportCode,
                flight.arrivalDatetime,
                flight.airlineName,
                flight.aircraftType,
                flight.passengerCount,
                flight.ticketPrice,
                flight.pilotName,
                flight.creditCardType
            ].join(','))
        ].join('\n');
        
        return csvContent;
    }
};

// Toast Notification System
const Toast = {
    show: (message, type = 'success') => {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
};

// Loading State Management
const Loading = {
    show: () => {
        document.getElementById('loadingOverlay').style.display = 'flex';
    },
    
    hide: () => {
        document.getElementById('loadingOverlay').style.display = 'none';
    }
};

// Initialize Application
document.addEventListener('DOMContentLoaded', async function() {
    await initializeApp();
});

async function initializeApp() {
    Loading.show();
    
    try {
        // Initialize filters
        populateFilters();
        
        // Load initial data
        await loadFlightData();
        
        // Initialize charts
        initializeCharts();
        
        // Setup event listeners
        setupEventListeners();
        
        Toast.show('Dashboard loaded successfully!', 'success');
    } catch (error) {
        Toast.show('Failed to load dashboard. Please try again.', 'error');
    } finally {
        Loading.hide();
    }
}

// Populate filter dropdowns
function populateFilters() {
    const airlines = FlightService.getUniqueAirlines();
    const aircraft = FlightService.getUniqueAircraft();
    const airports = FlightService.getUniqueAirports();
    const creditCards = FlightService.getUniqueCreditCardTypes();
    
    // Populate airline filter
    const airlineSelect = document.getElementById('airlineSelect');
    airlines.forEach(airline => {
        const option = document.createElement('option');
        option.value = airline;
        option.textContent = airline;
        airlineSelect.appendChild(option);
    });
    
    // Populate aircraft filter
    const aircraftSelect = document.getElementById('aircraftSelect');
    aircraft.forEach(plane => {
        const option = document.createElement('option');
        option.value = plane;
        option.textContent = plane;
        aircraftSelect.appendChild(option);
    });
    
    // Populate airport filters
    const departureSelect = document.getElementById('departureSelect');
    const arrivalSelect = document.getElementById('arrivalSelect');
    airports.forEach(airport => {
        const option1 = document.createElement('option');
        option1.value = airport;
        option1.textContent = airport;
        departureSelect.appendChild(option1);
        
        const option2 = document.createElement('option');
        option2.value = airport;
        option2.textContent = airport;
        arrivalSelect.appendChild(option2);
    });
    
    // Populate credit card filter
    const creditCardSelect = document.getElementById('creditCardTypeSelect');
    creditCards.forEach(card => {
        const option = document.createElement('option');
        option.value = card;
        option.textContent = card;
        creditCardSelect.appendChild(option);
    });
}

// Load and display flight data
async function loadFlightData(filters = {}) {
    try {
        const flights = await FlightService.getFlights(filters);
        filteredFlights = flights;
        
        updateStats();
        updateTable(flights);
        updateCharts(flights);
        
        document.getElementById('resultsCount').textContent = flights.length;
    } catch (error) {
        Toast.show('Failed to load flight data', 'error');
    }
}

// Update statistics cards
function updateStats() {
    const stats = {
        totalFlights: filteredFlights.length,
        totalAirlines: FlightService.getUniqueAirlines().length,
        totalAircraft: FlightService.getUniqueAircraft().length,
        totalAirports: FlightService.getUniqueAirports().length
    };
    
    // Animate counter updates
    animateCounter('totalFlights', stats.totalFlights);
    animateCounter('totalAirlines', stats.totalAirlines);
    animateCounter('totalAircraft', stats.totalAircraft);
    animateCounter('totalAirports', stats.totalAirports);
}

// Animate number counter
function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    const duration = 1000;
    const start = 0;
    const increment = targetValue / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= targetValue) {
            current = targetValue;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// Update flight table
function updateTable(flights) {
    const tbody = document.getElementById('flightsTableBody');
    
    if (flights.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <p>No flights found matching your criteria</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = flights.map(flight => `
        <tr>
            <td><strong>${flight.flightNumber}</strong></td>
            <td>${flight.departureAirportCode}<br><small>${flight.departureDatetime}</small></td>
            <td>${flight.arrivalAirportCode}<br><small>${flight.arrivalDatetime}</small></td>
            <td>${flight.airlineName}</td>
            <td>${flight.aircraftType}</td>
            <td>${flight.passengerCount}</td>
            <td>₱${flight.ticketPrice.toLocaleString()}</td>
            <td>${flight.pilotName}</td>
            <td><span class="badge badge-${getCreditCardBadgeClass(flight.creditCardType)}">${flight.creditCardType}</span></td>
        </tr>
    `).join('');
}

// Get badge class for credit card type
function getCreditCardBadgeClass(cardType) {
    const classes = {
        'Visa': 'success',
        'Mastercard': 'warning',
        'Amex': 'danger'
    };
    return classes[cardType] || 'secondary';
}

// Initialize charts
function initializeCharts() {
    // Airline Distribution Pie Chart
    const airlineCtx = document.getElementById('airlineChart').getContext('2d');
    const airlineData = {};
    filteredFlights.forEach(f => {
        airlineData[f.airlineName] = (airlineData[f.airlineName] || 0) + 1;
    });
    
    charts.airline = new Chart(airlineCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(airlineData),
            datasets: [{
                data: Object.values(airlineData),
                backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#f1f5f9',
                        padding: 15,
                        font: { size: 12 }
                    }
                }
            }
        }
    });
    
    // Aircraft Types Bar Chart
    const aircraftCtx = document.getElementById('aircraftChart').getContext('2d');
    const aircraftData = {};
    filteredFlights.forEach(f => {
        aircraftData[f.aircraftType] = (aircraftData[f.aircraftType] || 0) + 1;
    });
    
    charts.aircraft = new Chart(aircraftCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(aircraftData),
            datasets: [{
                label: 'Flights',
                data: Object.values(aircraftData),
                backgroundColor: '#3b82f6',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#334155' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
    
    // Monthly Trends Line Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    charts.trends = new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Flights',
                data: [120, 145, 165, 180, 195, 210],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#334155' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// Update charts with new data
function updateCharts(flights) {
    // Update airline chart
    const airlineData = {};
    flights.forEach(f => {
        airlineData[f.airlineName] = (airlineData[f.airlineName] || 0) + 1;
    });
    charts.airline.data.labels = Object.keys(airlineData);
    charts.airline.data.datasets[0].data = Object.values(airlineData);
    charts.airline.update();
    
    // Update aircraft chart
    const aircraftData = {};
    flights.forEach(f => {
        aircraftData[f.aircraftType] = (aircraftData[f.aircraftType] || 0) + 1;
    });
    charts.aircraft.data.labels = Object.keys(aircraftData);
    charts.aircraft.data.datasets[0].data = Object.values(aircraftData);
    charts.aircraft.update();
}

// Setup event listeners
function setupEventListeners() {
    // Filter form submission
    document.getElementById('filterForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        Loading.show();
        
        const formData = new FormData(e.target);
        const filters = Object.fromEntries(formData.entries());
        
        await loadFlightData(filters);
        
        Loading.hide();
        Toast.show('Filters applied successfully!', 'success');
    });
    
    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', () => {
        document.getElementById('filterForm').reset();
        loadFlightData();
        Toast.show('Filters cleared!', 'info');
    });
    
    // Export data
    document.getElementById('exportData').addEventListener('click', async () => {
        try {
            Loading.show();
            const csvData = await FlightService.exportData(filteredFlights);
            
            // Create download link
            const blob = new Blob([csvData], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `flight_data_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
            
            Loading.hide();
            Toast.show('Data exported successfully!', 'success');
        } catch (error) {
            Loading.hide();
            Toast.show('Failed to export data', 'error');
        }
    });
}
