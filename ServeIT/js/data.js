// Mock data to replace PHP database queries
// This file contains all the sample data that would normally come from the database

// Mock users data
const mockUsers = [
    {
        userID: 1,
        username: "admin",
        email: "admin@serveit.com",
        phoneNumber: "09123456789",
        password: "admin123",
        role: "admin",
        birthDate: "1990-01-01",
        profilePicture: "default.png"
    },
    {
        userID: 2,
        username: "johndoe",
        email: "john@example.com",
        phoneNumber: "09123456780",
        password: "password123",
        role: "user",
        birthDate: "1995-05-15",
        profilePicture: "default.png"
    },
    {
        userID: 3,
        username: "janesmith",
        email: "jane@example.com",
        phoneNumber: "09123456781",
        password: "password123",
        role: "user",
        birthDate: "1992-08-20",
        profilePicture: "default.png"
    }
];

// Mock products data
const mockProducts = [
    {
        itemID: 1,
        title: "E-commerce Website Template",
        shortDescription: "Modern responsive e-commerce template with shopping cart",
        description: "A fully functional e-commerce website template with modern design, responsive layout, shopping cart functionality, payment integration, and admin panel. Perfect for online stores selling physical or digital products.",
        price: 8999,
        type: "product",
        categoryName: "Templates",
        attachment: "0001.jpg"
    },
    {
        itemID: 2,
        title: "Portfolio Website Template",
        shortDescription: "Creative portfolio template for professionals",
        description: "Stunning portfolio website template perfect for designers, photographers, artists, and creative professionals. Features smooth animations, image galleries, project showcases, and contact forms.",
        price: 5999,
        type: "product",
        categoryName: "Templates",
        attachment: "0002.jpg"
    },
    {
        itemID: 3,
        title: "CRM Dashboard Template",
        shortDescription: "Professional CRM dashboard with analytics",
        description: "Comprehensive CRM dashboard template with customer management, sales tracking, analytics, reporting, and team collaboration features. Built with modern technologies and best practices.",
        price: 12999,
        type: "product",
        categoryName: "Templates",
        attachment: "0003.jpg"
    },
    {
        itemID: 4,
        title: "Social Media App Template",
        shortDescription: "Complete social media application template",
        description: "Full-featured social media app template with user profiles, posts, comments, likes, messaging, notifications, and real-time updates. Perfect for social networking platforms.",
        price: 15999,
        type: "product",
        categoryName: "Templates",
        attachment: "0004.jpg"
    },
    {
        itemID: 5,
        title: "Blog Website Template",
        shortDescription: "Clean and modern blog template",
        description: "Elegant blog website template with article management, categories, tags, comments, search functionality, and SEO optimization. Great for personal blogs and content websites.",
        price: 4999,
        type: "product",
        categoryName: "Templates",
        attachment: "0005.jpg"
    },
    {
        itemID: 6,
        title: "Restaurant Website Template",
        shortDescription: "Professional restaurant website with ordering",
        description: "Complete restaurant website template with menu display, online ordering, table reservations, customer reviews, and location maps. Ideal for restaurants and food businesses.",
        price: 7999,
        type: "product",
        categoryName: "Templates",
        attachment: "0006.jpg"
    },
    {
        itemID: 7,
        title: "Learning Management System",
        shortDescription: "Complete LMS template for online education",
        description: "Comprehensive learning management system template with course management, student enrollment, progress tracking, quizzes, and certificates. Perfect for online education platforms.",
        price: 18999,
        type: "product",
        categoryName: "Templates",
        attachment: "0007.jpg"
    },
    {
        itemID: 8,
        title: "Fitness App Template",
        shortDescription: "Mobile fitness tracking application",
        description: "Feature-rich fitness app template with workout tracking, nutrition planning, progress monitoring, and social features. Great for fitness enthusiasts and trainers.",
        price: 9999,
        type: "product",
        categoryName: "Templates",
        attachment: "0008.jpg"
    },
    {
        itemID: 9,
        title: "Real Estate Website Template",
        shortDescription: "Professional real estate platform",
        description: "Complete real estate website template with property listings, search filters, virtual tours, agent profiles, and contact forms. Perfect for real estate agencies.",
        price: 10999,
        type: "product",
        categoryName: "Templates",
        attachment: "0009.jpg"
    },
    {
        itemID: 10,
        title: "Travel Booking Platform",
        shortDescription: "Complete travel booking website",
        description: "Full-featured travel booking platform with hotel listings, flight search, booking management, payment processing, and review system. Ideal for travel agencies.",
        price: 14999,
        type: "product",
        categoryName: "Templates",
        attachment: "0010.jpg"
    }
];

