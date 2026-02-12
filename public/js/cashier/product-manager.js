    // Product Manager - Self Contained Version
class ProductManager {
    constructor() {
        console.log('Product Manager initializing...');
        this.currentCategory = 'breads';
        this.cart = [];

        // Initialize immediately since DOM is ready
        this.init();
    }

    init() {
        console.log('Product Manager initialized');
        this.loadProducts(this.currentCategory);
        this.setupTabListeners();
    }

    setupTabListeners() {
        const tabItems = document.querySelectorAll('.tab-item');
        console.log('Found tabs:', tabItems.length);
        
        tabItems.forEach(tab => {
            tab.removeEventListener('click', this.handleTabClick);
            tab.addEventListener('click', (e) => this.handleTabClick(e, tab));
        });
    }

    handleTabClick(e, tab) {
        const tabName = tab.dataset.tab;
        console.log('Tab clicked:', tabName);
        
        if (['breads', 'cakes', 'beverages'].includes(tabName)) {
            this.currentCategory = tabName;
            this.loadProducts(tabName);
        }
    }

    async loadProducts(category) {
        console.log('Loading products for:', category);
        
        try {
            // Use absolute URL
            const response = await fetch(`/cashier/products?category=${category}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Products loaded:', data);
            this.renderProducts(data.products);
        } catch (error) {
            console.error('Error loading products:', error);
            
            // Show error in product column
            const productColumn = document.querySelector('.column-products .column-content');
            if (productColumn) {
                productColumn.innerHTML = `
                    <div class="placeholder-content">
                        <div class="placeholder-icon text-red-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="placeholder-title text-red-600">Error Loading Products</h3>
                        <p class="placeholder-text">Please check console for details</p>
                    </div>
                `;
            }
        }
    }

    renderProducts(products) {
        const productColumn = document.querySelector('.column-products .column-content');
        if (!productColumn) {
            console.error('Product column not found');
            return;
        }

        if (!products || products.length === 0) {
            productColumn.innerHTML = `
                <div class="placeholder-content">
                    <div class="placeholder-icon">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <h3 class="placeholder-title">No Products Available</h3>
                    <p class="placeholder-text">No items found in this category.</p>
                </div>
            `;
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">';
        
        products.forEach(product => {
            html += `
                <div class="product-card bg-white rounded-xl p-4 border border-gray-100 hover:border-pink-border transition-all duration-300 hover:shadow-lg cursor-pointer group" data-product-id="${product.id}">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                            ${product.image || '🛒'}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">${product.name}</h4>
                                    <p class="text-sm text-gray-500 mt-0.5">${product.description.substring(0, 30)}...</p>
                                </div>
                                <span class="text-lg font-bold text-custom-gray">₱${product.price.toFixed(2)}</span>
                            </div>
                            <button class="add-to-cart-btn mt-3 text-xs bg-gradient-to-r from-pink-border to-[#FFB0C8] text-white px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity flex items-center space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        productColumn.innerHTML = html;
        
        // Add event listeners to product cards
        this.attachProductEvents();
    }

    attachProductEvents() {
        // Product card click
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', (e) => {
                // Don't trigger if clicking the button
                if (e.target.closest('.add-to-cart-btn')) return;
                
                const productId = card.dataset.productId;
                this.fetchAndShowProduct(productId);
            });
        });
        
        // Add to cart buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const card = e.target.closest('.product-card');
                const productId = card.dataset.productId;
                this.fetchAndShowProduct(productId);
            });
        });
    }

    async fetchAndShowProduct(productId) {
        try {
            const response = await fetch(`/cashier/products/${productId}`);
            const product = await response.json();
            this.showProductModal(product);
        } catch (error) {
            console.error('Error fetching product:', error);
        }
    }

    showProductModal(product) {
        // Find Alpine component
        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot && alpineRoot.__x) {
            alpineRoot.__x.$data.selectedProduct = product;
            alpineRoot.__x.$data.quantity = 1;
            alpineRoot.__x.$data.showProductModal = true;
        } else {
            console.error('Alpine component not found');
        }
    }

    addToCart(product, quantity = 1) {
        const existingItem = this.cart.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                ...product,
                quantity: quantity
            });
        }
        
        console.log('Cart updated:', this.cart);
        alert(`Added ${quantity} x ${product.name} to cart!`);
    }
}

// Make it globally available
window.ProductManager = ProductManager;
