# MESY - Event Management System (Static Front-End Version)

A fully front-end, static HTML/CSS/JavaScript version of the MESY Event Management System, converted from the original PHP/MySQL backend for portfolio demonstration purposes.

## 🚀 Features

### ✅ Fully Functional Front-End
- **Complete UI/UX Preservation**: All original design elements, styling, and interactions maintained exactly
- **No Backend Required**: Runs entirely in the browser with JavaScript
- **Local Storage**: Simulates database operations and user sessions
- **Responsive Design**: Works on all devices (desktop, tablet, mobile)

### 🎯 Core Features
- **Event Discovery**: Browse and search upcoming events with filters
- **User Authentication**: Login/registration with simulated session management
- **Event Management**: Create, edit, and manage events (organizer dashboard)
- **Event Registration**: Attend events with detailed registration forms
- **Archived Events**: View past events and statistics
- **Event Details**: Comprehensive event information pages

### 🛠 Technical Implementation
- **PHP Logic Converted**: All backend PHP logic rewritten in JavaScript
- **Mock Data Layer**: Sample data arrays replace database queries
- **Session Management**: Browser-based session simulation
- **Form Validation**: Client-side validation with error handling
- **Dynamic Content**: JavaScript renders all dynamic page content

## 📁 Project Structure

```
adet-grp-9/
├── index.html                 # Main events listing page
├── login.html                 # User login page
├── signup.html                # User registration page
├── eventInfoPage.html         # Event details page
├── attendEventsForm.html      # Event registration form
├── privacyAndPolicy.html      # Privacy policy page
├── organizers/                # Organizer dashboard
│   ├── index.html            # Organizer events listing
│   ├── eventForm.html        # Create new event
│   ├── myEvents.html         # Manage my events
│   └── archivedEvent.html    # View archived events
├── assets/
│   ├── css/                  # Stylesheets
│   │   ├── eventList.css     # Main event listing styles
│   │   ├── login.css         # Login page styles
│   │   └── signup.css        # Signup page styles
│   ├── js/                   # JavaScript files
│   │   ├── data.js           # Mock data and session management
│   │   └── eventList.js      # Event listing functionality
│   ├── img/                  # Images and assets
│   └── font/                 # Custom fonts
└── README.md                  # This file
```

## 🎨 Design & UI