// Mock services data
const mockServices = [
    {
        itemID: 11,
        title: "Web Development",
        shortDescription: "Custom website development",
        description: "Full-stack web development services using modern technologies like React, Node.js, and cloud platforms. We create responsive, fast, and secure websites tailored to your business needs.",
        price: 25000,
        type: "service",
        categoryName: "Development",
        attachment: "0011.jpg"
    },
    {
        itemID: 12,
        title: "Mobile App Development",
        shortDescription: "iOS and Android app development",
        description: "Native and cross-platform mobile app development using React Native, Flutter, and native technologies. We create engaging mobile experiences for iOS and Android platforms.",
        price: 35000,
        type: "service",
        categoryName: "Development",
        attachment: "0012.jpg"
    },
    {
        itemID: 13,
        title: "UI/UX Design",
        shortDescription: "User interface and experience design",
        description: "Professional UI/UX design services including wireframing, prototyping, user research, and visual design. We create intuitive and beautiful digital experiences.",
        price: 15000,
        type: "service",
        categoryName: "Design",
        attachment: "0013.jpg"
    },
    {
        itemID: 14,
        title: "Digital Marketing",
        shortDescription: "Online marketing and SEO services",
        description: "Comprehensive digital marketing services including SEO, social media marketing, content marketing, and paid advertising to grow your online presence.",
        price: 20000,
        type: "service",
        categoryName: "Marketing",
        attachment: "0014.jpg"
    },
    {
        itemID: 15,
        title: "Database Design",
        shortDescription: "Database architecture and optimization",
        description: "Expert database design, optimization, and management services. We work with SQL and NoSQL databases to ensure data integrity and performance.",
        price: 18000,
        type: "service",
        categoryName: "Development",
        attachment: "0015.jpg"
    },
    {
        itemID: 16,
        title: "Cloud Services",
        shortDescription: "Cloud deployment and management",
        description: "Complete cloud services including AWS, Azure, and Google Cloud deployment, server management, and infrastructure optimization for scalability.",
        price: 22000,
        type: "service",
        categoryName: "Development",
        attachment: "0016.jpg"
    }
];

// Mock transactions data
const mockTransactions = [
    {
        transactionID: 1,
        userID: 2,
        itemID: 1,
        amount: 2999,
        date: "2024-01-15",
        status: "completed"
    },
    {
        transactionID: 2,
        userID: 3,
        itemID: 5,
        amount: 15000,
        date: "2024-01-20",
        status: "completed"
    },
    {
        transactionID: 3,
        userID: 2,
        itemID: 2,
        amount: 5999,
        date: "2024-01-25",
        status: "pending"
    }
];

