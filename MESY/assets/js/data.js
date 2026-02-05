// Sample data for the static front-end version
// This replaces the PHP database queries with JavaScript arrays

// Sample events data (replaces PHP events array from database)
const sampleEvents = [
    {
        id: 1,
        eventName: "TechChika with Campus DEVCON",
        category: "Technology",
        description: "Join us for an exciting tech talk and networking event featuring the latest innovations in web development and AI. Learn from industry experts and connect with fellow developers.",
        eventDate: "2024-12-15",
        startTime: "14:00",
        endTime: "17:00",
        location: "Manila",
        organizerName: "Campus DEVCON",
        organizerId: 1,
        eventImage: "Summer.jpg",
        latitude: 14.5995,
        longitude: 120.9842,
        ageRequirement: "Open to All Ages",
        numberOfAttendees: 100
    },
    {
        id: 2,
        eventName: "Annual Music Festival",
        category: "Concerts & Music",
        description: "Experience the best local and international artists in this spectacular music festival. Food trucks, art installations, and unforgettable performances await you.",
        eventDate: "2024-12-20",
        startTime: "16:00",
        endTime: "23:00",
        location: "Quezon City",
        organizerName: "Music Events PH",
        organizerId: 2,
        eventImage: "festival.jpg",
        latitude: 14.6760,
        longitude: 121.0437,
        ageRequirement: "Open to All Ages",
        numberOfAttendees: 500
    },
    {
        id: 3,
        eventName: "Startup Pitch Night",
        category: "Conferences & Seminars",
        description: "Watch innovative startups pitch their ideas to investors and judges. Network with entrepreneurs and discover the next big thing in business.",
        eventDate: "2024-12-18",
        startTime: "18:00",
        endTime: "21:00",
        location: "Makati",
        organizerName: "Startup Hub PH",
        organizerId: 3,
        eventImage: "roblox.jpg",
        latitude: 14.5547,
        longitude: 121.0244,
        ageRequirement: "18 - 24",
        numberOfAttendees: 150
    },
    {
        id: 4,
        eventName: "Food & Wine Expo",
        category: "Festivals & Fairs",
        description: "Taste the finest cuisines and wines from local and international vendors. Cooking demonstrations, wine tasting, and culinary workshops available.",
        eventDate: "2024-12-22",
        startTime: "10:00",
        endTime: "20:00",
        location: "BGC Taguig",
        organizerName: "Foodie Events",
        organizerId: 4,
        eventImage: "Summer.jpg",
        latitude: 14.5398,
        longitude: 121.0542,
        ageRequirement: "Open to All Ages",
        numberOfAttendees: 300
    },
    {
        id: 5,
        eventName: "Digital Marketing Workshop",
        category: "Workshops & Training",
        description: "Learn the latest digital marketing strategies from industry experts. Hands-on workshops on SEO, social media marketing, and content creation.",
        eventDate: "2024-12-25",
        startTime: "09:00",
        endTime: "17:00",
        location: "Pasig",
        organizerName: "Marketing Academy",
        organizerId: 5,
        eventImage: "festival.jpg",
        latitude: 14.5764,
        longitude: 121.0851,
        ageRequirement: "18 - 24",
        numberOfAttendees: 50
    },
    {
        id: 6,
        eventName: "Fitness & Wellness Fair",
        category: "Sports & Fitness",
        description: "Join us for a day of fitness activities, wellness talks, and health screenings. Yoga sessions, nutrition workshops, and fitness challenges.",
        eventDate: "2024-12-28",
        startTime: "07:00",
        endTime: "15:00",
        location: "Alabang",
        organizerName: "FitLife PH",
        organizerId: 6,
        eventImage: "roblox.jpg",
        latitude: 14.4245,
        longitude: 121.0285,
        ageRequirement: "Open to All Ages",
        numberOfAttendees: 200
    },
    {
        id: 7,
        eventName: "Art Gallery Opening",
        category: "Festivals & Fairs",
        description: "Celebrate the opening of a contemporary art exhibition featuring local artists. Wine reception, artist talks, and guided tours available.",
        eventDate: "2025-01-05",
        startTime: "18:00",
        endTime: "21:00",
        location: "Makati",
        organizerName: "Art Space Manila",
        organizerId: 7,
        eventImage: "Summer.jpg",
        latitude: 14.5547,
        longitude: 121.0244,
        ageRequirement: "Open to All Ages",
        numberOfAttendees: 100
    },
    {
        id: 8,
        eventName: "Gaming Tournament",
        category: "Conferences & Seminars",
        description: "Compete in the ultimate gaming tournament with prizes worth ₱50,000. Multiple game categories including FPS, MOBA, and fighting games.",
        eventDate: "2025-01-08",
        startTime: "10:00",
        endTime: "22:00",
        location: "Quezon City",
        organizerName: "Game Masters PH",
        organizerId: 8,
        eventImage: "festival.jpg",
        latitude: 14.6760,
        longitude: 121.0437,
        ageRequirement: "13 - 17",
        numberOfAttendees: 250
    }
];

