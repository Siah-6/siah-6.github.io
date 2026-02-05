// Main JavaScript file for the static ServeIT website
// This file handles the dynamic content loading that was previously done by PHP

// Load navigation based on login status
function loadNavigation() {
    const navContainer = document.getElementById('nav-container');
    if (!navContainer) return;

    const currentUser = sessionManager.getCurrentUser();
    console.log('Current user in loadNavigation:', currentUser); // Debug log
    
    if (currentUser && currentUser.isLoggedIn) {
        // Load logged-in navigation
        navContainer.innerHTML = `
            <nav class="navbar navbar-expand-lg ">
                <div class="container nav-bar-style">
                    <a class="navbar-brand mx-5" href="index.html"><img src="assets/images/nav/logo-nav.png" style="width: 50px; height: 45px;"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav mx-auto flex-grow-1 justify-content-center">
                            <li class="nav-item mx-3">
                                <a class="nav-link active1" aria-current="page" href="index.html">Home</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active2" href="services.html">Services</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active3" href="products.html">Products</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active4" href="about.html">About</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active5" href="helpCenter.html">Help center</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto mx-5">
                            <li class="nav-item dropdown mx-3">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Hi, ${currentUser.username}<i class="fa-regular fa-user p-2"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <div class="nav-account-title">
                                        ACCOUNT
                                    </div>
                                    <li><a class="dropdown-item" href="profile.html"><i class="fa-solid fa-user nav-icons"></i>Profile</a></li>
                                    <li><a class="dropdown-item" href="cart.html"><i class="fa-solid fa-cart-shopping nav-icons"></i>Cart</a></li>
                                    <li><a class="dropdown-item" href="chats.html"><i class="fa-solid fa-message nav-icons"></i>Message Us</a></li>
                                    <li><button class="dropdown-item dark-mode-toggle" onclick="toggleDarkMode()" id="dark-mode-toggle"><i id="mode-icon" class="fa-solid fa-moon"></i> Dark Mode</button></li>
                                    <li><a class="dropdown-item" href="#" onclick="sessionManager.logout()"><i class="fa-solid fa-right-from-bracket nav-icons"></i>Log-out</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <script src="sharedAssets/darkmode.js"></script>
        `;
    } else {
        // Load register navigation
        navContainer.innerHTML = `
            <nav class="navbar navbar-expand-lg ">
                <div class="container nav-bar-style">
                    <a class="navbar-brand mx-5" href="index.html"><img src="assets/images/nav/logo-nav.png"
                        style="width: 50px; height: 45px;"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav mx-auto flex-grow-1 justify-content-center">
                            <li class="nav-item mx-3">
                                <a class="nav-link active1" aria-current="page" href="index.html">Home</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active2" href="services.html">Services</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active3" href="products.html">Products</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active4" href="about.html">About</a>
                            </li>
                            <li class="nav-item mx-3">
                                <a class="nav-link active5" href="helpCenter.html">Help center</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto mx-5">
                            <a href="login.html"><button class="btn btn-primary log-in-btn mx-2 my-2">
                                Log-in / Sign-in
                            </button></a>
                        </ul>
                    </div>
                </div>
            </nav>
        `;
    }
}

