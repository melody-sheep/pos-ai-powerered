// Product Manager - Simplified Version
class ProductManager {
    constructor() {
        console.log('Product Manager initialized - Ready for Add Product button');
        // Don't load products or setup tabs
    }

    // Keep this method for future use if needed
    showAddProductModal() {
        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot && alpineRoot.__x) {
            alpineRoot.__x.$data.showProductModal = true;
        } else {
            console.error('Alpine component not found');
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
