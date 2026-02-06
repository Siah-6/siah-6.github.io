/**
 * NutriCoach AI - Main Application Controller
 * Static Frontend Version - Replaces PHP backend with JavaScript
 */

// Global application state
const AppState = {
    currentUser: null,
    isLoggedIn: false,
    currentPage: 'landing',
    onboardingCompleted: false,
    userProfile: null,
    // Sample data for demo purposes
    sampleData: {
        users: [
            {
                id: 1,
                name: 'John Doe',
                email: 'john@example.com',
                password: 'password123',
                xp: 250,
                level: 3,
                created_at: new Date().toISOString()
            }
        ],
        profiles: [
            {
                user_id: 1,
                gender: 'male',
                fitness_goal: 'build_muscle',
                fitness_level: 'intermediate',
                activity_level: 'moderately_active',
                age: 28,
                height: 175,
                height_unit: 'cm',
                weight: 75,
                weight_unit: 'kg',
                daily_calories: 2800,
                protein_grams: 140,
                carbs_grams: 350,
                fats_grams: 90,
                bmi: 24.5,
                onboarding_completed: true
            }
        ],
        workouts: [
            {
                id: 1,
                user_id: 1,
 workout_type: 'Upper Body Strength',
                exercises: [
                    { name: 'Bench Press', sets: 4, reps: 8, weight: 60 },
                    { name: 'Pull-ups', sets: 3, reps: 10, weight: 'bodyweight' },
                    { name: 'Shoulder Press', sets: 3, reps: 12, weight: 20 }
                ],
                completed_exercises: 0,
                total_exercises: 3,
                xp_earned: 0,
                status: 'in_progress'
            }
        ],
        meals: [
            {
                id: 1,
                user_id: 1,
                date: new Date().toISOString().split('T')[0],
                meal_type: 'breakfast',
                meal_name: 'Protein Oatmeal',
                calories: 450,
                protein: 35,
                carbs: 45,
                fats: 12
            }
        ],
        progress: [
            {
                user_id: 1,
                log_date: new Date().toISOString().split('T')[0],
                weight: 75,
                workout_completed: false,
                calories_consumed: 0,
                calories_burned: 0
            }
        ]
    }
};

// Page navigation system
class PageManager {
    static showPage(pageName) {
        // Hide all pages
        document.querySelectorAll('.page').forEach(page => {
            page.style.display = 'none';
        });
        
        // Show requested page
        const targetPage = document.getElementById(pageName + '-page');
        if (targetPage) {
            targetPage.style.display = 'block';
            AppState.currentPage = pageName;
            
            // Initialize page-specific functionality
            this.initializePage(pageName);
        }
    }
    
    static initializePage(pageName) {
        switch(pageName) {
            case 'dashboard':
                DashboardManager.init();
                break;
            case 'onboarding':
                OnboardingManager.init();
                break;
            case 'workout':
                WorkoutManager.init();
                break;
            case 'meals':
                MealManager.init();
                break;
            case 'chat':
                ChatManager.init();
                break;
            case 'profile':
                ProfileManager.init();
                break;
        }
    }
}

// Authentication Manager
class AuthManager {
    static login(email, password) {
        // In real app, this would call an API
        // For demo, accept any credentials and create/use sample user
        const user = AppState.sampleData.users.find(u => u.email === email) || {
            id: Date.now(),
            name: email.split('@')[0],
            email: email,
            password: password,
            xp: 0,
            level: 1,
            created_at: new Date().toISOString()
        };
        
        AppState.currentUser = user;
        AppState.isLoggedIn = true;
        
        // Save to localStorage for persistence
        localStorage.setItem('nutricoach_user', JSON.stringify(user));
        
        // Check if onboarding completed
        const profile = AppState.sampleData.profiles.find(p => p.user_id === user.id);
        AppState.onboardingCompleted = profile ? profile.onboarding_completed : false;
        
        return { success: true, user: user };
    }
    
