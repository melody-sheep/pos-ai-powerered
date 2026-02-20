// Product Manager - Clean version with NO animations
class ProductManager {
    constructor(config = {}) {
        console.log('Product Manager initialized');
        
        this.config = {
            gridSelector: '#productsGrid',
            products: [],
            currentCategory: 'breads',
            onProductSelect: null,
            ...config
        };

        this.products = this.config.products;
        this.currentCategory = this.config.currentCategory;
        
        this.init();
    }

    init() {
        this.initModalTriggers();
        this.setupEventListeners();
        // Initial load of products
        setTimeout(() => {
            this.refreshProducts();
        }, 100);
    }

    initModalTriggers() {
        // Listen for Add Product button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-product-btn') || e.target.closest('[data-add-product]')) {
                this.openAddProductModal();
            }
        });
    }

    setupEventListeners() {
        // Listen for product added event
        window.addEventListener('product-added', (event) => {
            console.log('Product added event received:', event.detail);
            const addedCategory = event.detail?.category;
            
            // Always refresh the category that was added
            if (addedCategory) {
                console.log('Refreshing category:', addedCategory);
                
                // If the added category matches current, refresh immediately
                if (addedCategory === this.currentCategory) {
                    this.refreshProducts();
                } else {
                    // If it's a different category, just show a notification
                    this.showNotification(`Product added to ${addedCategory} category`, 'info');
                }
            }
        });

        // Listen for tab changes
        window.addEventListener('tabChanged', (event) => {
            const tabId = event.detail.tabId;
            console.log('Tab changed to:', tabId);
            
            // Only refresh if it's a product category tab
            if (['breads', 'cakes', 'beverages'].includes(tabId)) {
                this.currentCategory = tabId;
                this.refreshProducts();
            } else {
                // Clear the grid for non-product tabs
                this.clearGrid();
            }
        });

        // Listen for manual refresh requests
        window.addEventListener('refresh-products', (event) => {
            console.log('Manual refresh requested', event.detail);
            const category = event.detail?.category || this.currentCategory;
            this.currentCategory = category;
            this.refreshProducts();
        });
    }

    clearGrid() {
        const grid = document.querySelector(this.config.gridSelector);
        if (grid) {
            grid.innerHTML = '';
        }
    }

    openAddProductModal() {
        console.log('Opening add product modal');
        
        // Dispatch Alpine event to open modal
        window.dispatchEvent(new CustomEvent('open-modal', { 
            detail: 'add-product-modal' 
        }));
        
        // Also try Alpine data method as fallback
        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot && alpineRoot.__x) {
            const cashierData = alpineRoot.__x.$data;
            cashierData.showProductModal = true;
        }
    }

    switchCategory(category) {
        console.log('Switching to category:', category);
        this.currentCategory = category;
        this.refreshProducts();
    }

    async refreshProducts() {
        console.log('Refreshing products for category:', this.currentCategory);
        
        try {
            const url = `/cashier/products?category=${this.currentCategory}&_=${Date.now()}`; // Add timestamp to prevent caching
            console.log('Fetching from:', url);
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`Failed to fetch products: ${response.status} ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('API Response:', data);
            
            // Update products and render
            this.products = data.products || [];
            console.log('Products updated:', this.products.length, 'items');
            
            this.renderProductGrid();
            
        } catch (error) {
            console.error('Error refreshing products:', error);
            this.showNotification('Failed to refresh products: ' + error.message, 'error');
        }
    }

    renderProductGrid() {
        const grid = document.querySelector(this.config.gridSelector);
        if (!grid) {
            console.error('Products grid not found:', this.config.gridSelector);
            return;
        }

        console.log('Rendering grid with', this.products.length, 'products for category:', this.currentCategory);

        let html = '';

        // If no products, show empty state with centered content
        if (!this.products || this.products.length === 0) {
            console.log('No products, showing empty state');
            html = this.renderEmptyState();
        } else {
            // Add product cards for each product - these will be left-aligned by the grid
            this.products.forEach(product => {
                html += this.renderProductCard(product);
            });
            
            // Always add the "Add Product" frame at the end - also left-aligned
            html += this.renderAddProductFrame();
        }

        grid.innerHTML = html;
        console.log('Grid rendered successfully');
    }

    renderProductCard(product) {
        const imageUrl = product.image_path 
            ? `/storage/${product.image_path}`
            : null;

        return `
            <div class="product-card bg-white rounded-xl border border-gray-100  overflow-hidden">
                <!-- Product Image -->
                <div class="relative h-32 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-3">
                    ${imageUrl 
                        ? `<img src="${imageUrl}" 
                             alt="${product.name}"
                             class="max-w-full max-h-full object-contain">`
                        : `<div class="w-16 h-16 bg-pink-50 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-pink-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>`
                    }

                    <!-- Stock Badge -->
                    ${product.stock < 1 
                        ? '<span class="absolute top-2 right-2 bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full">Out of Stock</span>'
                        : product.stock < 6 
                            ? '<span class="absolute top-2 right-2 bg-yellow-100 text-yellow-600 text-xs font-medium px-2 py-1 rounded-full">Low Stock</span>'
                            : ''
                    }
                </div>

                <!-- Product Info -->
                <div class="p-3">
                    <h3 class="font-semibold text-gray-800 text-sm mb-1 line-clamp-1" title="${product.name}">
                        ${product.name}
                    </h3>
                    
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-pink-border">₱${parseFloat(product.price).toFixed(2)}</span>
                        <span class="text-xs text-gray-500">Stock: ${product.stock}</span>
                    </div>

                    <!-- Add to Order Button -->
                    <button 
                        onclick="addToOrder(${product.id})"
                        class="w-full bg-gradient-to-r from-pink-border to-pink-400 text-white text-xs font-medium py-2 rounded-lg hover:opacity-90 flex items-center justify-center gap-1"
                        ${product.stock < 1 ? 'disabled' : ''}
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add to Order</span>
                    </button>
                </div>
            </div>
        `;
    }

    renderEmptyState() {
        return `
            <div class="product-selection-section">
                <!-- Icon -->
                <div class="selection-icon">
                    <svg class="w-16 h-16 mx-auto text-custom-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                
                <!-- Text (Title) -->
                <h3 class="selection-title">
                    Product Selection
                </h3>
                
                <!-- Sub text -->
                <p class="selection-subtext">
                    Click the button below to add new products to your inventory
                </p>
                
                <!-- Add Product Button -->
                <button 
                    class="add-product-btn"
                    onclick="window.productManager?.openAddProductModal()"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Product</span>
                </button>
            </div>
        `;
    }

    renderAddProductFrame() {
        return `
            <div class="add-product-frame group cursor-pointer" onclick="window.productManager?.openAddProductModal()" data-add-product>
                <div class="add-product-frame-content">
                    <!-- Plus Icon with Circle Background -->
                    <div class="w-16 h-16 bg-pink-50 rounded-full flex items-center justify-center mb-3 mx-auto">
                        <svg class="w-8 h-8 text-pink-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    
                    <!-- Add Product Text -->
                    <span class="text-sm font-medium text-gray-700">
                        Add New Product
                    </span>
                    <span class="text-xs text-gray-400 mt-1 text-center block">
                        Click to add<br>new item
                    </span>
                </div>
            </div>
        `;
    }

    addProductToList(product) {
        console.log('Adding product to list:', product);
        
        // Add to products array if it matches current category
        if (product.category === this.currentCategory) {
            this.products.push(product);
            this.renderProductGrid();
        }
        
        this.showNotification('Product added successfully!', 'success');
    }

    showNotification(message, type = 'success') {
        // Try to use Alpine's notification system
        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot && alpineRoot.__x) {
            const cashierData = alpineRoot.__x.$data;
            if (cashierData.notifications) {
                const newNotification = {
                    id: Date.now(),
                    type: type,
                    title: type === 'success' ? 'Success' : 'Info',
                    message: message,
                    time: 'Just now',
                    read: false
                };
                cashierData.notifications.unshift(newNotification);
                if (cashierData.unreadCount !== undefined) {
                    cashierData.unreadCount = (cashierData.unreadCount || 0) + 1;
                }
                return;
            }
        }
        
        // Fallback to alert
        alert(message);
    }
}

// Make it globally available
window.ProductManager = ProductManager;

// Global addToOrder function
window.addToOrder = function(productId) {
    console.log('Adding product to order:', productId);
    // Implement your add to order logic here
    const productManager = window.productManager;
    if (productManager) {
        productManager.showNotification(`Product ${productId} added to order`, 'success');
    } else {
        alert(`Product ${productId} added to order (demo)`);
    }
};

// Auto-initialize when DOM is ready
function initializeProductManager() {
    console.log('Initializing Product Manager');
    
    // Check if already initialized
    if (!window.productManager) {
        window.productManager = new ProductManager({
            gridSelector: '#productsGrid',
            products: [],
            currentCategory: 'breads'
        });
        console.log('ProductManager instance created:', window.productManager);
    }
}

// Initialize immediately
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeProductManager);
} else {
    initializeProductManager();
}
