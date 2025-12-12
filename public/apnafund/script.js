// Custom JavaScript for Apna Crowdfunding

document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const navHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetSection.offsetTop - navHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Navbar background change on scroll
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Counter animation
    function animateCounters() {
        const counters = document.querySelectorAll('.counter');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const increment = target / 100;
            let current = 0;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        });
    }

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add animation classes
                if (entry.target.classList.contains('counter')) {
                    animateCounters();
                }
                
                // Animate cards on scroll
                if (entry.target.classList.contains('step-card') || 
                    entry.target.classList.contains('campaign-card') ||
                    entry.target.classList.contains('contact-item')) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            }
        });
    }, observerOptions);

    // Observe elements for scroll animations
    document.querySelectorAll('.step-card, .campaign-card, .contact-item, .counter').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });

    // Contact form submission
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'alert alert-success mt-3';
                successMessage.innerHTML = '<i class="fas fa-check-circle me-2"></i>Thank you! Your message has been sent successfully.';
                
                contactForm.appendChild(successMessage);
                contactForm.reset();
                
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                
                // Remove success message after 5 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 5000);
                
            }, 2000);
        });
    }

    // Donation button clicks
    const donateButtons = document.querySelectorAll('.campaign-card .btn, .cta-section .btn, .hero-buttons .btn-primary');
    donateButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#donate' || this.textContent.includes('Donate')) {
                e.preventDefault();
                showDonationModal();
            }
        });
    });

    // Donation Modal
    function showDonationModal() {
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'modal fade show';
        modal.style.display = 'block';
        modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
        
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-heart text-primary me-2"></i>
                            Make a Donation
                        </h5>
                        <button type="button" class="btn-close" onclick="this.closest('.modal').remove()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Select Campaign:</h6>
                                <select class="form-control mb-3">
                                    <option>Education for All</option>
                                    <option>Medical Emergency</option>
                                    <option>Housing Support</option>
                                    <option>General Fund</option>
                                </select>
                                
                                <h6>Donation Amount:</h6>
                                <div class="amount-buttons mb-3">
                                    <button class="btn btn-outline-primary me-2 amount-btn" data-amount="500">₹500</button>
                                    <button class="btn btn-outline-primary me-2 amount-btn" data-amount="1000">₹1000</button>
                                    <button class="btn btn-outline-primary me-2 amount-btn" data-amount="2000">₹2000</button>
                                    <button class="btn btn-outline-primary amount-btn" data-amount="5000">₹5000</button>
                                </div>
                                <input type="number" class="form-control mb-3" placeholder="Enter custom amount" id="customAmount">
                            </div>
                            <div class="col-md-6">
                                <h6>Donor Information:</h6>
                                <input type="text" class="form-control mb-2" placeholder="Full Name" required>
                                <input type="email" class="form-control mb-2" placeholder="Email Address" required>
                                <input type="tel" class="form-control mb-2" placeholder="Phone Number" required>
                                <textarea class="form-control mb-3" rows="3" placeholder="Message (Optional)"></textarea>
                                
                                <div class="donation-summary p-3 bg-light rounded">
                                    <h6>Donation Summary:</h6>
                                    <div class="d-flex justify-content-between">
                                        <span>Amount:</span>
                                        <span id="donationAmount">₹0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Processing Fee:</span>
                                        <span id="processingFee">₹0</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total:</span>
                                        <span id="totalAmount">₹0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="processDonation()">
                            <i class="fas fa-heart me-2"></i>Donate Now
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Add event listeners for amount buttons
        modal.querySelectorAll('.amount-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                modal.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('btn-primary'));
                modal.querySelectorAll('.amount-btn').forEach(b => b.classList.add('btn-outline-primary'));
                
                // Add active class to clicked button
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                // Update amount
                const amount = parseInt(this.dataset.amount);
                updateDonationSummary(amount);
                
                // Clear custom amount
                modal.querySelector('#customAmount').value = '';
            });
        });
        
        // Custom amount input
        modal.querySelector('#customAmount').addEventListener('input', function() {
            const amount = parseInt(this.value) || 0;
            updateDonationSummary(amount);
            
            // Remove active class from all amount buttons
            modal.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('btn-primary'));
            modal.querySelectorAll('.amount-btn').forEach(b => b.classList.add('btn-outline-primary'));
        });
    }

    function updateDonationSummary(amount) {
        const modal = document.querySelector('.modal');
        const processingFee = Math.round(amount * 0.03); // 3% processing fee
        const total = amount + processingFee;
        
        modal.querySelector('#donationAmount').textContent = `₹${amount}`;
        modal.querySelector('#processingFee').textContent = `₹${processingFee}`;
        modal.querySelector('#totalAmount').textContent = `₹${total}`;
    }

    // Process donation function
    window.processDonation = function() {
        const modal = document.querySelector('.modal');
        const donateBtn = modal.querySelector('.btn-primary');
        
        // Show loading state
        donateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        donateBtn.disabled = true;
        
        // Simulate payment processing
        setTimeout(() => {
            modal.remove();
            showSuccessMessage();
        }, 3000);
    };

    function showSuccessMessage() {
        const successModal = document.createElement('div');
        successModal.className = 'modal fade show';
        successModal.style.display = 'block';
        successModal.style.backgroundColor = 'rgba(0,0,0,0.8)';
        
        successModal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content text-center">
                    <div class="modal-body p-5">
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-success mb-3">Thank You!</h4>
                        <p class="mb-4">Your donation has been processed successfully. You will receive a confirmation email shortly.</p>
                        <button type="button" class="btn btn-primary" onclick="this.closest('.modal').remove()">
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(successModal);
        
        // Auto close after 5 seconds
        setTimeout(() => {
            successModal.remove();
        }, 5000);
    }

    // Parallax effect removed for simplicity

    // Add loading animation to page
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
        
        // Trigger counter animation when hero section is visible
        setTimeout(() => {
            animateCounters();
        }, 1000);
    });

    // Add some interactive effects
    document.querySelectorAll('.campaign-card, .step-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Progress bar animation
    function animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
                bar.style.transition = 'width 2s ease-in-out';
            }, 500);
        });
    }

    // Trigger progress bar animation when campaigns section is visible
    const campaignsSection = document.querySelector('#campaigns');
    if (campaignsSection) {
        const campaignsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateProgressBars();
                }
            });
        }, { threshold: 0.5 });
        
        campaignsObserver.observe(campaignsSection);
    }

    // Social media links functionality
    document.querySelectorAll('.social-links a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = 'btn btn-primary back-to-top';
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    `;
    
    document.body.appendChild(backToTopBtn);
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Show/hide back to top button
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.style.opacity = '1';
            backToTopBtn.style.visibility = 'visible';
        } else {
            backToTopBtn.style.opacity = '0';
            backToTopBtn.style.visibility = 'hidden';
        }
    });

    console.log('Apna Crowdfunding website loaded successfully! ❤️');
}); 