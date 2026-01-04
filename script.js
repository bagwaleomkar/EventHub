// EventHub - JavaScript Functionality

// ==========================================
// 1. REGISTER PAGE - TAB SWITCHING
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    // Register page tab switching
    const registerTabs = document.querySelectorAll('.register-tab');
    const registerForms = document.querySelectorAll('.register-form');
    
    if (registerTabs.length > 0) {
        registerTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                registerTabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Get the data-tab attribute
                const targetTab = this.getAttribute('data-tab');
                
                // Hide all forms
                registerForms.forEach(form => form.classList.remove('active'));
                
                // Show the target form
                const targetForm = document.getElementById(`${targetTab}-form`);
                if (targetForm) {
                    targetForm.classList.add('active');
                }
            });
        });
    }
    
    // ==========================================
    // 2. CONTACT PAGE - FAQ ACCORDION
    // ==========================================
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', function() {
                // Toggle active class
                item.classList.toggle('active');
                
                // Close other FAQ items (optional - for accordion effect)
                // Uncomment the lines below if you want only one FAQ open at a time
                /*
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                */
            });
        });
    }
    
    // ==========================================
    // 3. HAMBURGER MENU TOGGLE
    // ==========================================
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!hamburger.contains(event.target) && !navLinks.contains(event.target)) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    }
    
    // ==========================================
    // 4. FORM VALIDATION
    // ==========================================
    
    // Attendee Form Validation
    const attendeeForm = document.getElementById('attendeeRegForm');
    if (attendeeForm) {
        attendeeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            // Password match validation
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }
            
            // Password strength validation
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!passwordRegex.test(password)) {
                alert('Password must be at least 8 characters long with at least one uppercase letter, one lowercase letter, and one number.');
                return false;
            }
            
            // Terms validation
            if (!terms) {
                alert('You must agree to the Terms of Service and Privacy Policy.');
                return false;
            }
            
            // Submit form via AJAX
            const formData = new FormData(attendeeForm);
            
            // Get interests checkboxes - send as array
            const interests = [];
            document.querySelectorAll('input[name="interests[]"]:checked').forEach(checkbox => {
                interests.push(checkbox.value);
            });
            // Remove the old interests entries and add array properly
            formData.delete('interests[]');
            interests.forEach(interest => {
                formData.append('interests[]', interest);
            });
            
            // Show loading state
            const submitBtn = attendeeForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating Account...';
            
            fetch('register_attendee.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                
                if (data.success) {
                    alert(data.message);
                    attendeeForm.reset();
                    // Optionally redirect to login or dashboard
                    // window.location.href = 'login.html';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
            });
        });
    }
    
    // Organizer Form Validation
    const organizerForm = document.getElementById('organizerRegForm');
    if (organizerForm) {
        organizerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('orgPassword').value;
            const confirmPassword = document.getElementById('orgConfirmPassword').value;
            const terms = document.getElementById('orgTerms').checked;
            
            // Password match validation
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }
            
            // Password strength validation
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
            if (!passwordRegex.test(password)) {
                alert('Password must be at least 8 characters long with at least one uppercase letter, one lowercase letter, and one number.');
                return false;
            }
            
            // Terms validation
            if (!terms) {
                alert('You must agree to the Terms of Service, Privacy Policy, and Organizer Guidelines.');
                return false;
            }
            
            // Submit form via AJAX
            const formData = new FormData(organizerForm);
            
            // Get event types checkboxes - send as array
            const eventTypes = [];
            document.querySelectorAll('input[name="eventTypes[]"]:checked').forEach(checkbox => {
                eventTypes.push(checkbox.value);
            });
            // Remove the old eventTypes entries and add array properly
            formData.delete('eventTypes[]');
            eventTypes.forEach(type => {
                formData.append('eventTypes[]', type);
            });
            
            // Show loading state
            const submitBtn = organizerForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating Account...';
            
            fetch('register_organizer.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                
                if (data.success) {
                    alert(data.message);
                    organizerForm.reset();
                    // Optionally redirect to login or dashboard
                    // window.location.href = 'login.html';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                console.error('Error:', error);
                alert('An error occurred. Please try again later.');
            });
        });
    }
    
    // ==========================================
    // 5. CONTACT FORM VALIDATION
    // ==========================================
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            if (name && email && subject && message) {
                alert('Thank you for contacting us! We will get back to you soon. (This is a demo - no actual submission occurs)');
                contactForm.reset();
            }
        });
    }
    
    // ==========================================
    // 6. NEWSLETTER SUBSCRIPTION
    // ==========================================
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            if (emailInput && emailInput.value) {
                alert('Thank you for subscribing to our newsletter! (This is a demo - no actual subscription occurs)');
                emailInput.value = '';
            }
        });
    }
    
    // ==========================================
    // 7. SMOOTH SCROLLING FOR ANCHOR LINKS
    // ==========================================
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // ==========================================
    // 8. SCROLL TO TOP FUNCTIONALITY
    // ==========================================
    window.addEventListener('scroll', function() {
        const scrollBtn = document.getElementById('scrollToTop');
        if (scrollBtn) {
            if (window.pageYOffset > 300) {
                scrollBtn.style.display = 'block';
            } else {
                scrollBtn.style.display = 'none';
            }
        }
    });
    
    // ==========================================
    // 9. SOCIAL LOGIN BUTTONS (DEMO)
    // ==========================================
    const socialLoginButtons = document.querySelectorAll('.btn-social');
    socialLoginButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const provider = this.classList.contains('btn-google') ? 'Google' : 'Facebook';
            alert(`${provider} login would be initiated here. (This is a demo)`);
        });
    });
    
    // ==========================================
    // 10. LOADING ANIMATION (IF NEEDED)
    // ==========================================
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });
});

