/**
 * PERFUME PALACE - MAIN JAVASCRIPT
 * Professional Ecommerce Platform
 */

$(document).ready(function() {
    // Initialize components
    initBackToTop();
    initSearch();
    initNewsletterForm();
    updateCartCount();
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        const href = $(this).attr('href');
        if (href && href !== '#' && href.length > 1) {
            const target = $(href);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800);
            }
        }
    });
});

/**
 * Back to Top Button
 */
function initBackToTop() {
    const backToTop = $('#back-to-top');
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 300) {
            backToTop.addClass('show');
        } else {
            backToTop.removeClass('show');
        }
    });
    
    backToTop.on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 800);
    });
}

/**
 * Live Search
 */
function initSearch() {
    let searchTimeout;
    const searchInput = $('#search-input');
    const searchResults = $('#search-results');
    
    searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();
        
        if (query.length < 2) {
            searchResults.removeClass('show').empty();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, 300);
    });
    
    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-box').length) {
            searchResults.removeClass('show');
        }
    });
}

function performSearch(query) {
    showLoading();
    
    $.ajax({
        url: 'api/search_products.php',
        method: 'GET',
        data: { q: query },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            displaySearchResults(response.products || []);
        },
        error: function() {
            hideLoading();
            showToast('Search failed', 'error');
        }
    });
}

function displaySearchResults(products) {
    const searchResults = $('#search-results');
    searchResults.empty();
    
    if (products.length === 0) {
        searchResults.html('<div class="p-3 text-center text-muted">No products found</div>');
    } else {
        products.forEach(function(product) {
            const price = product.sale_price || product.price;
            const resultItem = `
                <a href="product-details.php?id=${product.id}" class="search-result-item">
                    <img src="${product.image}" alt="${product.name}">
                    <div class="search-result-info">
                        <h6>${product.name}</h6>
                        <p class="price">${formatPrice(price)}</p>
                    </div>
                </a>
            `;
            searchResults.append(resultItem);
        });
    }
    
    searchResults.addClass('show');
}

/**
 * Newsletter Form
 */
function initNewsletterForm() {
    $('#newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        const email = $(this).find('input[type="email"]').val();
        
        $.ajax({
            url: 'api/newsletter_subscribe.php',
            method: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#newsletter-form')[0].reset();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function() {
                showToast('Subscription failed', 'error');
            }
        });
    });
}

/**
 * Add to Cart
 */
function addToCart(productId, quantity = 1) {
    // Check if product ID is valid
    if (!productId || productId <= 0) {
        showToast('Product ID required', 'error');
        return;
    }
    
    showLoading();
    
    $.ajax({
        url: 'api/cart_add.php',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            csrf_token: typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : ''
        },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            
            if (response.success) {
                showToast('Product added to cart', 'success');
                updateCartCount();
                
                // Show cart animation
                animateCartIcon();
            } else {
                if (response.redirect) {
                    showToast(response.message, 'info');
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    showToast(response.message, 'error');
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            if (xhr.status === 401) {
                showToast('Please login to add items to cart', 'info');
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 1500);
            } else {
                showToast('Failed to add to cart', 'error');
            }
        }
    });
}

/**
 * Add to Wishlist
 */
function addToWishlist(productId) {
    $.ajax({
        url: 'api/add_to_wishlist.php',
        method: 'POST',
        data: {
            product_id: productId,
            csrf_token: CSRF_TOKEN
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('Added to wishlist', 'success');
                updateWishlistCount();
                
                // Toggle heart icon
                $(`.wishlist-btn[data-product-id="${productId}"]`)
                    .find('i')
                    .toggleClass('far fas');
            } else {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    showToast(response.message, 'error');
                }
            }
        },
        error: function() {
            showToast('Failed to add to wishlist', 'error');
        }
    });
}

/**
 * Remove from Wishlist
 */
