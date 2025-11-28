// ---- image slider ---- 
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('track');
    if (!track || !track.children || track.children.length === 0) {
        return; // Exit if track element doesn't exist or has no children
    }
    const slides = Array.from(track.children);
    const totalSlides = slides.length;
    if (totalSlides === 0) return; // Exit if no slides
    
    let index = 0;
    const intervalTime = 5000;
    let slideInterval;

    // Auto-slide function
    function moveToSlide(i) {
        if (track) {
        track.style.transform = `translateX(-${i * 100}%)`;
        }
    }

    function nextSlide() {
        index++;
        if (index >= totalSlides) index = 0;
        moveToSlide(index);
    }

    function startAutoSlide() {
        slideInterval = setInterval(nextSlide, intervalTime);
    }

    function stopAutoSlide() {
        clearInterval(slideInterval);
    }

    if (!track) return; // Double check before proceeding with event listeners

    startAutoSlide();

    // Pause on hover
    if (track) {
    track.addEventListener('mouseenter', stopAutoSlide);
    track.addEventListener('mouseleave', startAutoSlide);

    // --- Drag functionality ---
    let isDown = false;
    let startX;
    let currentTranslate = 0;
    let prevTranslate = 0;

    track.addEventListener('mousedown', (e) => {
            if (!track) return;
        isDown = true;
        startX = e.pageX;
        track.classList.add('grabbing');
        stopAutoSlide();
    });

    track.addEventListener('mousemove', (e) => {
            if (!isDown || !track) return;
        const deltaX = e.pageX - startX;
        track.style.transform = `translateX(${prevTranslate + deltaX}px)`;
    });

    track.addEventListener('mouseup', (e) => {
            if (!isDown || !track) return;
        isDown = false;
        track.classList.remove('grabbing');
        const deltaX = e.pageX - startX;
        // if moved enough to next slide
        if (deltaX < -50 && index < totalSlides - 1) index++;
        if (deltaX > 50 && index > 0) index--;
        moveToSlide(index);
        prevTranslate = -index * track.offsetWidth;
        startAutoSlide();
    });

    track.addEventListener('mouseleave', () => {
            if (!isDown || !track) return;
        isDown = false;
        track.classList.remove('grabbing');
        moveToSlide(index);
        prevTranslate = -index * track.offsetWidth;
        startAutoSlide();
    });

    // Touch events for mobile
    track.addEventListener('touchstart', (e) => {
            if (!track) return;
        isDown = true;
        startX = e.touches[0].clientX;
        stopAutoSlide();
    });

    track.addEventListener('touchmove', (e) => {
            if (!isDown || !track) return;
        const deltaX = e.touches[0].clientX - startX;
        track.style.transform = `translateX(${prevTranslate + deltaX}px)`;
    });

    track.addEventListener('touchend', (e) => {
            if (!isDown || !track) return;
        isDown = false;
        const deltaX = e.changedTouches[0].clientX - startX;
        if (deltaX < -50 && index < totalSlides - 1) index++;
        if (deltaX > 50 && index > 0) index--;
        moveToSlide(index);
        prevTranslate = -index * track.offsetWidth;
        startAutoSlide();
    });
    }
});

// sticky-header // 


/*document.addEventListener("DOMContentLoaded", function () {

    const navBar = document.querySelector('.nav');
    const stickyHeader = document.getElementById('sticky-header');

    
    if (!navBar || !stickyHeader) return;

    stickyHeader.style.display = 'block';
    stickyHeader.style.visibility = 'visible';
    stickyHeader.style.opacity = '1';
    stickyHeader.style.position = 'relative';
    stickyHeader.style.top = 'auto';
    stickyHeader.style.left = 'auto';
    stickyHeader.style.right = 'auto';
    stickyHeader.style.transform = 'none';
    
    stickyHeader.classList.remove("sticky");
    
    let lastScroll = window.pageYOffset || 0;

    window.addEventListener('scroll', function () {
        let currentScroll = window.pageYOffset || 0;

        if (currentScroll > lastScroll && currentScroll > 50) {
            if (navBar) {
                navBar.style.transform = "translateY(-100%)";
            }
            stickyHeader.classList.add("sticky");
        } 

        else {
            if (navBar) {
                navBar.style.transform = "translateY(0)";
            }
            

            if (currentScroll <= 50) {
                stickyHeader.classList.remove("sticky");
            } else {

                stickyHeader.classList.add("sticky");
            }
        }

        lastScroll = currentScroll;
    });
});*/