// Load footer
function loadFooter() {
    const footerContainer = document.getElementById('footer-container');
    if (!footerContainer) return;

    const currentUser = sessionManager.getCurrentUser();
    
    footerContainer.innerHTML = `
        <!-- help -->
        <div class="container-fluid help-section" style="margin: 0px; padding: 20px 0;">
            <div class="help-title text-center mb-3" style="font-size: 1.5rem; font-weight: bold;">
                HOW CAN WE HELP?
            </div>
            <div class="container d-flex justify-content-center align-items-center">
                ${currentUser && currentUser.isLoggedIn ? `
                    <!-- Show for logged-in users -->
                    <form onsubmit="sendHelpMessage(event)" class="d-flex align-items-center" style="max-width: 600px; width: 100%;">
                        <input class="form-control me-2 need-help" type="text" placeholder="Type a message here" id="help-message" required>
                        <button class="btn btn-outline-primary need-help-btn" type="submit">Send</button>
                    </form>
                ` : `
                    <!-- Show for non-logged-in users -->
                    <form class="d-flex align-items-center" style="max-width: 600px; width: 100%;">
                        <input class="form-control me-2 need-help" type="text" placeholder="Please log in to send a message" disabled>
                        <button class="btn btn-outline-primary need-help-btn" type="submit" disabled>Send</button>
                    </form>
                `}
            </div>
        </div>

        <!-- main footer -->
        <div class="container-fluid" style="background-color: #000000">
            <div class="container">
                <footer class="text-center text-lg-start text-white">
                    <div class="container-fluid p-4 pb-0">
                        <section class="">
                            <div class="row">
                                <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                                    <h6 class="text-uppercase mb-1" style="font-family:Dela Gothic One, sans-serif;">
                                        SERVE<span style="color:#19AFA5; ">IT</span>
                                    </h6>
                                    <p style="font-size: 12px;">
                                        The content on the Serve It website is provided for informational purposes only.
                                        While
                                        we strive to ensure accuracy, we do not guarantee completeness, reliability, or
                                        timeliness of information. Use of our website and services is at your own risk.
                                        Serve It is not liable for any damages or losses resulting from reliance on our
                                        content
                                        or services. By using our site, you agree to these terms.
                                    </p>
                                </div>
                                <hr class="w-100 clearfix d-md-none" />
                                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3 text-center">
                                    <h6 class="text-uppercase mb-4 font-weight-bold">MarketPlace</h6>
                                    <p><a class="text-white" href="services.html">Services</a></p>
                                    <p><a class="text-white" href="products.html">Products</a></p>
                                </div>
                                <hr class="w-100 clearfix d-md-none" />
                                <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3 text-center">
                                    <h6 class="text-uppercase mb-4 font-weight-bold">COMPANY</h6>
                                    <p><a class="text-white" href="about.html">About ServeIT</a></p>
                                    <p><a class="text-white" href="helpCenter.html">Help Center</a></p>
                                    <p><a class="text-white" href="chats.html">Contact Us</a></p>
                                </div>
                                <hr class="w-100 clearfix d-md-none" />
                                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3 text-center">
                                    <h6 class="text-uppercase mb-4 font-weight-bold">Follow us</h6>
                                    <p><i class="fa-brands fa-tiktok mx-1" href="."></i> ServeIT</p>
                                    <a href="https://github.com/ServeITPH">
                                        <p><i class="fa-brands fa-github mx-1"></i> Serve-IT</p>
                                    </a>
                                    <a href="https://discord.gg/czuSHrzM">
                                        <p><i class="fa-brands fa-discord mx-1"></i>Serve-IT.ph</p>
                                    </a>
                                </div>
                            </div>
                        </section>
                        <hr class="my-3">
                        <section class="p-3 pt-0">
                            <div class="row d-flex align-items-center">
                                <div class="col-md-7 col-lg-8 text-center text-md-start">
                                    <div class="p-3">
                                        2024 Copyright:
                                        <a href="https://github.com/ServeITPH" style="color:#19AFA5">Serve-It.Ph</a>
                                    </div>
                                </div>
                                <div class="col-md-5 col-lg-4 ml-lg-0 text-center text-md-end">
                                    <a class="btn btn-outline-light btn-floating m-1" role="button"><i
                                            class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-outline-light btn-floating m-1" role="button"
                                        href="https://github.com/ServeITPH"><i class="fab fa-github"></i></a>
                                    <a class="btn btn-outline-light btn-floating m-1" role="button"><i
                                            class="fab fa-google"></i></a>
                                    <a class="btn btn-outline-light btn-floating m-1" role="button"><i
                                            class="fab fa-linkedin"></i></a>
                                </div>
                            </div>
                        </section>
                    </div>
                </footer>
            </div>
        </div>
    `;
}