// ==========================================
// 11. UTILITY FUNCTIONS
// ==========================================

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Phone number validation
function isValidPhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
    return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    return strength;
}
// ==========================================
// NAVIGATION - SESSION CHECK & DYNAMIC UI
// ==========================================
async function checkLoginStatus() {
    try {
        const response = await fetch('check_session.php');
        const data = await response.json();
        
        const registerBtn = document.querySelector('.register-btn');
        
        if (data.logged_in) {
            // User is logged in - replace Register button with user menu
            if (registerBtn) {
                const userMenu = document.createElement('div');
                userMenu.className = 'user-menu';
                
                const dashboardLink = data.role === 'organizer' ? 'organizer_dashboard.php' : 'attendee_dashboard.php';
                
                userMenu.innerHTML = `
                    <span class="user-greeting">Hi, ${data.first_name} <i class="fas fa-chevron-down"></i></span>
                    <div class="dropdown-menu">
                        <a href="${dashboardLink}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                `;
                
                registerBtn.replaceWith(userMenu);
                
                // Add click event for mobile dropdown toggle
                const userGreeting = document.querySelector('.user-greeting');
                const dropdownMenu = document.querySelector('.dropdown-menu');
                
                if (userGreeting && dropdownMenu) {
                    // Toggle dropdown on click (for mobile)
                    userGreeting.addEventListener('click', function(e) {
                        e.stopPropagation();
                        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
                    });
                    
                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!userMenu.contains(e.target)) {
                            dropdownMenu.style.display = 'none';
                        }
                    });
                }
            }
            
            // Update navigation links based on role
            const navLinks = document.querySelector('.nav-links');
            if (navLinks && !document.querySelector('.nav-links a[href="my_events.php"]')) {
                if (data.role === 'organizer') {
                    // Add organizer-specific links
                    const createEventLi = document.createElement('li');
                    createEventLi.innerHTML = '<a href="create_event.php">Create Event</a>';
                    navLinks.appendChild(createEventLi);
                    
                    const myEventsLi = document.createElement('li');
                    myEventsLi.innerHTML = '<a href="my_events.php">My Events</a>';
                    navLinks.appendChild(myEventsLi);
                } else {
                    // Add attendee-specific links
                    const myEventsLi = document.createElement('li');
                    myEventsLi.innerHTML = '<a href="my_events.php">My Events</a>';
                    navLinks.appendChild(myEventsLi);
                }
            }
        } else {
            // User not logged in - show Register button (already in HTML)
            // Make sure Login link exists
            const nav = document.querySelector('.nav-links');
            if (nav && !document.querySelector('.nav-links a[href="login.html"]')) {
                const loginLi = document.createElement('li');
                loginLi.innerHTML = '<a href="login.html">Login</a>';
                nav.appendChild(loginLi);
            }
        }
    } catch (error) {
        console.log('Session check failed:', error);
    }
}

// Run on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkLoginStatus);
} else {
    checkLoginStatus();
}