// sticky mobile header //


document.addEventListener("DOMContentLoaded", function () {
    let lastScroll = 0;
    const delta = 5;
    const navMob = document.getElementById('hidden-nav');
    const stickyHeader = document.getElementById('sticky-header-mob');
    const navHeight = navMob.offsetHeight;

    // Initially sticky header is below nav
    stickyHeader.style.top = navHeight + 'px';

    window.addEventListener('scroll', function () {
        const currentScroll = window.pageYOffset;

        if (Math.abs(currentScroll - lastScroll) <= delta) return;

        if (currentScroll > lastScroll) {
            // Scrolling down → hide nav, sticky header moves to top
            navMob.classList.add('hidden-nav');
            stickyHeader.style.top = '0';
        } else {
            // Scrolling up → show nav, sticky header below nav
            navMob.classList.remove('hidden-nav');
            stickyHeader.style.top = navHeight + 'px';
        }

        lastScroll = currentScroll <= 0 ? 0 : currentScroll;
    });
});


// ---- cart open ----   
const openCartBtns = document.querySelectorAll('.openCart');
const closeCartBtn = document.getElementById('closeCart');
const cartSidebar = document.getElementById('cartSidebar');
const cartOverlay = document.getElementById('cartOverlay');

if (openCartBtns.length && cartSidebar && cartOverlay) {
    openCartBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            // Update cart sidebar before opening
            updateCartSidebar();
            cartSidebar.classList.add('active');
            cartOverlay.classList.add('active');
        });
    });

    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', () => {
            cartSidebar.classList.remove('active');
            cartOverlay.classList.remove('active');
        });
    }

    cartOverlay.addEventListener('click', () => {
        cartSidebar.classList.remove('active');
        cartOverlay.classList.remove('active');
    });
}


// ---- open menu ---- 
    const openBtnDesktop = document.getElementById('openMenuDesktop');
    const openBtnMobile = document.getElementById('openMenuMobile');
    const closeBtn = document.getElementById('closeMenu');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    if (sidebar && overlay) {
        function openSidebar() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        }

        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        if (openBtnDesktop) openBtnDesktop.addEventListener('click', openSidebar);
        if (openBtnMobile) openBtnMobile.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    }


    document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll(".toggle-submenu");

    // ✅ Run only if sidebar submenu toggles exist
    if (toggles.length > 0) {
        toggles.forEach(toggle => {
            toggle.addEventListener("click", function () {
                const submenu = this.nextElementSibling;

                if (submenu && submenu.classList.contains("submenu")) {
                    submenu.classList.toggle("active");
                }

                // rotate arrow
                this.classList.toggle("open");
            });
        });
    }
});


