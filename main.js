// Main JavaScript for Kersa Secondary School Website

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeMobileMenu();
    initializeHeroSlider();
    initializeCounters();
    initializeTestimonials();
    initializeSmoothScroll();
    initializeFormValidation();
    initializeNewsFilters();
    loadDynamicContent();
});

// Mobile Menu Toggle
function initializeMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            
            // Change icon
            const icon = menuToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menuToggle.contains(event.target) && !navMenu.contains(event.target)) {
                navMenu.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
}

// Hero Slider
function initializeHeroSlider() {
    const slides = document.querySelectorAll('.hero-slide');
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
    let currentSlide = 0;
    let slideInterval;
    
    if (slides.length > 0) {
        // Show first slide
        slides[0].classList.add('active');
        
        // Auto slide
        startAutoSlide();
        
        // Previous button
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                changeSlide(-1);
                resetAutoSlide();
            });
        }
        
        // Next button
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                changeSlide(1);
                resetAutoSlide();
            });
        }
        
        // Pause on hover
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            heroSection.addEventListener('mouseenter', stopAutoSlide);
            heroSection.addEventListener('mouseleave', startAutoSlide);
        }
    }
    
    function changeSlide(direction) {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + direction + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
    }
    
    function startAutoSlide() {
        slideInterval = setInterval(() => {
            changeSlide(1);
        }, 5000);
    }
    
    function stopAutoSlide() {
        clearInterval(slideInterval);
    }
    
    function resetAutoSlide() {
        stopAutoSlide();
        startAutoSlide();
    }
}

// Animated Counters
function initializeCounters() {
    const counters = document.querySelectorAll('.counter');
    
    if (counters.length > 0) {
        const observerOptions = {
            threshold: 0.5
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute('data-target'));
                    animateCounter(counter, target);
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => {
            observer.observe(counter);
        });
    }
    
    function animateCounter(counter, target) {
        let current = 0;
        const increment = target / 50;
        const duration = 2000;
        const stepTime = duration / 50;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target + '+';
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, stepTime);
    }
}

// Testimonials Slider
function initializeTestimonials() {
    const testimonials = document.querySelectorAll('.testimonial');
    const dots = document.querySelectorAll('.dot');
    let currentTestimonial = 0;
    let testimonialInterval;
    
    if (testimonials.length > 0) {
        showTestimonial(0);
        startAutoSlide();
        
        // Dot click events
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showTestimonial(index);
                resetAutoSlide();
            });
        });
    }
    
    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            testimonial.style.display = i === index ? 'block' : 'none';
        });
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        
        currentTestimonial = index;
    }
    
    function startAutoSlide() {
        testimonialInterval = setInterval(() => {
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            showTestimonial(currentTestimonial);
        }, 5000);
    }
    
    function resetAutoSlide() {
        clearInterval(testimonialInterval);
        startAutoSlide();
    }
}

// Smooth Scroll
function initializeSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            if (targetId !== '#') {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// Form Validation
function initializeFormValidation() {
    const applicationForm = document.getElementById('applicationForm');
    const contactForm = document.getElementById('contactForm');
    const loginForm = document.getElementById('loginForm');
    
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateApplicationForm()) {
                submitApplication(this);
            }
        });
    }
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateContactForm()) {
                submitContactForm(this);
            }
        });
    }
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateLoginForm()) {
                submitLoginForm(this);
            }
        });
    }
}

function validateApplicationForm() {
    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    const gender = document.getElementById('gender');
    const dob = document.getElementById('dob');
    const grade = document.getElementById('grade');
    const parentName = document.getElementById('parentName');
    const phone = document.getElementById('phone');
    const prevSchool = document.getElementById('prevSchool');
    const agree = document.getElementById('agree');
    
    let isValid = true;
    
    // Validate required fields
    const requiredFields = [firstName, lastName, gender, dob, grade, parentName, phone, prevSchool, agree];
    
    requiredFields.forEach(field => {
        if (field && !field.value.trim()) {
            field.style.borderColor = 'red';
            isValid = false;
        } else if (field) {
            field.style.borderColor = '';
        }
    });
    
    // Validate phone number
    if (phone && phone.value.trim() && !/^\d{10}$/.test(phone.value.replace(/[^0-9]/g, ''))) {
        phone.style.borderColor = 'red';
        isValid = false;
    }
    
    if (!isValid) {
        alert('Please fill in all required fields correctly.');
    }
    
    return isValid;
}