// Mock feedback data
const mockFeedbacks = [
    {
        feedbackID: 1,
        userID: 2,
        itemID: 1,
        rating: 5,
        comment: "Excellent web template! Very easy to customize.",
        date: "2024-01-16"
    },
    {
        feedbackID: 2,
        userID: 3,
        itemID: 5,
        rating: 4,
        comment: "Great web development service. Delivered on time.",
        date: "2024-01-21"
    },
    {
        feedbackID: 3,
        userID: 1,
        itemID: 11,
        rating: 5,
        comment: "Amazing web development service! They delivered exactly what I needed.",
        date: "2024-01-25"
    },
    {
        feedbackID: 4,
        userID: 2,
        itemID: 11,
        rating: 4,
        comment: "Professional team and great communication throughout the project.",
        date: "2024-01-28"
    },
    {
        feedbackID: 5,
        userID: 3,
        itemID: 12,
        rating: 5,
        comment: "Best mobile app development service I've used. Highly recommended!",
        date: "2024-02-01"
    },
    {
        feedbackID: 6,
        userID: 1,
        itemID: 13,
        rating: 4,
        comment: "Good UI/UX design work. Very creative and modern approach.",
        date: "2024-02-05"
    },
    {
        feedbackID: 7,
        userID: 4,
        itemID: 14,
        rating: 5,
        comment: "Excellent SEO service. Our website ranking improved significantly.",
        date: "2024-02-10"
    },
    {
        feedbackID: 8,
        userID: 2,
        itemID: 15,
        rating: 4,
        comment: "Professional content creation. Well-written and engaging content.",
        date: "2024-02-15"
    }
];

// Mock FAQ data (from original PHP)
const mockFAQs = [
    {
        category: "Products",
        question: "How do I purchase a product?",
        answer: "To purchase a product, simply browse our products page, select the item you want, and click 'Add to Cart'. Then proceed to checkout to complete your purchase."
    },
    {
        category: "Products",
        question: "Can I get a refund?",
        answer: "Yes, we offer a 30-day money-back guarantee on all digital products. If you're not satisfied, contact our support team for a refund."
    },
    {
        category: "Products",
        question: "Are the products downloadable?",
        answer: "Yes, all our digital products are available for instant download after purchase. You'll receive a download link via email."
    },
    {
        category: "Accounts",
        question: "How do I create an account?",
        answer: "Click the 'Sign Up' button on our homepage, fill in your details, and verify your email. Your account will be created instantly."
    },
    {
        category: "Accounts",
        question: "How do I reset my password?",
        answer: "Click 'Forgot Password' on the login page, enter your email address, and follow the instructions sent to your email to reset your password."
    },
    {
        category: "Accounts",
        question: "Can I change my username?",
        answer: "Yes, you can change your username by going to your profile settings and updating your account information."
    },
    {
        category: "Payments",
        question: "What payment methods do you accept?",
        answer: "We accept all major credit cards, PayPal, and bank transfers. All payments are processed securely through our payment gateway."
    },
    {
        category: "Payments",
        question: "Is my payment information secure?",
        answer: "Yes, we use industry-standard SSL encryption to protect your payment information. Your data is never stored on our servers."
    },
    {
        category: "Payments",
        question: "Can I get an invoice?",
        answer: "Yes, you can download an invoice for any purchase from your account dashboard under 'Order History'."
    },
    {
        category: "Services",
        question: "How do I book a service?",
        answer: "Browse our services page, select the service you need, and click 'Book Now'. Fill in the required details and our team will contact you."
    },
    {
        category: "Services",
        question: "Can I cancel a service booking?",
        answer: "Yes, you can cancel a service booking up to 24 hours before the scheduled time without any charges."
    },
    {
        category: "Services",
        question: "Do you offer custom services?",
        answer: "Yes, we offer custom solutions tailored to your specific needs. Contact our team to discuss your requirements."
    }
];