The project maintains the original design system:
- **Color Scheme**: Orange primary (#FE5722) with complementary colors
- **Typography**: Inter font family with custom Belove font for headings
- **Layout**: Bootstrap 5 grid system with custom CSS
- **Icons**: Font Awesome 6 for consistent iconography
- **Responsive**: Mobile-first responsive design

## 🔧 How It Works

### Data Layer (`assets/js/data.js`)
- **Sample Events**: Pre-populated array of sample events
- **User Management**: Mock user data and authentication
- **Session Manager**: Browser-based session handling using localStorage
- **Archived Events**: Sample past events data

### Authentication System
- **Any Credentials Work**: For demo purposes, any email/password combination works
- **Session Storage**: User sessions stored in browser localStorage
- **Auto-Redirect**: Protected pages redirect to login if not authenticated
- **Session Persistence**: Sessions persist across browser refreshes

### Event Management
- **CRUD Operations**: Create, read, update, delete events (simulated)
- **Search & Filter**: Real-time search and location filtering
- **Event Cards**: Dynamic rendering of event information
- **Statistics**: Calculated metrics for dashboard displays

## 🚀 Quick Start

### Prerequisites
- Modern web browser (Chrome, Firefox, Safari, Edge)
- No server or database required

### Running the Application

1. **Download or Clone** the project files
2. **Open `index.html`** in your web browser
3. **Navigate** through all features using the navigation menu

That's it! The application is ready to use immediately.

### First Time Setup

1. **Browse Events**: View the main events listing on the homepage
2. **Create Account**: Click "Sign Up" to create a new account (any credentials work)
3. **Login**: Use your credentials to access the organizer dashboard
4. **Create Events**: Navigate to "Create Event" in the organizer dashboard
5. **Manage Events**: View and manage your events in "My Events"

## 🎯 Demo Features

### Guest Features
- Browse upcoming events
- Search and filter events
- View event details
- Register for events
- View privacy policy

### Organizer Features
- Create new events
- Edit existing events
- View event statistics
- Manage registrations
- View archived events
- Dashboard analytics

## 🔄 PHP to JavaScript Conversion

### Original PHP Features → JavaScript Implementation

| PHP Feature | JavaScript Implementation |
|------------|-------------------------|
| `session_start()` | `SessionManager` class with localStorage |
| `mysqli_query()` | Array filtering and manipulation |
| `$_POST` handling | Form event listeners |
| `header()` redirects | `window.location.href` |
| `password_verify()` | Simple boolean validation (demo) |
| `include` statements | Inline HTML/JavaScript |

### Key Conversions

1. **Database Queries → Array Operations**
   ```php
   // PHP
   $events = mysqli_query($conn, "SELECT * FROM events");
   
   // JavaScript
   const events = sampleEvents.filter(event => event.condition);
   ```

2. **Session Management → localStorage**
   ```php
   // PHP
   $_SESSION['user'] = $userData;
   
   // JavaScript
   localStorage.setItem('userSession', JSON.stringify(sessionData));
   ```

3. **Form Processing → Event Listeners**
   ```php
   // PHP
   if (isset($_POST['submit'])) { ... }
   
   // JavaScript
   form.addEventListener('submit', function(e) { ... });
   ```

## 🎨 Customization

### Adding Sample Events
Edit `assets/js/data.js` and add new events to the `sampleEvents` array:

```javascript
{
    id: 999,
    eventName: "Your Event Name",
    category: "Technology",
    description: "Event description...",
    eventDate: "2024-12-31",
    startTime: "14:00",
    endTime: "17:00",
    location: "Your City",
    organizerName: "Your Name",
    organizerId: 1,
    eventImage: null
}
```

### Changing Colors
Edit `assets/css/eventList.css` and modify CSS variables:

```css
:root {
    --primary: #FE5722;    /* Change primary color */
    --primary-light: #FF7043;
    --primary-dark: #E64A19;
}
```

### Adding New Pages
1. Create new HTML file in root or `organizers/` directory
2. Include navigation header and footer
3. Add JavaScript functionality
4. Update navigation links

## 🌐 Browser Compatibility

- **Chrome**: Full support
- **Firefox**: Full support  
- **Safari**: Full support
- **Edge**: Full support
- **Mobile Browsers**: Full responsive support

## 🔒 Security Notes

This is a **demo/portfolio version** with simplified security:
- **No Real Authentication**: Any credentials work for demo purposes
- **Client-Side Only**: No backend security measures
- **Local Storage**: Data stored in browser (not secure for production)
- **No Input Sanitization**: Simplified validation (not production-ready)

For production use, implement proper backend security, server-side validation, and secure authentication.

## 📱 Mobile Responsiveness

The application is fully responsive:
- **Mobile (< 768px)**: Single column layout, stacked navigation
- **Tablet (768px - 1024px)**: Optimized grid layouts
- **Desktop (> 1024px)**: Full multi-column layout

## 🎯 Portfolio Demonstration Points

### Technical Skills Demonstrated
- **Front-End Development**: HTML5, CSS3, JavaScript ES6+
- **Framework Usage**: Bootstrap 5, Font Awesome 6
- **Responsive Design**: Mobile-first approach
- **JavaScript Architecture**: Modular code organization
- **State Management**: Client-side session handling
- **Form Validation**: Client-side validation patterns
- **DOM Manipulation**: Dynamic content rendering

### Problem-Solving Skills
- **Backend Logic Conversion**: Translating PHP to JavaScript
- **Data Structure Design**: Mock database implementation
- **User Experience**: Maintaining original UI/UX
- **Performance**: Efficient client-side operations

## 🤝 Contributing

This is a portfolio project demonstrating front-end development skills. For suggestions or improvements:

1. **Fork** the repository
2. **Create** a feature branch
3. **Make** your changes
4. **Submit** a pull request

## 📄 License

This project is for portfolio and educational purposes. Original design and functionality preserved for demonstration.

## 🙏 Acknowledgments

- **Original PHP Project**: Base functionality and design
- **Bootstrap**: UI framework
- **Font Awesome**: Icon library
- **Google Fonts**: Typography
- **Original Developers**: For the PHP backend implementation

---

**Note**: This is a static front-end demonstration. For production use, implement proper backend services, database integration, and security measures.