function validateContactForm() {
    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const subject = document.getElementById('subject');
    const message = document.getElementById('message');
    
    let isValid = true;
    
    if (name && !name.value.trim()) {
        name.style.borderColor = 'red';
        isValid = false;
    }
    
    if (email && !isValidEmail(email.value)) {
        email.style.borderColor = 'red';
        isValid = false;
    }
    
    if (subject && !subject.value.trim()) {
        subject.style.borderColor = 'red';
        isValid = false;
    }
    
    if (message && !message.value.trim()) {
        message.style.borderColor = 'red';
        isValid = false;
    }
    
    if (!isValid) {
        alert('Please fill in all fields correctly.');
    }
    
    return isValid;
}

function validateLoginForm() {
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    
    let isValid = true;
    
    if (username && !username.value.trim()) {
        username.style.borderColor = 'red';
        isValid = false;
    }
    
    if (password && !password.value.trim()) {
        password.style.borderColor = 'red';
        isValid = false;
    }
    
    if (!isValid) {
        alert('Please enter username and password.');
    }
    
    return isValid;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Form Submissions (AJAX)
function submitApplication(form) {
    const formData = new FormData(form);
    
    fetch('php/admissions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Application submitted successfully! We will contact you soon.');
            form.reset();
        } else {
            alert('Error submitting application. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting application. Please try again later.');
    });
}

function submitContactForm(form) {
    const formData = new FormData(form);
    
    fetch('php/contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Message sent successfully! We will respond within 24 hours.');
            form.reset();
        } else {
            alert('Error sending message. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message. Please try again later.');
    });
}

function submitLoginForm(form) {
    const formData = new FormData(form);
    
    fetch('php/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'dashboard.html';
        } else {
            alert('Invalid username or password.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error logging in. Please try again.');
    });
}

// News Filters
function initializeNewsFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const newsCards = document.querySelectorAll('.news-list-card');
    
    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                
                // Filter news cards
                newsCards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-category') === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }
}

// Load Dynamic Content
function loadDynamicContent() {
    loadNewsContent();
    loadStudentData();
}

function loadNewsContent() {
    fetch('api/news.json')
        .then(response => response.json())
        .then(data => {
            const newsContainer = document.getElementById('dynamicNews');
            
            if (newsContainer) {
                data.news.forEach(item => {
                    const newsCard = createNewsCard(item);
                    newsContainer.appendChild(newsCard);
                });
            }
        })
        .catch(error => {
            console.error('Error loading news:', error);
        });
}

function createNewsCard(item) {
    const card = document.createElement('div');
    card.className = 'news-list-card';
    card.setAttribute('data-category', item.category);
    
    card.innerHTML = `
        <img src="${item.image}" alt="${item.title}">
        <div class="news-list-content">
            <span class="news-category">${item.category}</span>
            <h3>${item.title}</h3>
            <p class="news-meta">
                <i class="fas fa-calendar"></i> ${item.date} | 
                <i class="fas fa-user"></i> ${item.author}
            </p>
            <p>${item.excerpt}</p>
            <a href="${item.link}" class="read-more">Read Full Article →</a>
        </div>
    `;
    
    return card;
}

function loadStudentData() {
    fetch('api/students.json')
        .then(response => response.json())
        .then(data => {
            const studentContainer = document.getElementById('studentData');
            
            if (studentContainer) {
                // Display student data in dashboard
                const table = document.createElement('table');
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>Average Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.students.map(student => `
                            <tr>
                                <td>${student.id}</td>
                                <td>${student.name}</td>
                                <td>${student.grade}</td>
                                <td>${student.section}</td>
                                <td>${student.average_score}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                `;
                
                studentContainer.appendChild(table);
            }
        })
        .catch(error => {
            console.error('Error loading student data:', error);
        });
}

// Utility Functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Scroll to top button
function createScrollToTopButton() {
    const button = document.createElement('button');
    button.innerHTML = '<i class="fas fa-arrow-up"></i>';
    button.className = 'scroll-top-btn';
    button.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--secondary-color);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        z-index: 1000;
        transition: all 0.3s;
    `;
    
    document.body.appendChild(button);
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            button.style.display = 'block';
        } else {
            button.style.display = 'none';
        }
    });
    
    button.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Initialize scroll to top button
createScrollToTopButton();