// Mock team members data (from original PHP)
const mockTeamMembers = [
    {
        id: 1,
        name: "Aguilar, Kissy",
        role: "Full Stack Developer",
        description: "Expert in React, Node.js, and cloud architecture with 5+ years of experience.",
        image: "dev1.png",
        portfolio: "https://kissyaguilar.github.io/"
    },
    {
        id: 2,
        name: "Apacionado, Jon Josiah",
        role: "Backend Developer",
        description: "Specializes in API development, database design, and system architecture.",
        image: "dev2.png",
        portfolio: "https://siah-6.github.io/"
    },
    {
        id: 3,
        name: "Barqueros, Scharizze Ann",
        role: "UI/UX Designer",
        description: "Creative designer specializing in user experience and modern design principles.",
        image: "dev3.png",
        portfolio: "https://scharizzeannbarqueros.github.io/"
    },
    {
        id: 4,
        name: "Endaya, Allen Benedict",
        role: "Frontend Developer",
        description: "Passionate about creating responsive and interactive web applications.",
        image: "dev4.jpg",
        portfolio: "https://onlyteyl.github.io/"
    },
    {
        id: 5,
        name: "Idanan, Mark Tristan",
        role: "DevOps Engineer",
        description: "Expert in cloud deployment, CI/CD pipelines, and infrastructure management.",
        image: "dev5.png",
        portfolio: "https://marktristan25.github.io/"
    },
    {
        id: 6,
        name: "Malabag, Zielco Cloei",
        role: "Product Manager",
        description: "Experienced in product strategy, user research, and agile methodologies.",
        image: "dev6.png",
        portfolio: "https://zielcocloei.github.io/"
    },
    {
        id: 7,
        name: "Mauricio, Brandon Areej",
        role: "Mobile Developer",
        description: "Specializes in iOS and Android app development with React Native.",
        image: "dev7.png",
        portfolio: "https://brandon-adm.github.io/"
    },
    {
        id: 8,
        name: "Melitante, Daniel",
        role: "QA Engineer",
        description: "Dedicated to ensuring quality through comprehensive testing and automation.",
        image: "dev8.jpg",
        portfolio: "https://le1nad-prog.github.io/"
    },
    {
        id: 9,
        name: "Milorin, Ken Aeron",
        role: "Data Scientist",
        description: "Expert in machine learning, data analysis, and predictive modeling.",
        image: "dev9.png",
        portfolio: "https://karmken.github.io/"
    }
];

// Mock chat data
const mockChats = [
    {
        chatID: 1,
        senderID: 2,
        receiverID: 1,
        message: "Hello, I need help with my order.",
        timestamp: "2024-01-25 10:30:00",
        isRead: false
    },
    {
        chatID: 2,
        senderID: 1,
        receiverID: 2,
        message: "Sure! How can I help you today?",
        timestamp: "2024-01-25 10:35:00",
        isRead: true
    }
];

// Helper functions to get data
function getUsers() {
    return mockUsers;
}

function getProducts() {
    return mockProducts;
}

function getServices() {
    return mockServices;
}

function getTransactions() {
    return mockTransactions;
}

function getFeedbacks() {
    return mockFeedbacks;
}

function getFAQs() {
    return mockFAQs;
}

function getTeamMembers() {
    return mockTeamMembers;
}

function getChats() {
    return mockChats;
}

// Get counts (replaces PHP count queries)
function getUserCount() {
    return mockUsers.length;
}

function getTransactionCount() {
    return mockTransactions.length;
}

function getProductCount() {
    return mockProducts.length;
}

// Get new arrivals (latest 4 products)
function getNewArrivals() {
    return mockProducts.slice(-4).reverse();
}

// Get specific product/service by ID
function getItemById(itemID) {
    const allItems = [...mockProducts, ...mockServices];
    return allItems.find(item => item.itemID == itemID);
}

// Get user by ID
function getUserById(userID) {
    return mockUsers.find(user => user.userID == userID);
}

// Search functionality
function searchItems(searchTerm, type = null, category = null) {
    let allItems = [...mockProducts, ...mockServices];
    
    if (type) {
        allItems = allItems.filter(item => item.type === type);
    }
    
    if (category) {
        allItems = allItems.filter(item => item.categoryName === category);
    }
    
    if (searchTerm) {
        const term = searchTerm.toLowerCase();
        allItems = allItems.filter(item => 
            item.title.toLowerCase().includes(term) ||
            item.shortDescription.toLowerCase().includes(term) ||
            item.categoryName.toLowerCase().includes(term)
        );
    }
    
    return allItems;
}

// Get distinct categories
function getCategories(type = null) {
    let items = [...mockProducts, ...mockServices];
    
    if (type) {
        items = items.filter(item => item.type === type);
    }
    
    const categories = [...new Set(items.map(item => item.categoryName))];
    return categories;
}