function removeFromWishlist(productId) {
    $.ajax({
        url: 'api/remove_from_wishlist.php',
        method: 'POST',
        data: {
            product_id: productId,
            csrf_token: CSRF_TOKEN
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('Removed from wishlist', 'success');
                updateWishlistCount();
                
                // Remove item from page if on wishlist page
                $(`.wishlist-item[data-product-id="${productId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function() {
            showToast('Failed to remove from wishlist', 'error');
        }
    });
}

/**
 * Update Cart Count
 */
function updateCartCount() {
    $.ajax({
        url: 'api/get_cart_count.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const count = response.count;
                const badge = $('#cart-count-badge');
                
                if (count > 0) {
                    badge.text(count).show();
                } else {
                    badge.hide();
                }
            }
        }
    });
}

/**
 * Update Wishlist Count
 */
function updateWishlistCount() {
    $.ajax({
        url: 'api/get_wishlist_count.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const count = response.count;
                const badge = $('.nav-icon .fa-heart').siblings('.badge');
                
                if (count > 0) {
                    if (badge.length) {
                        badge.text(count);
                    } else {
                        $('.nav-icon .fa-heart').parent().append(`<span class="badge">${count}</span>`);
                    }
                } else {
                    badge.remove();
                }
            }
        }
    });
}

/**
 * Animate Cart Icon
 */
function animateCartIcon() {
    const cartIcon = $('.fa-shopping-cart').parent();
    cartIcon.addClass('animate__animated animate__bounce');
    
    setTimeout(function() {
        cartIcon.removeClass('animate__animated animate__bounce');
    }, 1000);
}

/**
 * Quick View Modal
 */
function showQuickView(productId) {
    showLoading();
    
    $.ajax({
        url: 'api/get_product_details.php',
        method: 'GET',
        data: { id: productId },
        dataType: 'json',
        success: function(response) {
            hideLoading();
            
            if (response.success) {
                displayQuickViewModal(response.product);
            } else {
                showToast('Failed to load product', 'error');
            }
        },
        error: function() {
            hideLoading();
            showToast('Failed to load product', 'error');
        }
    });
}

function displayQuickViewModal(product) {
    const price = product.sale_price || product.price;
    const originalPrice = product.sale_price ? product.price : null;
    
    const modalHtml = `
        <div class="modal fade" id="quickViewModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${product.name}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="${product.image}" alt="${product.name}" class="img-fluid rounded">
                            </div>
                            <div class="col-md-6">
                                <div class="product-rating mb-3">
                                    <span class="stars">${generateStars(product.rating_avg)}</span>
                                    <span class="rating-count">(${product.rating_count} reviews)</span>
                                </div>
                                <div class="product-price mb-3">
                                    <span class="current-price">${formatPrice(price)}</span>
                                    ${originalPrice ? `<span class="original-price">${formatPrice(originalPrice)}</span>` : ''}
                                </div>
                                <p class="product-description">${product.short_description || product.description}</p>
                                <div class="product-meta mb-3">
                                    <p><strong>Category:</strong> ${product.category_name}</p>
                                    <p><strong>Brand:</strong> ${product.brand_name || 'N/A'}</p>
                                    <p><strong>Stock:</strong> ${product.stock > 0 ? 'In Stock' : 'Out of Stock'}</p>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-primary btn-lg w-100 mb-2" onclick="addToCart(${product.id})">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                    <a href="product-details.php?id=${product.id}" class="btn btn-outline-primary w-100">
                                        View Full Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#quickViewModal').remove();
    
    // Append and show modal
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    modal.show();
    
    // Remove modal from DOM when hidden
    $('#quickViewModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

/**
 * Show Loading Spinner
 */
function showLoading() {
    $('#loading-spinner').addClass('show');
}

/**
 * Hide Loading Spinner
 */
function hideLoading() {
    $('#loading-spinner').removeClass('show');
}

/**
 * Show Toast Notification
 */
function showToast(message, type = 'info') {
    const icon = {
        success: 'success',
        error: 'error',
        warning: 'warning',
        info: 'info'
    }[type] || 'info';
    
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

/**
 * Show Confirmation Dialog
 */
function showConfirm(title, text, confirmText = 'Yes', cancelText = 'No') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d4af37',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    });
}

/**
 * Format Price
 */
function formatPrice(price) {
    return '₹' + parseFloat(price).toFixed(2);
}

/**
 * Generate Star Rating HTML
 */
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5 ? 1 : 0;
    const emptyStars = 5 - fullStars - halfStar;
    
    let html = '';
    
    for (let i = 0; i < fullStars; i++) {
        html += '<i class="fas fa-star"></i>';
    }
    
    if (halfStar) {
        html += '<i class="fas fa-star-half-alt"></i>';
    }
    
    for (let i = 0; i < emptyStars; i++) {
        html += '<i class="far fa-star"></i>';
    }
    
    return html;
}

/**
 * Debounce Function
 */
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

/**
 * Throttle Function
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Lazy Load Images
 */
function initLazyLoad() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading if supported
if ('IntersectionObserver' in window) {
    initLazyLoad();
}
