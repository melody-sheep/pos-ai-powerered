// Product Manager - Clean version with pagination indicators
class ProductManager {
    constructor(config = {}) {
        console.log('Product Manager initialized');
        
        this.config = {
            gridSelector: '#productsGrid',
            products: [],
            currentCategory: 'breads',
            currentPage: 1,
            itemsPerPage: 8,
            onProductSelect: null,
            ...config
        };

        this.products = this.config.products;
        this.currentCategory = this.config.currentCategory;
        this.currentPage = this.config.currentPage;
        
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
                this.currentPage = 1; // Reset to first page
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
        this.currentPage = 1; // Reset to first page
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

        // Calculate pagination
        const itemsPerPage = this.config.itemsPerPage; // 2 columns x 4 rows
        const totalPages = Math.ceil(this.products.length / itemsPerPage);
        this.currentPage = Math.min(this.currentPage, totalPages || 1);

        const startIndex = (this.currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageProducts = this.products.slice(startIndex, endIndex);

        let html = '';

        // If no products, show empty state with centered content
        if (!this.products || this.products.length === 0) {
            console.log('No products, showing empty state');
            html = this.renderEmptyState();
        } else {
            // Add product cards for each product in current page - these will be left-aligned by the grid
            pageProducts.forEach(product => {
                html += this.renderProductCard(product);
            });
            
            // Add "Add Product" frame if on last page and there's space
            if (this.currentPage === totalPages && pageProducts.length < itemsPerPage) {
                html += this.renderAddProductFrame();
            }
        }

        grid.innerHTML = html;

        // Add page indicator
        this.renderPageIndicator(totalPages);

        console.log('Grid rendered successfully');
    }

    renderPageIndicator(totalPages) {
        // Remove existing indicator
        const existingIndicator = document.querySelector('.page-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        if (totalPages <= 1) return;

        const grid = document.querySelector(this.config.gridSelector);
        if (!grid) return;

        const indicator = document.createElement('div');
        indicator.className = 'page-indicator';

        // Create dots
        const dotsHtml = Array.from({length: totalPages}, (_, i) => {
            const pageNum = i + 1;
            return `<div class="page-dot${pageNum === this.currentPage ? ' active' : ''}" onclick="window.productManager.goToPage(${pageNum})"></div>`;
        }).join('');

        indicator.innerHTML = `
            <div class="page-indicator-dots">${dotsHtml}</div>
            <div class="page-indicator-text">${this.currentPage}/${totalPages}</div>
            <div class="page-indicator-icon">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M7 13l3 3 7-7"/>
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
        `;

        // Insert after the grid
        grid.parentNode.insertBefore(indicator, grid.nextSibling);
    }

    goToPage(pageNum) {
        const itemsPerPage = this.config.itemsPerPage;
        const totalPages = Math.ceil(this.products.length / itemsPerPage);
        if (pageNum >= 1 && pageNum <= totalPages) {
            this.currentPage = pageNum;
            this.renderProductGrid();
        }
    }

    getStockStatus(stock) {
        if (stock < 1) {
            return {
                text: 'Out of Stock',
                dotColor: 'bg-red-500',
                textColor: 'text-red-600'
            };
        } else if (stock < 6) {
            return {
                text: 'Low Stock',
                dotColor: 'bg-yellow-500',
                textColor: 'text-yellow-600'
            };
        } else {
            return {
                text: 'In Stock',
                dotColor: 'bg-green-500',
                textColor: 'text-green-600'
            };
        }
    }

    // Category icons mapping (same as modal)
    getCategoryIcon(category) {
        const icons = {
            'breads': '🥖',
            'cakes': '🎂',
            'beverages': '☕'
        };
        return icons[category] || '📦';
    }

    // Rating icons and colors mapping (same as modal)
    getRatingDisplay(rating) {
        const ratings = {
            'top_rated': { icon: '⭐', label: 'Top Rated', color: 'text-yellow-500', bgColor: 'bg-yellow-50' },
            'recommended': { icon: '👍', label: 'Recommended', color: 'text-blue-500', bgColor: 'bg-blue-50' },
            'best_selling': { icon: '🔥', label: 'Best Selling', color: 'text-orange-500', bgColor: 'bg-orange-50' },
            'new_arrival': { icon: '✨', label: 'New Arrival', color: 'text-purple-500', bgColor: 'bg-purple-50' },
            'popular': { icon: '🏆', label: 'Popular', color: 'text-green-500', bgColor: 'bg-green-50' }
        };
        return ratings[rating] || null;
    }


    renderProductCard(product) {
        const imageUrl = product.image_path 
            ? `/storage/${product.image_path}`
            : null;
        
        const stockStatus = this.getStockStatus(product.stock);
        const categoryIcon = this.getCategoryIcon(product.category);
        const ratingDisplay = this.getRatingDisplay(product.rating);

        return `
            <div class="product-card bg-white rounded-xl border border-gray-100 overflow-hidden flex flex-col">
                <!-- Product Image - Fixed height, will not change -->
                <div class="product-image-container relative overflow-hidden flex-shrink-0" style="height: 110px; min-height: 110px; max-height: 110px;">
                    ${imageUrl 
                        ? `<img src="${imageUrl}" 
                             alt="${product.name}"
                             class="w-full h-full object-cover">`
                        : `<div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                                <div class="w-16 h-16 bg-pink-50 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-pink-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>`
                    }
                </div>

                <!-- Product Info - Fixed layout that doesn't expand -->
                <div class="p-3 flex flex-col flex-grow">
                    <!-- Product Name with Stock Indicator -->
                    <div class="flex items-start justify-between mb-1 gap-2">
                        <h3 class="font-semibold text-gray-800 text-base line-clamp-1 flex-1" title="${product.name}">
                            ${product.name}
                        </h3>
                        <div class="flex items-center gap-1.5 flex-shrink-0 mt-0.5">
                            <span class="w-2 h-2 rounded-full ${stockStatus.dotColor}"></span>
                            <span class="text-xs font-medium ${stockStatus.textColor} whitespace-nowrap">${stockStatus.text}</span>
                        </div>
                    </div>
                    
                    <!-- Price and Quantity -->
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-lg font-bold text-pink-border">₱${parseFloat(product.price).toFixed(2)}</span>
                        <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                            Qty: ${product.stock}
                        </span>
                    </div>

                    <!-- Rating - Fixed height container -->
                    <div class="h-6 mb-1">
                        ${ratingDisplay ? `
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${ratingDisplay.bgColor} ${ratingDisplay.color}">
                            <span class="mr-1">${ratingDisplay.icon}</span>
                            <span>${ratingDisplay.label}</span>
                        </span>
                        ` : ''}
                    </div>

                    <!-- Category - Fixed height container -->
                    <div class="h-6 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            <span class="mr-1">${categoryIcon}</span>
                            <span class="capitalize">${product.category}</span>
                        </span>
                    </div>

                    <!-- Add to Order Button - Always at bottom -->
                    <div class="mt-auto mb-2">
                        <button 
                            onclick="addToOrder(${product.id})"
                            class="w-full text-sm font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 ease-in-out flex items-center justify-center gap-2 group"
                            style="background-color: #FFC5D9; color: #484545;"
                            onmouseover="this.style.backgroundColor='#FF0059'; this.style.color='white'; this.querySelector('.plus-icon').style.display='inline-block';"
                            onmouseout="this.style.backgroundColor='#FFC5D9'; this.style.color='#484545'; this.querySelector('.plus-icon').style.display='none';"
                            onmousedown="this.style.backgroundColor='#FF0059'; this.style.color='white'; this.querySelector('.plus-icon').style.display='inline-block';"
                            onmouseup="this.style.backgroundColor='#FF0059'; this.style.color='white';"
                            ${product.stock < 1 ? 'disabled' : ''}
                        >
                            <svg class="plus-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Add to Order</span>
                        </button>
                    </div>
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
            currentCategory: 'breads',
            currentPage: 1,
            itemsPerPage: 8
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