// Load social media payment section
function loadSMPayment() {
    const smpaymentContainer = document.getElementById('smpayment-container');
    if (!smpaymentContainer) return;

    smpaymentContainer.innerHTML = `
        <div class="container my-5 wow animate__animated animate__fadeInUp ">
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="title">
                        Social Media Marketing
                    </div>
                    <div class="subtitle text-justify">
                        Boost your online presence with our expert social media marketing services. We help you reach
                        your target audience effectively through strategic campaigns and engaging content across all
                        major platforms.
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-more btn-primary mx-auto" href="services.html"> More Services</a>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Load counts for homepage
function loadCounts() {
    const userCount = document.getElementById('user-count');
    const transactionCount = document.getElementById('transaction-count');
    const productCount = document.getElementById('product-count');

    if (userCount) userCount.textContent = getUserCount();
    if (transactionCount) transactionCount.textContent = getTransactionCount();
    if (productCount) productCount.textContent = getProductCount();
}

// Load new arrivals
function loadNewArrivals() {
    const container = document.getElementById('new-arrivals-container');
    if (!container) return;

    const newArrivals = getNewArrivals();
    
    container.innerHTML = newArrivals.map(product => `
        <div class="col d-flex flex-row">
            <div class="productCard rounded mx-auto">
                <div class="card-body d-flex flex-column justify-content-between align-items-center">
                    <div class="productImage">
                        <img src="assets/images/items/${product.attachment}"
                            alt="${product.title}">
                    </div>
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <span class="productTitle">${product.title}</span>
                        <span class="productPrice">₱${product.price}</span>
                    </div>
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <p class="productDescription">${product.shortDescription}</p>
                        <a href="#" onclick="handleSeeMoreClick(event, ${product.itemID})">
                            <button class="btnSeeMore rounded-pill">See More</button>
                        </a>
                    </div>
                    <div class="line" style="border-top: 2px solid black; width: 100%; margin: 10px 0;"></div>
                    <div class="category">
                        <span>${product.categoryName}</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Load services
function loadServices() {
    const container = document.getElementById('services-container');
    if (!container) return;

    const services = getServices().slice(0, 6); // Limit to 6 services like in PHP
    
    container.innerHTML = services.map(service => {
        const titleParts = service.title.split(' ');
        const limitedTitle = titleParts.length >= 2 
            ? `<span style="color: #19AFA5;">${titleParts[0]}</span> ${titleParts[1]}`
            : service.title;
        
        return `
            <div class="col-12 col-md-4 my-4">
                <div class="row">
                    <div class="services-title text-center">
                        ${limitedTitle}
                    </div>
                </div>
                <div class="subtitle text-center">
                    ${service.description}
                </div>
            </div>
        `;
    }).join('');
}

// Load digital products
function loadDigitalProducts() {
    const container = document.getElementById('digital-products-container');
    if (!container) return;

    const products = getProducts().slice(0, 6); // Limit to 6 products like in PHP
    
    container.innerHTML = products.map(product => {
        const titleParts = product.title.split(' ');
        const limitedTitle = titleParts.length >= 2 
            ? `<span style="color: #19AFA5;">${titleParts[0]}</span> ${titleParts[1]}`
            : product.title;
        
        return `
            <div class="col-12 col-md-4 my-4">
                <div class="row">
                    <div class="services-title text-center">
                        ${limitedTitle}
                    </div>
                </div>
                <div class="subtitle text-center">
                    ${product.description}
                </div>
            </div>
        `;
    }).join('');
}

// Load team carousel
function loadTeamCarousel() {
    const container = document.getElementById('team-carousel-container');
    if (!container) return;
    
    const teamMembers = getTeamMembers();
    
    container.innerHTML = `
        <style>
            .dev-name {
                font-size: 24px;
                text-align: center;
            }

            .dev-name .last-name {
                font-weight: bold;
                font-size: 24px;
            }

            .dev-name .first-name {
                font-size: 24px;
            }

            @media (max-width: 768px) {
                .dev-name {
                    font-size: 14px;
                    text-align: center;
                }

                .dev-name .last-name {
                    font-size: 14px;
                    font-weight: bold;
                }

                .dev-name .first-name {
                    font-size: 14px;
                }
            }
        </style>
        <div id="teamCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                ${teamMembers.map((member, index) => {
                    const slideIndex = Math.floor(index / 3);
                    const isActive = slideIndex === 0 ? 'active' : '';
                    return `<button type='button' data-bs-target='#teamCarousel' data-bs-slide-to='${slideIndex}' class='${isActive}'></button>`;
                }).slice(0, Math.ceil(teamMembers.length / 3)).join('')}
            </div>

            <!-- Carousel Items -->
            <div class="carousel-inner">
                ${teamMembers.map((member, index) => {
                    const nameParts = member.name.split(', ');
                    const firstName = nameParts.length > 1 ? nameParts[1].trim() : nameParts[0];
                    const lastName = nameParts[0].trim();
                    
                    const startNewSlide = index % 3 === 0;
                    const endSlide = index % 3 === 2 || index === teamMembers.length - 1;
                    
                    let html = '';
                    
                    if (startNewSlide) {
                        html += '<div class="carousel-item ' + (index === 0 ? 'active' : '') + '"><div class="row justify-content-center">';
                    }
                    
                    html += `
                        <div class='col-4 col-md-3 col-sml-6'> 
                            <div class='team-member'>
                                <a href='${member.portfolio}'><img src='assets/images/about/${member.image}' class='img-fluid rounded-circle' alt='${member.name}'></a>
                                <h5 class='dev-name'>
                                    <span class='last-name'>${lastName}</span><br>
                                    <span class='first-name'>${firstName}</span>
                                </h5>
                            </div>
                        </div>
                    `;
                    
                    if (endSlide) {
                        html += '</div></div>';
                    }
                    
                    return html;
                }).join('')}
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#teamCarousel" data-bs-slide="prev">
                <img src="assets/images/about/prev.png" alt="Previous">
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#teamCarousel" data-bs-slide="next">
                <img src="assets/images/about/next.png" alt="Next">
            </button>
        </div>
    `;
}

// Load smpayment section
function loadSMPayment() {
    const container = document.getElementById('smpayment-container');
    if (!container) return;

    container.innerHTML = `
        <div class="container wow animate__animated animate__fadeIn">
            <div class="smpayment-container">
                <div class="main-smpayment-title text-center">
                    <span style=" color: #000000 ">SIMPLIFIED </span>PAYMENT
                </div>
                <div class="row ">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="smpayment-info-container">
                            <div class="smpayment-icon">
                            <i class="fas fa-bolt fa-3x animate__animated animate__pulse animate__infinite" ></i> 
                            </div>
                            <div class="smpayment-title">
                                Fast Transactions
                            </div>
                            <div class="subtitle">
                                Enjoy quick and seamless payment processing for a hassle-free experience.
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="smpayment-info-container">
                            <div class="smpayment-icon">
                            <i class="fas fa-lock fa-3x animate__animated animate__pulse animate__infinite" ></i> 
                            </div>
                            <div class="smpayment-title">
                                Secure Payments
                            </div>
                            <div class="subtitle">
                                Your financial information is protected with industry-standard encryption.
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="smpayment-info-container">
                            <div class="smpayment-icon">
                            <i class="fas fa-credit-card fa-3x animate__animated animate__pulse animate__infinite" ></i> 
                            </div>
                            <div class="smpayment-title">
                                Multiple Methods
                            </div>
                            <div class="subtitle">
                                Choose from various payment options that suit your preferences.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Handle help message submission
function sendHelpMessage(event) {
    event.preventDefault();
    const messageInput = document.getElementById('help-message');
    const message = messageInput.value.trim();
    
    if (message) {
        // In a real app, this would send to a server
        // For demo, we'll just show an alert and redirect
        alert('Message sent! We will get back to you soon.');
        messageInput.value = '';
        // Redirect to chats page
        window.location.href = 'chats.html';
    }
}

// Initialize page when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    loadNavigation();
    loadFooter();
    loadSMPayment();
    loadCounts();
    loadNewArrivals();
    loadServices();
    loadDigitalProducts();
    loadTeamCarousel();
});