// ---- single product slider ---- 
document.querySelectorAll(".slider-wrapper").forEach(wrapper => {
    const productsSection = wrapper.querySelector(".products-section");
    const prevBtn = wrapper.querySelector(".prev");
    const nextBtn = wrapper.querySelector(".next");
    const productWidthEl = wrapper.querySelector(".single-product-section");

    if (!productsSection || !prevBtn || !nextBtn || !productWidthEl) return;

    const productWidth = productWidthEl.offsetWidth;
    const visibleProducts = 6;
    let currentIndex = 0;
    const totalProducts = wrapper.querySelectorAll(".single-product-section").length;
    const maxIndex = Math.max(0, totalProducts - visibleProducts);

    function updateSlider() {
        productsSection.style.transform = `translateX(-${currentIndex * productWidth}px)`;
        productsSection.style.transition = "transform 0.4s ease-in-out";
    }

    nextBtn.addEventListener("click", () => {
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateSlider();
        }
    });

    prevBtn.addEventListener("click", () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });

    // ---- Mouse drag (swipe) support ----
    let isDragging = false, startX = 0, scrollStart = 0;

    productsSection.addEventListener("mousedown", e => {
        isDragging = true;
        startX = e.pageX;
        scrollStart = currentIndex * productWidth;
        productsSection.style.transition = "none";
    });

    productsSection.addEventListener("mousemove", e => {
        if (!isDragging) return;
        const dx = e.pageX - startX;
        productsSection.style.transform = `translateX(${-(scrollStart - dx)}px)`;
    });

    productsSection.addEventListener("mouseup", e => {
        if (!isDragging) return;
        isDragging = false;
        const dx = e.pageX - startX;
        if (dx < -50 && currentIndex < maxIndex) currentIndex++;
        else if (dx > 50 && currentIndex > 0) currentIndex--;
        updateSlider();
    });

    productsSection.addEventListener("mouseleave", () => {
        if (isDragging) {
            isDragging = false;
            updateSlider();
        }
    });
});

    //-- tab-open--

    document.addEventListener("DOMContentLoaded", () => {
        const tabLinks = document.querySelectorAll(".tab-link");
        const tabContents = document.querySelectorAll(".tab-content");

        if (tabLinks.length && tabContents.length) {
            tabLinks.forEach(link => {
                link.addEventListener("click", () => {
                    // Remove active from all
                    tabLinks.forEach(l => l.classList.remove("active"));
                    tabContents.forEach(c => c.classList.remove("active"));

                    // Add active to clicked tab
                    link.classList.add("active");
                    const target = document.getElementById(link.dataset.tab);
                    if (target) target.classList.add("active"); // ✅ only if element exists
                });
            });
        }
    });

    //--write a review--//

        document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("toggleReviewForm");
        const reviewForm = document.getElementById("reviewForm");

        if (toggleBtn && reviewForm) {  
            toggleBtn.addEventListener("click", function () {
            reviewForm.style.display = (reviewForm.style.display === "none" || reviewForm.style.display === "") 
                ? "block" 
                : "none";
            });
        }
        });


        document.addEventListener("DOMContentLoaded", function() {
            // check if this section exists on the page
            let mainImage = document.getElementById("mainProductImage");
            let thumbnails = document.querySelectorAll(".single-page-product-image-section img");

            if (mainImage && thumbnails.length > 0) {
                thumbnails.forEach(img => {
                    img.addEventListener("click", function() {
                        // update main image
                        mainImage.src = this.src;

                        // reset borders
                        thumbnails.forEach(t => t.style.border = "2px solid #F0F0F0");

                        // set active border
                        this.style.border = "2px solid #000";
                    });
                });

                // ✅ make first thumbnail active by default
                thumbnails[0].style.border = "2px solid #000";
            }
        });

// ---- AJAX Cart Functions ----
function addToCart(productId, quantity = 1, buyNow = 0, buttonElement = null) {
    // Get button element - from parameter, event, or by ID
    let addToCartBtn = buttonElement;
    if (!addToCartBtn && typeof event !== 'undefined' && event && event.target) {
        addToCartBtn = event.target;
    }
    if (!addToCartBtn) {
        addToCartBtn = document.getElementById('add-to-cart-btn') || document.querySelector('.add-to-cart-btn');
    }
    
    // Show loading state
    const originalText = addToCartBtn ? addToCartBtn.textContent : '';
    if (addToCartBtn) {
    addToCartBtn.textContent = 'Adding...';
    addToCartBtn.disabled = true;
    }

    // Get base URL
    const baseUrl = window.location.origin;
    const pathname = window.location.pathname;
    const basePath = pathname.includes('/public/') ? '/public' : '';

    // Make AJAX request
    fetch(baseUrl + basePath + '/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id: productId,
            qty: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.msg_type === 'success' || data.success) {
            // Update cart count in header
            if (typeof updateCartCount === 'function') {
                updateCartCount(data.qty || 0);
            }
            
            // Update cart sidebar
            if (typeof updateCartSidebar === 'function') {
            updateCartSidebar();
            }
            
            // Show success message
            if (typeof showNotification === 'function') {
                showNotification(data.msg || 'Product added to cart successfully!', 'success');
            }
            
            // If buy now, redirect to checkout
            if (buyNow === 1) {
                window.location.href = baseUrl + basePath + '/checkout';
                return;
            }
            
            // Open cart sidebar
            const cartSidebar = document.getElementById('cartSidebar');
            const cartOverlay = document.getElementById('cartOverlay');
            if (cartSidebar && cartOverlay) {
                cartSidebar.classList.add('active');
                cartOverlay.classList.add('active');
            }
        } else {
            if (typeof showNotification === 'function') {
            showNotification(data.msg || 'Error adding to cart', 'error');
            } else {
                alert(data.msg || 'Error adding to cart');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showNotification === 'function') {
        showNotification('Error adding to cart', 'error');
        } else {
            alert('Error adding to cart');
        }
    })
    .finally(() => {
        // Reset button state
        if (addToCartBtn) {
        addToCartBtn.textContent = originalText;
        addToCartBtn.disabled = false;
        }
    });
}