// Sample users data (replaces PHP organizers table)
const sampleUsers = [
    {
        organizerId: 1,
        username: "campusdevcon",
        email: "info@campusdevcon.ph",
        password: "hashed_password_1"
    },
    {
        organizerId: 2,
        username: "musicevents",
        email: "events@musicevents.ph",
        password: "hashed_password_2"
    },
    {
        organizerId: 3,
        username: "startuphub",
        email: "hello@startuphub.ph",
        password: "hashed_password_3"
    }
];

// Sample archived events
const sampleArchivedEvents = [
    {
        id: 101,
        eventName: "Summer Coding Bootcamp 2024",
        category: "Education",
        description: "Intensive 3-week coding bootcamp covering web development, mobile apps, and cloud computing.",
        eventDate: "2024-06-15",
        startTime: "09:00",
        endTime: "17:00",
        location: "Manila",
        organizerName: "Code Academy PH",
        organizerId: 1,
        eventImage: "bootcamp2024.jpg"
    },
    {
        id: 102,
        eventName: "Jazz Night Under the Stars",
        category: "Music",
        description: "An evening of smooth jazz featuring renowned local and international jazz artists.",
        eventDate: "2024-08-20",
        startTime: "19:00",
        endTime: "23:00",
        location: "Alabang",
        organizerName: "Jazz Society PH",
        organizerId: 2,
        eventImage: "jazznight.jpg"
    }
];

// Front-end session management (replaces PHP sessions)
class SessionManager {
    constructor() {
        this.currentUser = null;
        this.isLoggedIn = false;
        this.loadSession();
    }

    loadSession() {
        const sessionData = localStorage.getItem('userSession');
        if (sessionData) {
            const session = JSON.parse(sessionData);
            this.currentUser = session.user;
            this.isLoggedIn = true;
        }
    }

    login(email, password) {
        // In a real app, this would validate against a backend
        // For demo purposes, any credentials work
        const user = {
            organizerId: Math.floor(Math.random() * 1000) + 1,
            username: email.split('@')[0],
            email: email,
            name: email.split('@')[0].charAt(0).toUpperCase() + email.split('@')[0].slice(1)
        };
        
        this.currentUser = user;
        this.isLoggedIn = true;
        
        // Save to localStorage
        localStorage.setItem('userSession', JSON.stringify({
            user: user,
            timestamp: Date.now()
        }));
        
        return true;
    }

    logout() {
        this.currentUser = null;
        this.isLoggedIn = false;
        localStorage.removeItem('userSession');
    }

    requireAuth() {
        if (!this.isLoggedIn) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    }
}

// Global session instance
const session = new SessionManager();

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        sampleEvents,
        sampleUsers,
        sampleArchivedEvents,
        SessionManager,
        session
    };
}
