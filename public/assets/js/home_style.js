// DOM Elements
const body = document.body;
const darkModeToggle = document.getElementById('darkmode-toggle');
const menuBtn = document.querySelector('.menu-btn');
const nav = document.getElementById('nav-menu');
const navLinks = document.querySelectorAll('nav a');

// Dark Mode Toggle
function enableDarkMode() {
    body.classList.add('dark-mode');
    localStorage.setItem('darkMode', 'enabled');
}

function disableDarkMode() {
    body.classList.remove('dark-mode');
    localStorage.setItem('darkMode', 'disabled');
}

// Check user preference
const darkMode = localStorage.getItem('darkMode');
if (darkMode === 'enabled') {
    enableDarkMode();
    darkModeToggle.checked = true;
}

// Event Listeners
darkModeToggle.addEventListener('change', () => {
    if (darkModeToggle.checked) {
        enableDarkMode();
    } else {
        disableDarkMode();
    }
});

// Mobile Menu Toggle
menuBtn.addEventListener('click', () => {
    nav.classList.toggle('active');
    
    // Change menu icon
    const menuIcon = menuBtn.querySelector('i');
    if (nav.classList.contains('active')) {
        menuIcon.classList.remove('fa-bars');
        menuIcon.classList.add('fa-times');
    } else {
        menuIcon.classList.remove('fa-times');
        menuIcon.classList.add('fa-bars');
    }
});

// Close mobile menu when clicking on a nav link
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        nav.classList.remove('active');
        const menuIcon = menuBtn.querySelector('i');
        menuIcon.classList.remove('fa-times');
        menuIcon.classList.add('fa-bars');
    });
});

// Add active class to nav links based on scroll position
function highlightActiveNavLink() {
    const sections = document.querySelectorAll('section');
    const scrollPosition = window.scrollY;
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.offsetHeight;
        const sectionId = section.getAttribute('id');
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${sectionId}`) {
                    link.classList.add('active');
                }
            });
        }
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
        }
    });
});

// Animation on scroll for features
function animateOnScroll() {
    const features = document.querySelectorAll('.feature');
    
    features.forEach((feature, index) => {
        const featurePosition = feature.getBoundingClientRect().top;
        const screenPosition = window.innerHeight / 1.3;
        
        if (featurePosition < screenPosition) {
            setTimeout(() => {
                feature.style.opacity = 1;
                feature.style.transform = 'translateY(0)';
            }, index * 200);
        }
    });
}

// Initialize animations
window.addEventListener('load', () => {
    // Set initial opacity for features
    const features = document.querySelectorAll('.feature');
    features.forEach(feature => {
        feature.style.opacity = 0;
        feature.style.transform = 'translateY(20px)';
        feature.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    
    // Trigger animations
    animateOnScroll();
});

// Event listeners for scroll events
window.addEventListener('scroll', () => {
    highlightActiveNavLink();
    animateOnScroll();
});

// Add placeholder hero image if not provided
window.addEventListener('load', () => {
    const heroImage = document.querySelector('.hero-image img');
    if (!heroImage.getAttribute('src') || heroImage.getAttribute('src').includes('undefined')) {
        heroImage.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImEiIHgxPSIwIiB5MT0iMCIgeDI9IjEiIHkyPSIxIj48c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjNDM2MWVlIi8+PHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNGNjOWYwIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PHBhdGggZD0iTTUwLDUwIEE1MCw1MCAwIDEgMSA5MCw5MCBMNTUwLDk5MCBMMzUwLDE1MCBaIiBmaWxsPSJ1cmwoI2EpIiBvcGFjaXR5PSIwLjUiLz48cGF0aCBkPSJNMTAwLDAgTDE1MCw1MCBMMTUwLDM1MCBMMCwzMDAgWiIgZmlsbD0idXJsKCNhKSIgb3BhY2l0eT0iMC41Ii8+PGNpcmNsZSBjeD0iNDUwIiBjeT0iMTUwIiByPSI4MCIgZmlsbD0idXJsKCNhKSIgb3BhY2l0eT0iMC43Ii8+PHJlY3QgeD0iMzAwIiB5PSIyMDAiIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSJ1cmwoI2EpIiBvcGFjaXR5PSIwLjMiLz48L3N2Zz4=';
    }
});