function updateCartCount(qty) {
    // Update cart count in header
    const cartCountElements = document.querySelectorAll('.cart-count, .toolbar-count');
    if (cartCountElements.length > 0) {
    cartCountElements.forEach(element => {
        element.textContent = qty || 0;
            // Also update innerHTML in case textContent doesn't work
            element.innerHTML = qty || 0;
        });
    } else {
        // If elements not found, try again after a short delay
        setTimeout(() => {
            const retryElements = document.querySelectorAll('.cart-count, .toolbar-count');
            retryElements.forEach(element => {
                element.textContent = qty || 0;
                element.innerHTML = qty || 0;
            });
        }, 100);
    }
}

function updateCartSidebar() {
    // Fetch cart data and update sidebar
    const baseUrl = window.location.origin;
    const pathname = window.location.pathname;
    // Get base path (remove /public if exists)
    const basePath = pathname.includes('/public/') ? '/public' : '';
    
    fetch(baseUrl + basePath + '/cart/data', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const cartContent = document.querySelector('.cart-content');
        if (cartContent) {
            if (data.cart && data.cart.items && data.cart.items.length > 0) {
                cartContent.innerHTML = generateCartHTML(data.cart);
                // Update cart count - ensure it's called after DOM update
                setTimeout(() => {
                    updateCartCount(data.cart.qty || 0);
                }, 50);
            } else {
                const assetsUrl = baseUrl + basePath + '/theme2/';
                cartContent.innerHTML = `
                    <img src="${assetsUrl}img/cart-cut-icon.svg" alt="Empty Cart">
                    <p>No Products In The Cart.</p>
                    <button onclick="window.location.href='${baseUrl}${basePath}'" class="return-shop-btn"> Return To Shop </button>
                `;
                // Update cart count to 0
                setTimeout(() => {
                    updateCartCount(0);
                }, 50);
            }
        }
        // Always update cart count regardless of cart content
        if (data.cart) {
            setTimeout(() => {
                updateCartCount(data.cart.qty || 0);
            }, 50);
        }
    })
    .catch(error => {
        console.error('Error fetching cart data:', error);
        // Show empty cart on error
        const cartContent = document.querySelector('.cart-content');
        if (cartContent) {
            const assetsUrl = baseUrl + basePath + '/theme2/';
            cartContent.innerHTML = `
                <img src="${assetsUrl}img/cart-cut-icon.svg" alt="Empty Cart">
                <p>No Products In The Cart.</p>
                <button onclick="window.location.href='${baseUrl}${basePath}'" class="return-shop-btn"> Return To Shop </button>
            `;
        }
    });
}

function generateCartHTML(cart) {
    const baseUrl = window.location.origin;
    const pathname = window.location.pathname;
    const basePath = pathname.includes('/public/') ? '/public' : '';
    const assetsUrl = baseUrl + basePath + '/theme2/';
    
    let html = '<div class="cart-items">';
    
    cart.items.forEach(item => {
        let imageUrl = item.image || assetsUrl + 'img/solo.webp';
        // Fix image URL if it has duplicate public
        if (imageUrl.includes('/public/public/')) {
            imageUrl = imageUrl.replace('/public/public/', '/public/');
        }
        
        const itemTotal = parseFloat(item.price) * parseInt(item.qty);
        
        html += `
            <div class="cart-item" data-product-id="${item.id}">
                <div class="cart-item-image">
                    <img src="${imageUrl}" alt="${item.name}" onerror="this.src='${assetsUrl}img/solo.webp'">
                </div>
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p class="cart-item-price">Rs: ${item.price} x ${item.qty} = Rs: ${itemTotal.toFixed(2)}</p>
                    <div class="cart-item-quantity">
                        <button class="cart-qty-btn" onclick="decrementCartItem(${item.id})">-</button>
                        <span class="cart-qty-value" id="cart-qty-${item.id}">${item.qty}</span>
                        <button class="cart-qty-btn" onclick="incrementCartItem(${item.id})">+</button>
                    </div>
                </div>
                <div class="cart-item-remove">
                    <button onclick="removeFromCart(${item.id})" class="remove-btn">&times;</button>
                </div>
            </div>
        `;
    });
    
    // Get shipping charges from cart data
    const shippingCharges = parseFloat(cart.shipping_charges || 0);
    const grandTotal = parseFloat(cart.amount) + parseFloat(shippingCharges);
    
    html += `
        </div>
        <div class="cart-summary">
            <div class="cart-subtotal">
                <span>Subtotal:</span>
                <span class="cart-subtotal-value">Rs: ${parseFloat(cart.amount).toFixed(2)}</span>
            </div>
            <div class="cart-shipping">
                <span>Shipping:</span>
                <span class="cart-shipping-value">Rs: ${parseFloat(shippingCharges).toFixed(2)}</span>
        </div>
        <div class="cart-total">
                <h3>Total: Rs: <span class="cart-total-value">${grandTotal.toFixed(2)}</span></h3>
            <p>${cart.qty} item(s) in cart</p>
            </div>
        </div>
        <div class="cart-actions">
            <button onclick="window.location.href='${baseUrl}${basePath}/checkout'" class="checkout-btn">Checkout</button>
            <button onclick="window.location.href='${baseUrl}${basePath}'" class="continue-shopping-btn">Continue Shopping</button>
        </div>
    `;
    
    return html;
}

