$(document).ready(function() {
    loadProducts();
});

function loadProducts() {
    $.ajax({
        url: 'api/get_products.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayProducts(response.products);
            }
        },
        error: function() {
            showNotification('Failed to load products', 'error');
        }
    });
}

function displayProducts(products) {
    const productList = $('.product-list');
    productList.empty();
    
    products.forEach(function(product) {
        const card = `
            <div class="card" data-product-id="${product.id}">
                <img src="${product.image}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p class="price">₹${parseFloat(product.price).toFixed(2)}</p>
                <button class="add-to-cart-btn" onclick="addToCart(${product.id})">Add to Cart</button>
            </div>
        `;
        productList.append(card);
    });
}