    static register(name, email, password) {
        // Check if user already exists
        const existingUser = AppState.sampleData.users.find(u => u.email === email);
        if (existingUser) {
            return { success: false, error: 'Email already registered' };
        }
        
        // Create new user
        const newUser = {
            id: Date.now(),
            name: name,
            email: email,
            password: password, // In real app, this would be hashed
            xp: 0,
            level: 1,
            created_at: new Date().toISOString()
        };
        
        AppState.sampleData.users.push(newUser);
        AppState.currentUser = newUser;
        AppState.isLoggedIn = true;
        
        // Save to localStorage
        localStorage.setItem('nutricoach_user', JSON.stringify(newUser));
        
        return { success: true, user: newUser };
    }
    
    static logout() {
        AppState.currentUser = null;
        AppState.isLoggedIn = false;
        AppState.onboardingCompleted = false;
        AppState.userProfile = null;
        
        localStorage.removeItem('nutricoach_user');
        
        // Redirect to landing page
        PageManager.showPage('landing');
    }
    
    static checkAuthStatus() {
        const savedUser = localStorage.getItem('nutricoach_user');
        if (savedUser) {
            AppState.currentUser = JSON.parse(savedUser);
            AppState.isLoggedIn = true;
            
            // Load user profile
            const profile = AppState.sampleData.profiles.find(p => p.user_id === AppState.currentUser.id);
            AppState.userProfile = profile;
            AppState.onboardingCompleted = profile ? profile.onboarding_completed : false;
            
            return true;
        }
        return false;
    }
}

// Dashboard Manager
class DashboardManager {
    static init() {
        if (!AppState.isLoggedIn) {
            PageManager.showPage('landing');
            return;
        }
        
        if (!AppState.onboardingCompleted) {
            PageManager.showPage('onboarding');
            return;
        }
        
        this.loadUserData();
        this.loadProgressData();
        this.loadXPStats();
    }
    
    static loadUserData() {
        const user = AppState.currentUser;
        const profile = AppState.userProfile;
        
        if (user) {
            document.querySelector('.welcome-section h1').textContent = user.name;
        }
        
        if (profile) {
            document.querySelector('[data-stat="calories"]').textContent = profile.daily_calories;
            document.querySelector('[data-stat="protein"]').textContent = profile.protein_grams;
            document.querySelector('[data-stat="bmi"]').textContent = profile.bmi;
        }
    }
    
    static loadProgressData() {
        // Load weight and progress data
        const progressData = JSON.parse(localStorage.getItem('progressData') || '{}');
        
        if (progressData.currentWeight) {
            document.querySelector('#currentWeight .weight-number').textContent = progressData.currentWeight;
        }
        
        if (progressData.goalWeight) {
            document.querySelector('#goalWeight .weight-number').textContent = progressData.goalWeight;
        }
    }
    
    static loadXPStats() {
        const user = AppState.currentUser;
        if (user) {
            document.getElementById('userLevel').textContent = user.level;
            document.getElementById('userXP').textContent = user.xp;
            document.getElementById('totalWorkouts').textContent = AppState.sampleData.workouts.filter(w => w.user_id === user.id).length;
            document.getElementById('totalExercises').textContent = AppState.sampleData.workouts.reduce((total, w) => total + w.total_exercises, 0);
        }
    }
}

// Onboarding Manager
class OnboardingManager {
    static currentStep = 1;
    static totalSteps = 6;
    static formData = {};
    
    static init() {
        this.currentStep = 1;
        this.formData = {};
        this.updateProgress();
        this.showStep(1);
    }
    
    static showStep(stepNumber) {
        // Hide all steps
        document.querySelectorAll('.onboarding-step').forEach(step => {
            step.classList.remove('active');
        });
        
        // Show current step
        const currentStepEl = document.querySelector(`[data-step="${stepNumber}"]`);
        if (currentStepEl) {
            currentStepEl.classList.add('active');
        }
        
        // Update progress
        this.updateProgress();
        
        // Show/hide back button
        const backBtn = document.getElementById('backBtn');
        if (backBtn) {
            backBtn.style.display = stepNumber > 1 ? 'block' : 'none';
        }
    }
    