function incrementCartItem(productId) {
    const baseUrl = window.location.origin;
    const pathname = window.location.pathname;
    const basePath = pathname.includes('/public/') ? '/public' : '';
    
    fetch(baseUrl + basePath + '/cart/increment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.cart) {
            updateCartSidebar();
            updateCartCount(data.cart.qty || 0);
        } else {
            updateCartSidebar();
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        updateCartSidebar(); // Refresh anyway
    });
    }
    
function decrementCartItem(productId) {
    const baseUrl = window.location.origin;
    const pathname = window.location.pathname;
    const basePath = pathname.includes('/public/') ? '/public' : '';
    
    fetch(baseUrl + basePath + '/cart/decrement', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.cart) {
        updateCartSidebar();
            updateCartCount(data.cart.qty || 0);
        } else {
            updateCartSidebar();
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
        updateCartSidebar(); // Refresh anyway
    });
}

function removeFromCart(productId) {
    const baseUrl = window.location.origin;
    const pathname = window.location.pathname;
    const basePath = pathname.includes('/public/') ? '/public' : '';
    
    fetch(baseUrl + basePath + '/cart/remove/' + productId, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.cart) {
        updateCartSidebar();
            updateCartCount(data.cart.qty || 0);
        } else {
            updateCartSidebar();
            updateCartCount(0);
        }
        showNotification('Item removed from cart', 'success');
    })
    .catch(error => {
        console.error('Error removing item:', error);
        updateCartSidebar(); // Refresh anyway
        updateCartCount(0);
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 300px;
    `;
    
    // Set background color based on type
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        info: '#17a2b8',
        warning: '#ffc107'
    };
    notification.style.backgroundColor = colors[type] || colors.info;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .cart-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
		margin-bottom:15px;
    }
    .cart-item-image {
        width: 80px;
        height: 80px;
    }
    .cart-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 5px;
    }
    .cart-item-details {
        flex: 1;
		padding:0px 10px;
    }
    .cart-item-details h4 {
        margin: 0 0 5px 0;
        font-size: 14px;
	    font-weight: 500;
		color: #333;
    }
    .cart-item-price {
        font-weight: 500 !important;
        color: #333 !important;
		font-size:14px !important;
		text-transform:uppercase;
		padding:0px !important;
		margin-bottom:5px !important;
    }
    .cart-item-quantity {
        display: flex;
        align-items: center;
        gap: 10px;
	    justify-content: center;
    }
    .cart-item-quantity button {
        width: 25px;
        height: 25px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        border-radius: 3px;
		padding:0px ;
		color:#333;
    }
    .cart-item-remove .remove-btn {
        background: #dc3545;
        color: white;
        border: none;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        cursor: pointer;
		padding:0px;
    }
    .cart-total {
        padding: 15px;
        border-top: 2px solid #eee;
        text-align: center;
    }
    .cart-actions {
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .checkout-btn, .continue-shopping-btn {
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .checkout-btn {
        background: #007bff;
        color: white;
    }
    .continue-shopping-btn {
        background: #6c757d;
        color: white;
    }
`;
document.head.appendChild(style);

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update cart sidebar and count on page load
    updateCartSidebar();
    
    // Also update cart count from server-side rendered value as fallback
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        // The count is already rendered from server, but we'll refresh it
        // after sidebar loads to ensure accuracy
        setTimeout(() => {
            updateCartSidebar();
        }, 100);
    }
    
    // Intercept jQuery AJAX calls to cart/add and update cart count
    if (typeof $ !== 'undefined') {
        $(document).ajaxSuccess(function(event, xhr, settings) {
            // Check if this is a cart/add request
            if (settings.url && (settings.url.includes('/cart/add') || settings.url.includes('cart/add'))) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.qty !== undefined) {
                        // Update cart count
                        updateCartCount(response.qty);
                        // Update cart sidebar
                        updateCartSidebar();
                    }
                } catch (e) {
                    // If response is not JSON, try to update anyway
                    updateCartSidebar();
                }
            }
        });
    }
});

