// Product Manager - With Add Product Modal functionality
class ProductManager {
    constructor() {
        console.log('Product Manager initialized');
        this.initModalTriggers();
        this.currentCategory = 'breads'; // Default category
    }

    initModalTriggers() {
        // Listen for Add Product button clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-product-btn') || e.target.closest('[data-add-product]')) {
                this.openAddProductModal();
            }
        });

        // Listen for form submission
        const form = document.getElementById('addProductForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleAddProduct();
            });
        }

        // Listen for tab clicks to update current category
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.addEventListener('click', (e) => {
                this.currentCategory = e.currentTarget.dataset.tab;
            });
        });
    }

    openAddProductModal() {
        // Dispatch Alpine event to open modal
        window.dispatchEvent(new CustomEvent('open-modal', { 
            detail: 'add-product-modal' 
        }));
        
        // Also try Alpine data method as fallback
        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot && alpineRoot.__x) {
            alpineRoot.__x.$data.showProductModal = true;
        }
    }

    async refreshProducts() {
        console.log('Refreshing products for category:', this.currentCategory);
        
        try {
            const response = await fetch(`/cashier/products?category=${this.currentCategory}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to fetch products');
            }
            
            const data = await response.json();
            
            // Dispatch event with updated products
            window.dispatchEvent(new CustomEvent('products-updated', { 
                detail: data 
            }));
            
            // Update UI if needed
            this.updateProductsDisplay(data.products);
            
        } catch (error) {
            console.error('Error refreshing products:', error);
        }
    }

    updateProductsDisplay(products) {
        // Find the products container
        const productsContainer = document.querySelector('.product-selection-section');
        if (!productsContainer) return;

        if (products && products.length > 0) {
            // Products exist - you can update your product grid here
            console.log('Products updated:', products);
        } else {
            // No products - show empty state
            console.log('No products found');
        }
    }

    handleAddProduct() {
        // TODO: Implement product addition logic
        console.log('Adding new product...');
        
        // Show success notification
        this.showNotification('Product added successfully!', 'success');
        
        // Close modal
        window.dispatchEvent(new CustomEvent('close-modal', { 
            detail: 'add-product-modal' 
        }));
    }

    showNotification(message, type = 'success') {
        // Use Alpine's notification system
        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot && alpineRoot.__x) {
            // Add to notifications array
            const newNotification = {
                id: Date.now(),
                type: type,
                title: type === 'success' ? 'Success' : 'Info',
                message: message,
                time: 'Just now',
                read: false
            };
            
            // Access Alpine data and update
            const cashierData = alpineRoot.__x.$data;
            if (cashierData.notifications) {
                cashierData.notifications.unshift(newNotification);
                cashierData.unreadCount = (cashierData.unreadCount || 0) + 1;
            }
        }
    }
}

// Make it globally available
window.ProductManager = ProductManager;

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready, initializing Product Manager');
    window.productManager = new ProductManager();
});