    static updateProgress() {
        const progressBar = document.getElementById('progressBar');
        const currentStepEl = document.getElementById('currentStep');
        const totalStepsEl = document.getElementById('totalSteps');
        
        if (progressBar) {
            progressBar.style.width = `${(this.currentStep / this.totalSteps) * 100}%`;
        }
        
        if (currentStepEl) currentStepEl.textContent = this.currentStep;
        if (totalStepsEl) totalStepsEl.textContent = this.totalSteps;
    }
    
    static nextStep() {
        // Validate current step
        if (!this.validateCurrentStep()) {
            return;
        }
        
        // Collect form data for current step
        this.collectStepData();
        
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.showStep(this.currentStep);
        } else {
            // Complete onboarding
            this.completeOnboarding();
        }
    }
    
    static prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
        }
    }
    
    static validateCurrentStep() {
        const currentStepEl = document.querySelector(`[data-step="${this.currentStep}"]`);
        const requiredInputs = currentStepEl.querySelectorAll('[required]');
        
        for (let input of requiredInputs) {
            if (!input.value) {
                input.focus();
                this.showError('Please fill in all required fields');
                return false;
            }
        }
        
        return true;
    }
    
    static collectStepData() {
        const currentStepEl = document.querySelector(`[data-step="${this.currentStep}"]`);
        const inputs = currentStepEl.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            if (input.type === 'radio' && input.checked) {
                this.formData[input.name] = input.value;
            } else if (input.type !== 'radio') {
                this.formData[input.name] = input.value;
            }
        });
    }
    
    static completeOnboarding() {
        // Create user profile
        const profile = {
            user_id: AppState.currentUser.id,
            ...this.formData,
            onboarding_completed: true,
            daily_calories: this.calculateCalories(),
            protein_grams: this.calculateProtein(),
            carbs_grams: this.calculateCarbs(),
            fats_grams: this.calculateFats(),
            bmi: this.calculateBMI()
        };
        
        AppState.sampleData.profiles.push(profile);
        AppState.userProfile = profile;
        AppState.onboardingCompleted = true;
        
        // Save to localStorage
        localStorage.setItem('nutricoach_profile', JSON.stringify(profile));
        
        // Redirect to dashboard
        PageManager.showPage('dashboard');
    }
    
    static calculateCalories() {
        // Basic BMR calculation using Mifflin-St Jeor Equation
        const weight = parseFloat(this.formData.weight) || 70;
        const height = parseFloat(this.formData.height) || 170;
        const age = parseInt(this.formData.age) || 30;
        const gender = this.formData.gender || 'male';
        
        let bmr = (10 * weight) + (6.25 * height) - (5 * age);
        if (gender === 'male') bmr += 5;
        else bmr -= 161;
        
        // Activity multiplier
        const activityMultipliers = {
            'sedentary': 1.2,
            'lightly_active': 1.375,
            'moderately_active': 1.55,
            'very_active': 1.725
        };
        
        const multiplier = activityMultipliers[this.formData.activity_level] || 1.55;
        return Math.round(bmr * multiplier);
    }
    
    static calculateProtein() {
        const weight = parseFloat(this.formData.weight) || 70;
        const goal = this.formData.fitness_goal || 'stay_in_shape';
        
        const proteinMultipliers = {
            'build_muscle': 2.2,
            'lose_weight': 2.0,
            'stay_in_shape': 1.6,
            'look_better': 1.8
        };
        
        return Math.round(weight * (proteinMultipliers[goal] || 1.8));
    }
    
    static calculateCarbs() {
        const calories = this.calculateCalories();
        const protein = this.calculateProtein() * 4; // 4 calories per gram
        const fats = this.calculateFats() * 9; // 9 calories per gram
        
        return Math.round((calories - protein - fats) / 4);
    }
    
    static calculateFats() {
        const calories = this.calculateCalories();
        return Math.round(calories * 0.25 / 9); // 25% of calories from fat
    }
    
    static calculateBMI() {
        const weight = parseFloat(this.formData.weight) || 70;
        let height = parseFloat(this.formData.height) || 170;
        
        // Convert to meters if in cm
        if (this.formData.height_unit === 'cm') {
            height = height / 100;
        }
        
        return (weight / (height * height)).toFixed(1);
    }
    
    static showError(message) {
        // Show error message
        const errorEl = document.createElement('div');
        errorEl.className = 'alert alert-error';
        errorEl.textContent = message;
        
        const currentStepEl = document.querySelector(`[data-step="${this.currentStep}"]`);
        currentStepEl.insertBefore(errorEl, currentStepEl.firstChild);
        
        setTimeout(() => {
            errorEl.remove();
        }, 3000);
    }
}