// ---- Mobile Search Modal ----
const openMobileSearchBtn = document.getElementById('openMobileSearch');
const closeMobileSearchBtn = document.getElementById('closeMobileSearch');
const mobileSearchModal = document.getElementById('mobileSearchModal');
const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
const mobileSearchInput = document.getElementById('mobileSearchInput');
const mobileSearchResults = document.getElementById('mobileSearchResults');
let searchTimeout = null;

if (openMobileSearchBtn && mobileSearchModal) {
    openMobileSearchBtn.addEventListener('click', (e) => {
        e.preventDefault();
        mobileSearchModal.classList.add('active');
        mobileSearchOverlay.classList.add('active');
        // Focus on input when modal opens
        setTimeout(() => {
            mobileSearchInput.focus();
        }, 100);
    });
}

if (closeMobileSearchBtn) {
    closeMobileSearchBtn.addEventListener('click', () => {
        mobileSearchModal.classList.remove('active');
        mobileSearchOverlay.classList.remove('active');
        mobileSearchInput.value = '';
        mobileSearchResults.innerHTML = '';
    });
}

if (mobileSearchOverlay) {
    mobileSearchOverlay.addEventListener('click', () => {
        mobileSearchModal.classList.remove('active');
        mobileSearchOverlay.classList.remove('active');
        mobileSearchInput.value = '';
        mobileSearchResults.innerHTML = '';
    });
}

// Live search on keyup
if (mobileSearchInput) {
    mobileSearchInput.addEventListener('keyup', function(e) {
        const query = this.value.trim();
        
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // If query is less than 2 characters, clear results
        if (query.length < 2) {
            mobileSearchResults.innerHTML = '';
            return;
        }
        
        // Debounce search - wait 300ms after user stops typing
        searchTimeout = setTimeout(() => {
            performLiveSearch(query);
        }, 300);
    });
    
    // Clear results when input is empty
    mobileSearchInput.addEventListener('input', function() {
        if (this.value.trim().length === 0) {
            mobileSearchResults.innerHTML = '';
        }
    });
}

function performLiveSearch(query) {
    if (!query || query.length < 2) {
        mobileSearchResults.innerHTML = '';
        return;
    }
    
    // Show loading state with spinner
    mobileSearchResults.innerHTML = `
        <div class="search-loading">
            <div class="search-spinner"></div>
            <p>Searching...</p>
        </div>
    `;
    
    // Make AJAX request
    fetch(`/api/live-search?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        displaySearchResults(data.products);
    })
    .catch(error => {
        console.error('Search error:', error);
        mobileSearchResults.innerHTML = '<div class="search-error">Error searching products. Please try again.</div>';
    });
}

function displaySearchResults(products) {
    if (!products || products.length === 0) {
        mobileSearchResults.innerHTML = '<div class="search-no-results">No products found</div>';
        return;
    }
    
    const baseUrl = window.location.origin;
    const assetsUrl = baseUrl + '/theme2/';
    
    let html = '<ul class="search-results-list">';
    products.forEach(product => {
        let imageUrl = product.image || '';
        if (imageUrl && !imageUrl.startsWith('http')) {
            imageUrl = baseUrl + '/' + imageUrl;
        } else if (!imageUrl) {
            imageUrl = assetsUrl + 'img/solo.webp';
        }
        
        html += `
            <li class="search-result-item">
                <a href="${product.url}" onclick="closeMobileSearchModal()">
                    <div class="search-result-image">
                        <img src="${imageUrl}" alt="${product.name}" onerror="this.src='${assetsUrl}img/solo.webp'">
                    </div>
                    <div class="search-result-info">
                        <h4>${product.name}</h4>
                        <p class="search-result-price">Rs: ${product.price}</p>
                    </div>
                </a>
            </li>
        `;
    });
    html += '</ul>';
    
    mobileSearchResults.innerHTML = html;
}

function closeMobileSearchModal() {
    if (mobileSearchModal) {
        mobileSearchModal.classList.remove('active');
        mobileSearchOverlay.classList.remove('active');
        mobileSearchInput.value = '';
        mobileSearchResults.innerHTML = '';
    }
}


