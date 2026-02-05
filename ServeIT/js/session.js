// Session management for the static version
// Replaces PHP session functionality with localStorage

class SessionManager {
    constructor() {
        this.sessionKey = 'serveit_session';
        this.init();
    }

    init() {
        // Initialize session if it doesn't exist
        if (!localStorage.getItem(this.sessionKey)) {
            this.createSession();
        }
    }

    createSession() {
        const session = {
            userID: '',
            username: '',
            email: '',
            phoneNumber: '',
            birthDate: '',
            role: '',
            profilePicture: '',
            isLoggedIn: false
        };
        localStorage.setItem(this.sessionKey, JSON.stringify(session));
    }

    getSession() {
        const sessionData = localStorage.getItem(this.sessionKey);
        return sessionData ? JSON.parse(sessionData) : null;
    }

    updateSession(userData) {
        const currentSession = this.getSession() || {};
        const updatedSession = { ...currentSession, ...userData, isLoggedIn: true };
        localStorage.setItem(this.sessionKey, JSON.stringify(updatedSession));
    }

    login(username, password) {
        // Mock authentication - any credentials work for demo purposes
        // In real app, this would validate against actual user data
        const users = getUsers();
        const user = users.find(u => 
            (u.username === username || u.email === username || u.phoneNumber === username) && 
            u.password === password
        );

        if (user) {
            this.updateSession({
                userID: user.userID,
                username: user.username,
                email: user.email,
                phoneNumber: user.phoneNumber,
                birthDate: user.birthDate,
                role: user.role,
                profilePicture: user.profilePicture
            });
            return { success: true, user };
        } else {
            // For demo purposes, allow any login
            const mockUser = {
                userID: 999,
                username: username,
                email: username + '@example.com',
                phoneNumber: '09123456789',
                birthDate: '1990-01-01',
                role: 'user',
                profilePicture: 'default.png'
            };
            
            this.updateSession(mockUser);
            return { success: true, user: mockUser };
        }
    }

    register(username, email, password, phoneNumber) {
        // Check if user already exists
        const users = getUsers();
        const existingUser = users.find(u => 
            u.username === username || u.email === email || u.phoneNumber === phoneNumber
        );

        if (existingUser) {
            return { success: false, error: 'Username, email, or phone number already exists.' };
        }

        // Create new user (in real app, this would be saved to database)
        const newUser = {
            userID: users.length + 1,
            username: username,
            email: email,
            phoneNumber: phoneNumber,
            password: password,
            role: 'user',
            birthDate: '',
            profilePicture: 'default.png'
        };

        // For demo purposes, we'll just log the user in immediately
        this.updateSession(newUser);
        return { success: true, user: newUser };
    }

    logout() {
        this.createSession();
        window.location.href = 'index.html';
    }

    isLoggedIn() {
        const session = this.getSession();
        return session && session.isLoggedIn;
    }

    getCurrentUser() {
        const session = this.getSession();
        return session && session.isLoggedIn ? session : null;
    }

    isAdmin() {
        const session = this.getSession();
        return session && session.isLoggedIn && session.role === 'admin';
    }

    requireAuth() {
        if (!this.isLoggedIn()) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    }

    requireAdmin() {
        if (!this.isAdmin()) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    }
}

// Global session manager instance
const sessionManager = new SessionManager();

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SessionManager;
}