// Modal functions (from original auth-simple.js)
function openLoginModal() {
    document.getElementById('loginModal').style.display = 'block';
}

function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
    document.getElementById('loginError').style.display = 'none';
}

function openSignupModal() {
    document.getElementById('signupModal').style.display = 'block';
}

function closeSignupModal() {
    document.getElementById('signupModal').style.display = 'none';
    document.getElementById('signupError').style.display = 'none';
}

function openForgotPasswordModal() {
    document.getElementById('forgotPasswordModal').style.display = 'block';
}

function closeForgotPasswordModal() {
    document.getElementById('forgotPasswordModal').style.display = 'none';
}

// Initialize application
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication status
    AuthManager.checkAuthStatus();
    
    // Show appropriate page
    if (AppState.isLoggedIn) {
        if (AppState.onboardingCompleted) {
            PageManager.showPage('dashboard');
        } else {
            PageManager.showPage('onboarding');
        }
    } else {
        PageManager.showPage('landing');
    }
    
    // Setup form handlers
    setupFormHandlers();
    
    // Setup modal close handlers
    setupModalHandlers();
});

function setupFormHandlers() {
    // Login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.email.value;
            const password = this.password.value;
            
            const result = AuthManager.login(email, password);
            
            if (result.success) {
                closeLoginModal();
                if (AppState.onboardingCompleted) {
                    PageManager.showPage('dashboard');
                } else {
                    PageManager.showPage('onboarding');
                }
            } else {
                const errorEl = document.getElementById('loginError');
                errorEl.textContent = result.error || 'Login failed';
                errorEl.style.display = 'block';
            }
        });
    }
    
    // Signup form
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = this.name.value;
            const email = this.email.value;
            const password = this.password.value;
            const confirmPassword = this.confirm_password.value;
            
            // Validation
            if (password.length < 8) {
                const errorEl = document.getElementById('signupError');
                errorEl.textContent = 'Password must be at least 8 characters';
                errorEl.style.display = 'block';
                return;
            }
            
            if (password !== confirmPassword) {
                const errorEl = document.getElementById('passwordMatchError');
                errorEl.textContent = 'Passwords do not match';
                errorEl.style.display = 'block';
                return;
            }
            
            const result = AuthManager.register(name, email, password);
            
            if (result.success) {
                closeSignupModal();
                PageManager.showPage('onboarding');
            } else {
                const errorEl = document.getElementById('signupError');
                errorEl.textContent = result.error || 'Registration failed';
                errorEl.style.display = 'block';
            }
        });
    }
    
    // Forgot password form
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.email.value;
            
            // Simulate sending reset email
            alert(`Password reset link would be sent to: ${email}`);
            closeForgotPasswordModal();
            openLoginModal();
        });
    }
}

function setupModalHandlers() {
    // Close modals when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        }
    });
}

// Global functions for onclick handlers
function showPage(pageName) {
    PageManager.showPage(pageName);
}

function nextStep() {
    OnboardingManager.nextStep();
}

function prevStep() {
    OnboardingManager.prevStep();
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        AuthManager.logout();
    }
}

// Mock managers for other features (to be implemented)
const WorkoutManager = {
    init: function() {
        console.log('Workout page initialized');
    }
};

const MealManager = {
    init: function() {
        console.log('Meal page initialized');
    }
};

const ChatManager = {
    init: function() {
        console.log('Chat page initialized');
    }
};

const ProfileManager = {
    init: function() {
        console.log('Profile page initialized');
    }
};
