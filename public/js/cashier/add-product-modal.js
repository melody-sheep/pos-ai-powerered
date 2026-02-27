// Add Product Modal Alpine.js Component
document.addEventListener('alpine:init', () => {
    Alpine.data('addProductForm', () => ({
        // Form fields
        productName: '',
        category: '',
        rating: 'none',
        stock: '',
        price: '',
        description: '',
        imagePreview: null,
        imageFile: null,
        
        // UI states
        isSubmitting: false,
        submitSuccess: false,
        submitError: null,
        
        // Error states
        errors: {
            productName: false,
            category: false,
            rating: false,
            stock: false,
            price: false,
            image: false
        },
        
        // Error messages from server
        serverErrors: {},
        
        // Touched fields for validation
        touched: {
            productName: false,
            category: false,
            rating: false,
            stock: false,
            price: false
        },
        
        // Computed property for form validity
        get isFormValid() {
            return this.productName.trim() !== '' && 
                   this.category !== '' && 
                   this.stock !== '' && 
                   this.price !== '' && 
                   parseFloat(this.price) > 0;
        },

        // Category options
        get categoryOptions() {
            return [
                { value: 'breads', label: 'Breads', color: 'text-amber-600', bgColor: 'bg-amber-50', borderColor: 'border-amber-200' },
                { value: 'cakes', label: 'Cakes', color: 'text-pink-600', bgColor: 'bg-pink-50', borderColor: 'border-pink-200' },
                { value: 'beverages', label: 'Beverages', color: 'text-blue-600', bgColor: 'bg-blue-50', borderColor: 'border-blue-200' }
            ];
        },

        // Rating options
        get ratingOptions() {
            return [
                { value: 'none', label: 'No Rating', color: 'text-gray-500', bgColor: 'bg-gray-50' },
                { value: 'top_rated', label: 'Top Rated', color: 'text-yellow-500', bgColor: 'bg-yellow-50' },
                { value: 'recommended', label: 'Recommended', color: 'text-blue-500', bgColor: 'bg-blue-50' },
                { value: 'best_selling', label: 'Best Selling', color: 'text-orange-500', bgColor: 'bg-orange-50' },
                { value: 'new_arrival', label: 'New Arrival', color: 'text-purple-500', bgColor: 'bg-purple-50' },
                { value: 'popular', label: 'Popular', color: 'text-green-500', bgColor: 'bg-green-50' }
            ];
        },

        // Get selected category display
        get selectedCategoryDisplay() {
            return this.categoryOptions.find(opt => opt.value === this.category) || null;
        },

        // Get selected rating display
        get selectedRatingDisplay() {
            return this.ratingOptions.find(opt => opt.value === this.rating) || this.ratingOptions[0];
        },
        
        // Stock status
        get stockStatus() {
            if (this.stock === '' || this.stock === null) return '';
            const stockNum = parseInt(this.stock);
            if (stockNum === 0) return 'out';
            if (stockNum < 6) return 'low';
            return 'in';
        },
        
        // Validate specific field
        validateField(field) {
            this.touched[field] = true;
            
            switch(field) {
                case 'productName':
                    this.errors.productName = this.productName.trim() === '';
                    break;
                case 'category':
                    this.errors.category = this.category === '';
                    break;
                case 'rating':
                    this.errors.rating = false;
                    break;
                case 'stock':
                    this.errors.stock = this.stock === '' || parseInt(this.stock) < 0;
                    break;
                case 'price':
                    this.errors.price = this.price === '' || parseFloat(this.price) <= 0;
                    break;
            }
        },
        
        // Validate all fields
        validateAll() {
            this.validateField('productName');
            this.validateField('category');
            this.validateField('rating');
            this.validateField('stock');
            this.validateField('price');
            
            return !this.errors.productName && 
                   !this.errors.category && 
                   !this.errors.rating && 
                   !this.errors.stock && 
                   !this.errors.price;
        },
        
        // Image handling
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFile = file;
                this.imagePreview = URL.createObjectURL(file);
                this.errors.image = false;
            }
        },
        
        // Handle form submission
        async submitForm() {
            if (!this.validateAll()) {
                this.showFormError('Please fill in all required fields correctly');
                return;
            }
            
            this.isSubmitting = true;
            this.submitError = null;
            this.serverErrors = {};
            
            const formData = new FormData();
            formData.append('name', this.productName);
            formData.append('category', this.category);
            formData.append('rating', this.rating);
            formData.append('stock', this.stock);
            formData.append('price', this.price);
            formData.append('description', this.description || '');
            
            if (this.imageFile) {
                formData.append('image', this.imageFile);
            }
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                const response = await fetch('/cashier/products', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    if (response.status === 422) {
                        this.serverErrors = data.errors || {};
                        this.showFormError('Please check the form for errors');
                    } else {
                        throw new Error(data.message || 'Failed to add product');
                    }
                    return;
                }
                
                this.submitSuccess = true;
                this.showNotification('Product added successfully!', 'success');
                
                const createdProduct = data.product;
                
                setTimeout(() => {
                    this.resetForm();
                    
                    // Close modal
                    const modal = document.querySelector('[x-data*="addProductForm"]');
                    if (modal) {
                        modal.dispatchEvent(new CustomEvent('close-modal'));
                    }
                    
                    // Dispatch product-added event
                    window.dispatchEvent(new CustomEvent('product-added', { 
                        detail: { 
                            product: createdProduct,
                            category: createdProduct?.category || this.category 
                        }
                    }));
                    
                    // Trigger refresh if productManager exists
                    if (window.productManager) {
                        window.productManager.refreshProducts();
                    }
                    
                }, 1500);
                
            } catch (error) {
                console.error('Error adding product:', error);
                this.submitError = error.message || 'An error occurred';
                this.showFormError(this.submitError);
            } finally {
                this.isSubmitting = false;
            }
        },
        
        showNotification(message, type) {
            // Try to use global notification system
            if (window.showNotification) {
                window.showNotification(message, type);
                return;
            }
            
            // Fallback to alert
            alert(message);
        },
        
        showFormError(message) {
            alert(message);
        },
        
        resetForm() {
            this.productName = '';
            this.category = '';
            this.rating = 'none';
            this.stock = '';
            this.price = '';
            this.description = '';
            this.imagePreview = null;
            this.imageFile = null;
            this.errors = {
                productName: false,
                category: false,
                rating: false,
                stock: false,
                price: false,
                image: false
            };
            this.touched = {
                productName: false,
                category: false,
                rating: false,
                stock: false,
                price: false
            };
            this.serverErrors = {};
            this.submitError = null;
            this.submitSuccess = false;
        },
        
        // Listen for image selection from gallery
        listenForImageSelection() {
            window.addEventListener('image-selected', (event) => {
                const selectedImage = event.detail;
                this.imagePreview = '/storage/' + selectedImage.path;
                this.imageFile = null;
                this.errors.image = false;
            });
        },
        
        // Initialize
        init() {
            this.listenForImageSelection();
        }
    }));
});
